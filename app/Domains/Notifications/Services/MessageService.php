<?php

namespace App\Domains\Notifications\Services;

use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Enums\MessageStatus;
use App\Domains\Notifications\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageService
{
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
        return Message::create([
            'sender_id' => $sender?->id,
            'receiver_id' => $receiver?->id,
            'customer_id' => $customerId,
            'channel' => $channel->value,
            'subject' => $subject,
            'body' => $body,
            'status' => MessageStatus::Pending,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'payload_json' => $payload,
        ]);
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
}
