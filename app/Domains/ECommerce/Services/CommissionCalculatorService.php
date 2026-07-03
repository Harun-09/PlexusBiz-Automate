<?php

namespace App\Domains\ECommerce\Services;

class CommissionCalculatorService
{
    /**
     * Calculate the platform commission fee for a given order amount.
     * Currently set to a flat 5% (0.05). In the future, this can be dynamic based on Supplier subscription/tier.
     */
    public function calculate(float $amount): float
    {
        return round($amount * 0.05, 2);
    }
}
