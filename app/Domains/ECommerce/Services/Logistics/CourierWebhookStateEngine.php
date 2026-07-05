<?php

namespace App\Domains\ECommerce\Services\Logistics;

use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Models\Order;

class CourierWebhookStateEngine
{
    /**
     * Map courier specific statuses to our internal OrderStatus enum
     */
    public function processWebhook(string $courier, array $payload): void
    {
        $consignmentId = $this->extractConsignmentId($courier, $payload);
        $status = $this->extractStatus($courier, $payload);
        
        $order = Order::where('consignment_id', $consignmentId)->first();
        if (!$order) {
            return;
        }

        $internalStatus = $this->mapStatus($courier, $status);
        if ($internalStatus) {
            $order->update(['status' => $internalStatus]);
            // Here we could also fire events like OrderDelivered, etc.
        }
    }

    protected function extractConsignmentId(string $courier, array $payload): ?string
    {
        if (strtolower($courier) === 'pathao') {
            return $payload['consignment_id'] ?? null;
        } elseif (strtolower($courier) === 'steadfast') {
            return $payload['consignment_id'] ?? null;
        }
        return null;
    }

    protected function extractStatus(string $courier, array $payload): ?string
    {
        if (strtolower($courier) === 'pathao') {
            return $payload['order_status'] ?? null;
        } elseif (strtolower($courier) === 'steadfast') {
            return $payload['status'] ?? null;
        }
        return null;
    }

    protected function mapStatus(string $courier, string $status): ?OrderStatus
    {
        $status = strtolower($status);
        
        if ($courier === 'pathao') {
            return match ($status) {
                'delivered' => OrderStatus::DELIVERED,
                'returned' => OrderStatus::RETURNED,
                'pickup_cancelled' => OrderStatus::CANCELLED,
                default => null,
            };
        } elseif ($courier === 'steadfast') {
            return match ($status) {
                'delivered' => OrderStatus::DELIVERED,
                'returned' => OrderStatus::RETURNED,
                'cancelled' => OrderStatus::CANCELLED,
                default => null,
            };
        }

        return null;
    }
}
