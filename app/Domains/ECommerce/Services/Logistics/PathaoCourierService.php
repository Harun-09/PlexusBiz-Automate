<?php

namespace App\Domains\ECommerce\Services\Logistics;

use Illuminate\Support\Facades\Http;

class PathaoCourierService
{
    /**
     * Calculate delivery price using Pathao API (Mocked)
     */
    public function calculatePrice(int $storeId, string $itemType, string $deliveryType, float $itemWeight, int $recipientCity, int $recipientZone): float
    {
        // REAL API LOGIC (Inactive to avoid costs)
        /*
        $response = Http::withToken(config('services.pathao.token'))->post('https://api-hermes.pathao.com/aladdin/api/v1/price-calculation', [
            'store_id' => $storeId,
            'item_type' => $itemType,
            'delivery_type' => $deliveryType,
            'item_weight' => $itemWeight,
            'recipient_city' => $recipientCity,
            'recipient_zone' => $recipientZone
        ]);
        return $response->json('data.price');
        */

        // MOCK LOGIC
        return $itemWeight > 2 ? 120.00 : 80.00; // Base inside city rate mocked
    }

    /**
     * Create Order in Pathao (Mocked)
     */
    public function createOrder(array $orderData): array
    {
        // REAL API LOGIC
        /*
        $response = Http::withToken(config('services.pathao.token'))->post('https://api-hermes.pathao.com/aladdin/api/v1/orders', $orderData);
        return $response->json();
        */

        // MOCK LOGIC
        return [
            'consignment_id' => 'PATHAO-' . strtoupper(uniqid()),
            'status' => 'success',
            'message' => 'Order created successfully in Pathao'
        ];
    }
}
