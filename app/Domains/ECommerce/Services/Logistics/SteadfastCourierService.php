<?php

namespace App\Domains\ECommerce\Services\Logistics;

use Illuminate\Support\Facades\Http;

class SteadfastCourierService
{
    /**
     * Calculate delivery price using Steadfast API (Mocked)
     */
    public function calculatePrice(float $weight): float
    {
        // REAL API LOGIC
        /*
        // Steadfast generally uses a flat rate or weight-based rate without origin dependency
        $response = Http::withHeaders(['Api-Key' => config('services.steadfast.key'), 'Secret-Key' => config('services.steadfast.secret')])
            ->get('https://portal.packzy.com/api/v1/get_delivery_charge'); // Note: Endpoint may vary
        // ... parse and calculate
        */

        // MOCK LOGIC
        return $weight > 2 ? 110.00 : 70.00; // Mocked inside city
    }

    /**
     * Create Order in Steadfast (Mocked)
     */
    public function createOrder(array $orderData): array
    {
        // REAL API LOGIC
        /*
        $response = Http::withHeaders([
            'Api-Key' => config('services.steadfast.key'),
            'Secret-Key' => config('services.steadfast.secret')
        ])->post('https://portal.packzy.com/api/v1/create_order', $orderData);
        
        return $response->json();
        */

        // MOCK LOGIC
        return [
            'consignment_id' => 'STEADFAST-' . strtoupper(uniqid()),
            'status' => 'success',
            'message' => 'Order created successfully in Steadfast'
        ];
    }
}
