<?php

namespace App\Domains\HCM\Services;

use App\Domains\HCM\Models\Employee;

class TaxCalculator
{
    /**
     * NBR Progressive Tax Slabs (2024-2025 assessment year standard)
     */
    protected array $slabs = [
        ['limit' => 300000, 'rate' => 0.10], // Next 3,00,000 at 10%
        ['limit' => 400000, 'rate' => 0.15], // Next 4,00,000 at 15%
        ['limit' => 500000, 'rate' => 0.20], // Next 5,00,000 at 20%
        ['limit' => 500000, 'rate' => 0.25], // Next 5,00,000 at 25%
        ['limit' => INF,    'rate' => 0.30], // Remaining at 30%
    ];

    /**
     * Calculate the annual tax liability for an employee based on their annual taxable income.
     * Minimum tax in city corporation areas is usually BDT 5,000 if taxable income exceeds the exemption.
     *
     * @param float $annualTaxableIncome
     * @param Employee $employee
     * @return float
     */
    public function calculateAnnualTax(float $annualTaxableIncome, Employee $employee): float
    {
        $exemptionLimit = $this->getExemptionLimit($employee);
        
        if ($annualTaxableIncome <= $exemptionLimit) {
            return 0.0;
        }

        $taxableAmount = $annualTaxableIncome - $exemptionLimit;
        $totalTax = 0.0;

        foreach ($this->slabs as $slab) {
            if ($taxableAmount <= 0) {
                break;
            }

            $amountInSlab = min($taxableAmount, $slab['limit']);
            $totalTax += $amountInSlab * $slab['rate'];
            
            $taxableAmount -= $amountInSlab;
        }

        // Apply minimum tax rule if tax is calculated to be > 0 but < 5000
        if ($totalTax > 0 && $totalTax < 5000) {
            $totalTax = 5000;
        }

        return $totalTax;
    }

    /**
     * Get the NBR Tax exemption limit based on employee demographics.
     */
    protected function getExemptionLimit(Employee $employee): float
    {
        if ($employee->is_freedom_fighter) {
            return 500000.0; // 5,00,000 for freedom fighters
        }
        if ($employee->is_physically_challenged) {
            return 500000.0; // 5,00,000 for physically challenged
        }
        if ($employee->is_female_or_senior) {
            return 400000.0; // 4,00,000 for women and seniors (65+ years)
        }

        return 350000.0; // 3,50,000 general exemption limit
    }
}
