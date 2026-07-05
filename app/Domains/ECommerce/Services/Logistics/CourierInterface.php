<?php

namespace App\Domains\ECommerce\Services\Logistics;

interface CourierInterface
{
    /**
     * Create a shipment and get a tracking number
     */
    public function createShipment(array $orderData): array;

    /**
     * Get the status of a shipment
     */
    public function trackShipment(string $trackingNumber): array;
}
