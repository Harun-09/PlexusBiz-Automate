<?php

namespace App\Domains\ECommerce\Events;

use App\Domains\ECommerce\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Order $order)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function payloadSnapshot(): array
    {
        $this->order->loadMissing(['buyer', 'items', 'invoice']);

        return [
            'order' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'status' => $this->order->status->value,
                'subtotal' => $this->order->subtotal,
                'grand_total' => $this->order->grand_total,
                'currency' => $this->order->currency,
                'placed_at' => $this->order->placed_at?->toIso8601String(),
            ],
            'buyer' => [
                'id' => $this->order->buyer?->id,
                'name' => $this->order->buyer?->name,
                'email' => $this->order->buyer?->email,
                'phone' => $this->order->buyer?->phone,
            ],
            'items' => $this->order->items->map(fn ($item): array => [
                'product_id' => $item->product_id,
                'supplier_id' => $item->supplier_id,
                'sku' => $item->sku,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ])->all(),
            'invoice' => [
                'id' => $this->order->invoice?->id,
                'invoice_number' => $this->order->invoice?->invoice_number,
                'total' => $this->order->invoice?->total,
            ],
        ];
    }
}
