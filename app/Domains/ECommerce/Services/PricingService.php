<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Models\Product;
use Illuminate\Validation\ValidationException;

class PricingService
{
    public function unitPrice(Product $product, int $quantity): string
    {
        if ($quantity < $product->moq) {
            throw ValidationException::withMessages([
                'quantity' => sprintf('Minimum order quantity for %s is %d.', $product->name, $product->moq),
            ]);
        }

        $tier = $product->pricingTiers()
            ->where('min_quantity', '<=', $quantity)
            ->orderByDesc('min_quantity')
            ->first();

        return number_format((float) ($tier?->unit_price ?? $product->base_price), 2, '.', '');
    }

    public function lineTotal(Product $product, int $quantity): string
    {
        return number_format((float) $this->unitPrice($product, $quantity) * $quantity, 2, '.', '');
    }
}
