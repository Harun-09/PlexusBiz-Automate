<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'subject' => $this->subject,
            'description' => $this->description,
            'channel' => $this->channel->value,
            'priority' => $this->priority->value,
            'status' => $this->status->value,
            'tags' => $this->tags_json ?? [],
            'metadata' => $this->metadata_json ?? [],
            'last_message_at' => $this->last_message_at?->toJSON(),
            'requester' => $this->whenLoaded('requester', fn (): ?array => $this->requester ? [
                'id' => $this->requester->id,
                'name' => $this->requester->name,
                'email' => $this->requester->email,
            ] : null),
            'supplier' => $this->whenLoaded('supplier', fn (): ?array => $this->supplier ? [
                'id' => $this->supplier->id,
                'company_name' => $this->supplier->company_name,
            ] : null),
            'messages' => $this->whenLoaded('messages', fn () => $this->messages
                ->sortBy('created_at')
                ->map(fn ($message): array => [
                    'id' => $message->id,
                    'sender_type' => $message->sender_type->value,
                    'visibility' => $message->visibility->value,
                    'message' => $message->message,
                    'payload' => $message->payload_json ?? [],
                    'created_at' => $message->created_at?->toJSON(),
                ])
                ->values()),
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}
