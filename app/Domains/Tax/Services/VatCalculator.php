<?php

namespace App\Domains\Tax\Services;

use App\Domains\ECommerce\Models\Order;
use App\Domains\Tax\Models\TaxConfiguration;

class VatCalculator
{
    /**
     * Calculate the total VAT for an order based on TaxConfiguration.
     * We look up the tax rate by category. If not found, fallback to region default.
     *
     * @param Order $order
     * @return float
     */
    public function calculateTotalVatForOrder(Order $order): float
    {
        $totalVat = 0.0;
        
        $region = 'BD'; // Can be fetched from order's shipping address in a real system

        // Preload default rate for the region
        $defaultConfig = TaxConfiguration::where('region', $region)
            ->whereNull('category_id')
            ->where('is_active', true)
            ->first();
            
        $defaultRate = $defaultConfig ? $defaultConfig->tax_rate : 15.00; // NBR standard 15%

        foreach ($order->items as $item) {
            $product = $item->product;
            $rate = $defaultRate;

            if ($product && $product->category_id) {
                // Find category specific rate
                $catConfig = TaxConfiguration::where('region', $region)
                    ->where('category_id', $product->category_id)
                    ->where('is_active', true)
                    ->first();
                    
                if ($catConfig) {
                    $rate = $catConfig->tax_rate;
                }
            }

            $lineTotal = $item->unit_price * $item->quantity;
            $vatAmount = $lineTotal * ($rate / 100);
            
            $totalVat += $vatAmount;
        }

        return round($totalVat, 2);
    }
}
