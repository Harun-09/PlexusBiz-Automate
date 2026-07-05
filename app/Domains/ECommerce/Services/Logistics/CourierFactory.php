<?php

namespace App\Domains\ECommerce\Services\Logistics;

class CourierFactory
{
    /**
     * Resolve the appropriate courier service based on the provided name.
     */
    public static function make(string $courierName): CourierInterface
    {
        switch (strtolower($courierName)) {
            case 'pathao':
                return new PathaoCourierService();
            case 'redx':
                return new RedXCourierService();
            // Fallback to Steadfast or others later
            default:
                throw new \InvalidArgumentException("Unsupported courier service: {$courierName}");
        }
    }
}
