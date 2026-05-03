<?php

namespace App\Domains\Support\Events;

use App\Domains\Support\Models\SupportTicket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportTicketCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public SupportTicket $ticket)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function payloadSnapshot(): array
    {
        $this->ticket->loadMissing(['requester', 'supplier.user', 'order', 'customer', 'messages', 'supplierNotifications']);

        return [
            'ticket' => [
                'id' => $this->ticket->id,
                'number' => $this->ticket->ticket_number,
                'subject' => $this->ticket->subject,
                'description' => $this->ticket->description,
                'status' => $this->ticket->status->value,
                'priority' => $this->ticket->priority->value,
                'channel' => $this->ticket->channel->value,
                'tags' => $this->ticket->tags_json ?? [],
                'metadata' => $this->ticket->metadata_json ?? [],
            ],
            'requester' => [
                'id' => $this->ticket->requester?->id,
                'name' => $this->ticket->requester?->name,
                'email' => $this->ticket->requester?->email,
            ],
            'supplier' => [
                'id' => $this->ticket->supplier?->id,
                'company_name' => $this->ticket->supplier?->company_name,
                'email' => $this->ticket->supplier?->contact_email,
            ],
            'order' => [
                'id' => $this->ticket->order?->id,
                'number' => $this->ticket->order?->order_number,
            ],
            'customer' => [
                'id' => $this->ticket->customer?->id,
                'email' => $this->ticket->customer?->email,
            ],
            'messages' => $this->ticket->messages
                ->sortBy('created_at')
                ->map(fn ($message): array => [
                    'id' => $message->id,
                    'sender_type' => $message->sender_type->value,
                    'visibility' => $message->visibility->value,
                    'message' => $message->message,
                    'payload' => $message->payload_json ?? [],
                ])
                ->values()
                ->all(),
            'supplier_notifications' => $this->ticket->supplierNotifications
                ->map(fn ($notification): array => [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'payload' => $notification->payload_json ?? [],
                ])
                ->values()
                ->all(),
        ];
    }
}
