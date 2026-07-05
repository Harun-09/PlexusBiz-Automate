<?php

namespace App\Domains\ECommerce\Services\Logistics;

use Illuminate\Support\Facades\Http;

class PathaoCourierService implements CourierInterface
{
    /**
     * Calculate delivery price using Pathao API (Mocked)
     */
    public function calculatePrice(int $storeId, string $itemType, string $deliveryType, float $itemWeight, int $recipientCity, int $recipientZone): float
    {
        // MOCK LOGIC
        return $itemWeight > 2 ? 120.00 : 80.00; // Base inside city rate mocked
    }

    public function createShipment(array $orderData): array
    {
        // MOCK LOGIC
        return [
            'consignment_id' => 'PATHAO-' . strtoupper(uniqid()),
            'tracking_number' => 'PTH' . strtoupper(\Illuminate\Support\Str::random(8)),
            'status' => 'success',
            'message' => 'Order created successfully in Pathao',
            'courier' => 'Pathao',
        ];
    }

    public function trackShipment(string $trackingNumber): array
    {
        return [
            'tracking_number' => $trackingNumber,
            'status' => 'In Transit',
            'location' => 'Dhaka Hub',
        ];
    }
}
