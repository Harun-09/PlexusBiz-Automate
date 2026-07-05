<?php

namespace App\Domains\ECommerce\Services\Logistics;

use Exception;

class LogisticsBrokerService
{
    protected PathaoCourierService $pathao;
    protected SteadfastCourierService $steadfast;

    public function __construct(PathaoCourierService $pathao, SteadfastCourierService $steadfast)
    {
        $this->pathao = $pathao;
        $this->steadfast = $steadfast;
    }

    /**
     * Determine best courier based on rules/preferences and create order
     */
    public function dispatchOrder(array $orderData, string $preferredCourier = null): array
    {
        $courier = $preferredCourier ?? $this->determineBestCourier($orderData);

        switch (strtolower($courier)) {
            case 'pathao':
                $response = $this->pathao->createOrder($orderData);
                $response['courier_used'] = 'Pathao';
                break;
            case 'steadfast':
                $response = $this->steadfast->createOrder($orderData);
                $response['courier_used'] = 'Steadfast';
                break;
            default:
                throw new Exception("Unsupported courier: {$courier}");
        }

        return $response;
    }

    /**
     * Logic to determine the best courier
     */
    protected function determineBestCourier(array $orderData): string
    {
        // Example Rule: if recipient is in a specific city, prefer Steadfast, else Pathao.
        // For now, randomly mock or default to Pathao.
        $weight = $orderData['item_weight'] ?? 1.0;
        
        // If it's a heavy package, maybe Steadfast is cheaper (mock rule)
        if ($weight > 5.0) {
            return 'Steadfast';
        }

        return 'Pathao';
    }
}
