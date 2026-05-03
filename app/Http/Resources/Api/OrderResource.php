<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status->value,
            'currency' => $this->currency,
            'subtotal' => $this->subtotal,
            'tax_total' => $this->tax_total,
            'shipping_total' => $this->shipping_total,
            'discount_total' => $this->discount_total,
            'grand_total' => $this->grand_total,
            'placed_at' => $this->placed_at?->toJSON(),
            'buyer' => $this->whenLoaded('buyer', fn (): array => [
                'id' => $this->buyer?->id,
                'name' => $this->buyer?->name,
                'email' => $this->buyer?->email,
            ]),
            'customer' => $this->whenLoaded('customer', fn (): ?array => $this->customer ? [
                'id' => $this->customer->id,
                'company_name' => $this->customer->company_name,
                'email' => $this->customer->email,
            ] : null),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item): array => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'supplier_id' => $item->supplier_id,
                'product_name' => $item->product_name,
                'sku' => $item->sku,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
                'status' => $item->status,
            ])->values()),
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}
