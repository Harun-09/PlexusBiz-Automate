<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Enums\CartStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Product;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(
        private readonly InventoryService $inventory,
        private readonly PricingService $pricing,
    ) {
    }

    public function currentFor(User $user): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => CartStatus::Active->value],
            ['expires_at' => now()->addDays(14)],
        );
    }

    public function addItem(User $user, Product $product, int $quantity): Cart
    {
        if ($product->status !== ProductStatus::Active) {
            throw ValidationException::withMessages([
                'product' => sprintf('%s is not available for checkout.', $product->name),
            ]);
        }

        $cart = $this->currentFor($user);
        $existing = $cart->items()->where('product_id', $product->id)->first();
        $targetQuantity = $existing ? $existing->quantity + $quantity : $quantity;

        $this->inventory->assertAvailable($product, $targetQuantity);
        $unitPrice = $this->pricing->unitPrice($product, $targetQuantity);

        $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'supplier_id' => $product->supplier_id,
                'quantity' => $targetQuantity,
                'unit_price' => $unitPrice,
            ],
        );

        return $cart->fresh(['items.product']);
    }

    /**
     * @return array{subtotal: string, items_count: int}
     */
    public function summary(Cart $cart): array
    {
        $cart->loadMissing('items');

        $subtotal = $cart->items->sum(fn ($item): float => (float) $item->unit_price * $item->quantity);

        return [
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'items_count' => $cart->items->sum('quantity'),
        ];
    }
}
