<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Enums\CartStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\CartItem;
use App\Domains\ECommerce\Models\Product;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\User;
use App\Models\B2CCustomer;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(
        private readonly InventoryService $inventory,
        private readonly PricingService $pricing,
    ) {
    }

    public function currentFor(Authenticatable $user): Cart
    {
        $column = $user instanceof B2CCustomer ? 'b2c_customer_id' : 'user_id';

        return Cart::firstOrCreate(
            [$column => $user->id, 'status' => CartStatus::Active->value],
            ['expires_at' => now()->addDays(14)],
        );
    }

    public function addItem(Authenticatable $user, Product $product, int $quantity): Cart
    {
        if ($product->status !== ProductStatus::Active) {
            throw ValidationException::withMessages([
                'product' => sprintf('%s is not available for checkout.', $product->name),
            ]);
        }

        $cart = $this->currentFor($user);
        $existing = $cart->items()->where('product_id', $product->id)->first();
        $targetQuantity = $existing ? $existing->quantity + $quantity : $quantity;

        // B2C logic: they can only buy items, not in bulk (so MOQ applies mainly to B2B, but let's allow it for now, or just limit it on frontend)
        $this->inventory->assertAvailable($product, $targetQuantity);
        
        // PricingService handles tiers correctly
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
     * @return array{subtotal:string,tax_total:string,shipping_total:string,discount_total:string,grand_total:string,items_count:int,total_weight:float}
     */
    public function totals(Cart $cart): array
    {
        $cart->loadMissing('items.product');

        $subtotal = $cart->items->sum(fn ($item): float => (float) $item->unit_price * $item->quantity);
        $totalWeight = $cart->items->sum(fn ($item): float => (float) ($item->product->weight ?? 0) * $item->quantity);

        // Hybrid Shipping Logic
        $shippingCost = 0.00;
        
        if ($cart->b2c_customer_id) {
            // Retail (B2C) gets standard $5 fixed shipping
            $shippingCost = 5.00;
        } else {
            // Bulk (B2B) shipping based on method
            $shippingMethod = $cart->shipping_method ?? 'weight_based';
            
            if ($shippingMethod === 'weight_based') {
                $ratePerKg = 2.00; // Placeholder rate
                $shippingCost = $totalWeight * $ratePerKg;
            } elseif ($shippingMethod === 'own_logistics') {
                $shippingCost = 0.00;
            }
        }

        $grandTotal = $subtotal + $shippingCost; // Assuming no tax for now

        return [
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'tax_total' => '0.00',
            'shipping_total' => number_format($shippingCost, 2, '.', ''),
            'discount_total' => '0.00',
            'grand_total' => number_format($grandTotal, 2, '.', ''),
            'items_count' => $cart->items->sum('quantity'),
            'total_weight' => $totalWeight,
        ];
    }
}
