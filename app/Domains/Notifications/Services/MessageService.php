<?php

namespace App\Domains\Notifications\Services;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Services\InteractionLogger;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Enums\MessageStatus;
use App\Domains\Notifications\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class MessageService
{
    public function __construct(
        private readonly InteractionLogger $interactions,
    ) {
    }

    public function send(
        ?User $sender,
        ?User $receiver,
        string $body,
        ?string $subject = null,
        MessageChannel $channel = MessageChannel::System,
        ?int $customerId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?array $payload = null,
    ): Message {
        [$customer, $direction] = $this->resolveMessageContext($customerId, $sender, $receiver);

        $message = Message::create([
            'sender_id' => $sender?->id,
            'receiver_id' => $receiver?->id,
            'customer_id' => $customer?->id,
            'channel' => $channel->value,
            'subject' => $subject,
            'body' => $body,
            'status' => MessageStatus::Pending,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'payload_json' => $payload,
        ]);

        if ($customer) {
            $this->interactions->record(
                customer: $customer,
                type: InteractionType::Message,
                summary: $this->summaryForMessage($subject, $body),
                related: $message,
                payload: [
                    'message_id' => $message->id,
                    'channel' => $channel->value,
                    'subject' => $subject,
                    'receiver_id' => $receiver?->id,
                    'sender_id' => $sender?->id,
                    'customer_id' => $customer->id,
                ],
                actor: $sender,
                direction: $direction,
            );
        }

        return $message;
    }

    public function sendToUser(User $receiver, string $subject, string $body, ?User $sender = null): Message
    {
        return $this->send(
            sender: $sender,
            receiver: $receiver,
            body: $body,
            subject: $subject,
            channel: MessageChannel::System,
        );
    }

    public function sendToCustomer(int $customerId, string $subject, string $body, ?User $sender = null): Message
    {
        return $this->send(
            sender: $sender,
            receiver: null,
            body: $body,
            subject: $subject,
            channel: MessageChannel::System,
            customerId: $customerId,
        );
    }

    public function markAsRead(int $messageId, int $userId): ?Message
    {
        $message = Message::where('id', $messageId)
            ->where('receiver_id', $userId)
            ->first();

        if (! $message) {
            return null;
        }

        $message->markAsRead();

        return $message;
    }

    public function markAllAsRead(int $userId): int
    {
        return Message::forUser($userId)
            ->unread()
            ->update([
                'status' => MessageStatus::Read->value,
                'read_at' => now(),
            ]);
    }

    public function getUnreadCount(int $userId): int
    {
        return Message::forUser($userId)->unread()->count();
    }

    public function getInbox(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Message::forUser($userId)
            ->with(['sender'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getSent(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Message::where('sender_id', $userId)
            ->with(['receiver'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getRecentForUser(int $userId, int $limit = 5): Collection
    {
        return Message::forUser($userId)
            ->with(['sender'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array{0: ?Customer, 1: string}
     */
    private function resolveMessageContext(?int $customerId, ?User $sender, ?User $receiver): array
    {
        if (is_numeric($customerId) && (int) $customerId > 0) {
            $customer = Customer::buyerAccounts()->find((int) $customerId);

            if ($customer) {
                $direction = $sender && (int) $sender->id === (int) $customer->user_id ? 'inbound' : 'outbound';

                return [$customer, $direction];
            }
        }

        if ($receiver) {
            $customer = Customer::buyerAccounts()->where('user_id', $receiver->id)->first();

            if ($customer) {
                return [$customer, 'outbound'];
            }
        }

        if ($sender) {
            $customer = Customer::buyerAccounts()->where('user_id', $sender->id)->first();

            if ($customer) {
                return [$customer, 'inbound'];
            }
        }

        return [null, 'outbound'];
    }

    private function summaryForMessage(?string $subject, string $body): string
    {
        $subject = trim((string) $subject);

        if ($subject !== '') {
            return 'Message: '.$subject;
        }

        $snippet = Str::limit(trim(strip_tags($body)), 120);

        return $snippet !== '' ? 'Message: '.$snippet : 'Message sent';
    }
}
