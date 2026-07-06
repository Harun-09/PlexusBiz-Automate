<?php

namespace App\Domains\Tax\Services;

class VDSEngine
{
    /**
     * Statutory VDS Deduction Rates for Specified Services (FY 2025-2026)
     */
    protected array $vdsRates = [
        'S001.10' => 15.00, // AC Hotel Accommodations
        'S014.00' => 15.00, // Indenting Firm Services
        'S028.00' => 15.00, // Courier and Express Mail Service
        'S032.00' => 15.00, // Advisory / Consultancy / Professional Services
        'S049.00' => 15.00, // Rent-a-Car Services
        'S065.00' => 15.00, // Cleaning or Maintenance Services
    ];

    /**
     * Calculate VAT Deduction at Source (VDS) based on service code and base amount.
     */
    public function calculateVDS(string $serviceCode, float $baseAmount): float
    {
        if (!isset($this->vdsRates[$serviceCode])) {
            return 0.00;
        }

        $rate = $this->vdsRates[$serviceCode];
        return round(($baseAmount * $rate) / 100, 2);
    }
    
    /**
     * Determine if a service requires VDS withholding.
     */
    public function requiresVDS(string $serviceCode): bool
    {
        return isset($this->vdsRates[$serviceCode]);
    }
}
