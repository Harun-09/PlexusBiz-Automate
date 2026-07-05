<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Models\Product;
use App\Models\User;

class DynamicPricingEngine
{
    /**
     * Calculate dynamic discount based on rules
     */
    public function calculateDiscount(Product $product, int $quantity, ?User $buyer): float
    {
        $basePrice = (float) $product->base_price;
        $totalPrice = $basePrice * $quantity;
        $discountAmount = 0.0;

        // Rule 1: Volume Discount
        if ($quantity >= 50) {
            $discountAmount += $totalPrice * 0.10; // 10% off for 50+ items
        } elseif ($quantity >= 20) {
            $discountAmount += $totalPrice * 0.05; // 5% off for 20+ items
        }

        // Rule 2: Temporal Discount (e.g. Flash Sale mock)
        $hour = (int) date('H');
        if ($hour >= 14 && $hour <= 16) {
            // Happy hour discount 2%
            $discountAmount += $totalPrice * 0.02;
        }

        // Rule 3: User State Discount (VIP buyers)
        if ($buyer && $this->isVipBuyer($buyer)) {
            $discountAmount += $totalPrice * 0.05; // Extra 5% for VIP
        }

        return min($discountAmount, $totalPrice * 0.50); // Max 50% discount cap
    }

    /**
     * Determine if buyer is VIP
     */
    protected function isVipBuyer(User $buyer): bool
    {
        // Mock VIP logic
        return $buyer->id % 10 === 0; // Mock: every 10th user is VIP
    }
}
