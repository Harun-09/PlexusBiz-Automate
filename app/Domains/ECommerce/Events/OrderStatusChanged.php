<?php

namespace App\Domains\ECommerce\Events;

use App\Domains\ECommerce\Models\SupplierOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly SupplierOrder $supplierOrder,
        public readonly string $fromStatus,
        public readonly string $toStatus,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function payloadSnapshot(): array
    {
        $this->supplierOrder->loadMissing(['order.items', 'order.buyer', 'supplier.user']);

        $order = $this->supplierOrder->order;

        return [
            'order' => [
                'id' => $order?->id,
                'order_number' => $order?->order_number,
                'status' => $this->toStatus,
                'previous_status' => $this->fromStatus,
                'subtotal' => $order?->subtotal,
                'grand_total' => $order?->grand_total,
                'currency' => $order?->currency,
                'placed_at' => $order?->placed_at?->toIso8601String(),
            ],
            'supplier_order' => [
                'id' => $this->supplierOrder->id,
                'supplier_order_number' => $this->supplierOrder->supplier_order_number,
                'status' => $this->supplierOrder->status->value,
                'subtotal' => $this->supplierOrder->subtotal,
                'grand_total' => $this->supplierOrder->grand_total,
                'currency' => $this->supplierOrder->currency,
            ],
            'buyer' => [
                'id' => $order?->buyer?->id,
                'name' => $order?->buyer?->name,
                'email' => $order?->buyer?->email,
                'phone' => $order?->buyer?->phone,
            ],
            'supplier' => [
                'id' => $this->supplierOrder->supplier?->id,
                'company_name' => $this->supplierOrder->supplier?->company_name,
                'email' => $this->supplierOrder->supplier?->contact_email,
            ],
            'items' => $order?->items
                ?->map(fn ($item): array => [
                    'product_id' => $item->product_id,
                    'supplier_id' => $item->supplier_id,
                    'sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ])
                ->all() ?? [],
        ];
    }
}
