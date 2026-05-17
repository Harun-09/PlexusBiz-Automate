<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RfqResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rfq_number' => $this->rfq_number,
            'status' => $this->status->value,
            'message' => $this->message,
            'needed_by' => $this->needed_by?->toJSON(),
            'buyer' => $this->whenLoaded('buyer', fn (): ?array => $this->buyer ? [
                'id' => $this->buyer->id,
                'name' => $this->buyer->name,
                'email' => $this->buyer->email,
            ] : null),
            'supplier' => $this->whenLoaded('supplier', fn (): ?array => $this->supplier ? [
                'id' => $this->supplier->id,
                'company_name' => $this->supplier->company_name,
                'slug' => $this->supplier->slug,
            ] : null),
            'items' => $this->whenLoaded('items', fn (): array => $this->items
                ->map(fn ($item): array => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'target_price' => $item->target_price,
                ])
                ->values()
                ->all()),
            'responses_count' => $this->whenCounted('responses'),
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}

