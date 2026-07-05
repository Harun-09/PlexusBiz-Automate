<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\RfqStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\OrderItem;
use App\Domains\ECommerce\Models\Rfq;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RfqConversionService
{
    /**
     * Convert an approved RFQ to an Order.
     */
    public function convert(Rfq $rfq, string $paymentTerm = 'net30'): ?Order
    {
        if ($rfq->status !== RfqStatus::Accepted) {
            throw new \Exception("Only accepted RFQs can be converted to orders.");
        }

        return DB::transaction(function () use ($rfq, $paymentTerm) {
            $grandTotal = $rfq->items->sum(function ($item) {
                return $item->target_price * $item->quantity;
            });

            $order = Order::create([
                'user_id' => $rfq->buyer_id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => OrderStatus::Pending,
                'payment_term' => $paymentTerm,
                'subtotal' => $grandTotal,
                'tax_total' => 0,
                'shipping_total' => 0,
                'grand_total' => $grandTotal,
                'currency' => 'BDT',
                'placed_at' => now(),
            ]);

            foreach ($rfq->items as $rfqItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $rfqItem->product_id,
                    'quantity' => $rfqItem->quantity,
                    'unit_price' => $rfqItem->target_price,
                    'subtotal' => $rfqItem->target_price * $rfqItem->quantity,
                ]);
            }

            $rfq->update(['status' => RfqStatus::Converted]);

            return $order;
        });
    }
}
