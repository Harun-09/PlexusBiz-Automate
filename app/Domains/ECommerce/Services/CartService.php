<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Enums\CartStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\CartItem;
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

        $cart->touch();

        return $cart->fresh(['items.product']);
    }

    public function updateItem(CartItem $item, int $quantity): Cart
    {
        $product = $item->product()->with(['pricingTiers', 'supplier', 'images'])->firstOrFail();

        if ($product->status !== ProductStatus::Active) {
            throw ValidationException::withMessages([
                'product' => sprintf('%s is not available for checkout.', $product->name),
            ]);
        }

        $this->inventory->assertAvailable($product, $quantity);
        $unitPrice = $this->pricing->unitPrice($product, $quantity);

        $item->forceFill([
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ])->save();

        $item->cart()->first()?->touch();

        return $item->cart()->with(['items.product', 'items.supplier'])->firstOrFail();
    }

    public function removeItem(CartItem $item): Cart
    {
        $cart = $item->cart;
        $item->delete();
        $cart->touch();

        return $cart->fresh(['items.product', 'items.supplier']);
    }

    /**
     * @return array{subtotal: string, items_count: int}
     */
    public function summary(Cart $cart): array
    {
        $totals = $this->totals($cart);

        return [
            'subtotal' => $totals['subtotal'],
            'items_count' => $totals['items_count'],
        ];
    }

    /**
     * @return array{subtotal:string,tax_total:string,shipping_total:string,discount_total:string,grand_total:string,items_count:int}
     */
    public function totals(Cart $cart): array
    {
        $cart->loadMissing('items');

        $subtotal = $cart->items->sum(fn ($item): float => (float) $item->unit_price * $item->quantity);

        return [
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => number_format($subtotal, 2, '.', ''),
            'items_count' => $cart->items->sum('quantity'),
        ];
    }
}
