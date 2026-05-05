<?php

namespace App\Domains\Support\Services;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Services\InteractionLogger;
use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Enums\SupportMessageSenderType;
use App\Domains\Support\Enums\SupportMessageVisibility;
use App\Domains\Support\Enums\TicketPriority;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Events\SupportTicketCreated;
use App\Domains\ECommerce\Models\Order;
use App\Domains\Support\Models\SupportMessage;
use App\Domains\Support\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportTicketService
{
    public function __construct(
        private readonly SupportTicketNumberService $numbers,
        private readonly FaqMatcher $faqs,
        private readonly SupplierNotificationService $supplierNotifications,
        private readonly InteractionLogger $interactions,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createTicket(User $requester, array $data, SupportChannel $channel = SupportChannel::Web): SupportTicket
    {
        $customerId = $this->resolveCustomerId($requester, $data);

        $ticket = DB::transaction(function () use ($requester, $data, $channel, $customerId): SupportTicket {
            $priority = TicketPriority::tryFrom((string) ($data['priority'] ?? TicketPriority::Normal->value)) ?? TicketPriority::Normal;

            $ticket = SupportTicket::create([
                'ticket_number' => $this->numbers->next(),
                'requester_id' => $requester->id,
                'supplier_id' => $data['supplier_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'customer_id' => $customerId,
                'channel' => $channel,
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $priority,
                'status' => TicketStatus::Open,
                'tags_json' => $data['tags'] ?? [],
                'metadata_json' => $data['metadata'] ?? [],
                'last_message_at' => now(),
            ]);

            $this->addMessage(
                ticket: $ticket,
                sender: $requester,
                senderType: $this->senderTypeFor($requester),
                message: $data['description'],
            );

            $this->addAutoReply($ticket);

            if ($ticket->supplier_id !== null) {
                $ticket->forceFill(['status' => TicketStatus::WaitingSupplier])->save();
                $this->supplierNotifications->notifyTicketCreated($ticket->refresh());
            }

            return $ticket->refresh()->load(['requester', 'supplier.user', 'order', 'customer', 'messages.sender', 'supplierNotifications', 'assignee']);
        });

        event(new SupportTicketCreated($ticket));

        return $ticket;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function replyTicket(SupportTicket $ticket, User $sender, array $data): SupportTicket
    {
        $ticket = DB::transaction(function () use ($ticket, $sender, $data): SupportTicket {
            $message = $this->addMessage(
                ticket: $ticket,
                sender: $sender,
                senderType: $this->senderTypeFor($sender),
                message: $data['message'],
                visibility: SupportMessageVisibility::Public,
                payload: [
                    'source' => 'support_reply',
                ],
            );

            $status = match ($this->senderTypeFor($sender)) {
                SupportMessageSenderType::Supplier => TicketStatus::WaitingSupplier,
                SupportMessageSenderType::Buyer, SupportMessageSenderType::Customer => TicketStatus::Open,
                default => TicketStatus::Pending,
            };

            $ticket->forceFill([
                'status' => $status,
                'resolved_at' => in_array($status, [TicketStatus::Resolved, TicketStatus::Closed], true) ? now() : null,
            ])->save();

            return $ticket->refresh()->load(['requester', 'supplier.user', 'order', 'customer', 'messages.sender', 'supplierNotifications', 'assignee']);
        });

        return $ticket;
    }

    public function updateStatus(SupportTicket $ticket, TicketStatus $status): SupportTicket
    {
        $ticket->forceFill([
            'status' => $status,
            'resolved_at' => in_array($status, [TicketStatus::Resolved, TicketStatus::Closed], true) ? now() : null,
        ])->save();

        return $ticket->refresh()->load(['requester', 'supplier.user', 'order', 'customer', 'messages.sender', 'supplierNotifications', 'assignee']);
    }

    public function assignTicket(SupportTicket $ticket, ?User $assignee): SupportTicket
    {
        $ticket->forceFill([
            'assigned_to' => $assignee?->id,
        ])->save();

        return $ticket->refresh()->load(['requester', 'supplier.user', 'order', 'customer', 'messages', 'supplierNotifications', 'assignee']);
    }

    public function addMessage(
        SupportTicket $ticket,
        ?User $sender,
        SupportMessageSenderType $senderType,
        string $message,
        SupportMessageVisibility $visibility = SupportMessageVisibility::Public,
        array $payload = [],
    ): SupportMessage {
        $supportMessage = $ticket->messages()->create([
            'sender_id' => $sender?->id,
            'sender_type' => $senderType,
            'visibility' => $visibility,
            'message' => $message,
            'payload_json' => $payload,
        ]);

        $ticket->forceFill(['last_message_at' => now()])->save();

        $customer = $this->resolveCustomerForTicket($ticket, $sender);

        if ($customer) {
            $this->interactions->record(
                customer: $customer,
                type: InteractionType::SupportTicket,
                summary: $this->supportSummary($ticket, $message, $senderType),
                related: $ticket,
                payload: [
                    'support_ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'support_message_id' => $supportMessage->id,
                    'sender_type' => $senderType->value,
                    'visibility' => $visibility->value,
                    'message_excerpt' => Str::limit(trim(strip_tags($message)), 200),
                    'supplier_id' => $ticket->supplier_id,
                    'order_id' => $ticket->order_id,
                    'customer_id' => $customer->id,
                ],
                actor: $sender,
                direction: $this->directionForSenderType($senderType),
            );
        }

        return $supportMessage;
    }

    private function addAutoReply(SupportTicket $ticket): ?SupportMessage
    {
        $match = $this->faqs->match($ticket->subject.' '.$ticket->description);

        if ($match === null) {
            return null;
        }

        $message = $this->addMessage(
            ticket: $ticket,
            sender: null,
            senderType: SupportMessageSenderType::Chatbot,
            message: $match->faq->answer,
            payload: [
                'source' => 'support_faq',
                'faq_id' => $match->faq->id,
                'confidence' => $match->confidence,
                'matched_keywords' => $match->matchedKeywords,
            ],
        );

        $ticket->forceFill(['status' => TicketStatus::Pending])->save();

        return $message;
    }

    private function senderTypeFor(User $user): SupportMessageSenderType
    {
        if ($user->hasRole('supplier')) {
            return SupportMessageSenderType::Supplier;
        }

        if ($user->hasRole('admin') || $user->hasRole('marketing_manager')) {
            return SupportMessageSenderType::Agent;
        }

        return SupportMessageSenderType::Buyer;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function resolveCustomerId(User $requester, array $data): ?int
    {
        $customerId = (int) ($data['customer_id'] ?? 0);

        if ($customerId > 0 && Customer::buyerAccounts()->whereKey($customerId)->exists()) {
            return $customerId;
        }

        $orderId = (int) ($data['order_id'] ?? 0);

        if ($orderId > 0) {
            $orderCustomerId = Order::query()->whereKey($orderId)->value('customer_id');

            if (is_numeric($orderCustomerId) && (int) $orderCustomerId > 0 && Customer::buyerAccounts()->whereKey((int) $orderCustomerId)->exists()) {
                return (int) $orderCustomerId;
            }
        }

        return Customer::buyerAccounts()->where('user_id', $requester->id)->value('id') ?: null;
    }

    private function resolveCustomerForTicket(SupportTicket $ticket, ?User $sender = null): ?Customer
    {
        $ticket->loadMissing(['customer', 'order.customer', 'requester']);

        if ($ticket->customer && Customer::buyerAccounts()->whereKey($ticket->customer->getKey())->exists()) {
            return $ticket->customer;
        }

        if ($ticket->order?->customer && Customer::buyerAccounts()->whereKey($ticket->order->customer->getKey())->exists()) {
            return $ticket->order->customer;
        }

        if ($sender) {
            $customer = Customer::buyerAccounts()->where('user_id', $sender->id)->first();

            if ($customer) {
                return $customer;
            }
        }

        if ($ticket->requester) {
            return Customer::buyerAccounts()->where('user_id', $ticket->requester->id)->first();
        }

        return null;
    }

    private function directionForSenderType(SupportMessageSenderType $senderType): string
    {
        return match ($senderType) {
            SupportMessageSenderType::Buyer,
            SupportMessageSenderType::Customer => 'inbound',
            SupportMessageSenderType::Chatbot,
            SupportMessageSenderType::Automation => 'internal',
            default => 'outbound',
        };
    }

    private function supportSummary(SupportTicket $ticket, string $message, SupportMessageSenderType $senderType): string
    {
        $label = match ($senderType) {
            SupportMessageSenderType::Buyer,
            SupportMessageSenderType::Customer => 'Support request',
            SupportMessageSenderType::Supplier => 'Supplier support reply',
            SupportMessageSenderType::Chatbot => 'Automated support reply',
            default => 'Support message',
        };

        $snippet = Str::limit(trim(strip_tags($message)), 120);

        return sprintf('%s on %s: %s', $label, $ticket->ticket_number, $snippet !== '' ? $snippet : 'No message');
    }
}
