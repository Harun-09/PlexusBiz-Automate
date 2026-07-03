<?php

namespace App\Http\Controllers\Marketplace;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\CartItem;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Services\CartService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function index(Request $request): Response
    {
        $user = $request->user();
        $isB2C = $user instanceof \App\Models\B2CCustomer;

        $cart = $this->cartService->currentFor($user);
        $cart->load(['items.product.images', 'items.product.supplier', 'items.supplier']);

        $summary = $this->cartService->totals($cart);

        return Inertia::render('Marketplace/Cart/Index', [
            'isB2C' => $isB2C,
            'cartCount' => (int) $summary['items_count'],
            'cart' => [
                'id' => $cart->id,
                'status' => $cart->status->value,
                'shipping_method' => $cart->shipping_method,
                'summary' => $summary,
                'items' => $cart->items->map(fn (CartItem $item): array => $this->presentCartItem($item))->values()->all(),
            ],
            'suggestions' => $this->suggestions($cart),
            'currency' => config('commerce.currency', 'BDT'),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::query()
            ->with(['supplier', 'images', 'pricingTiers'])
            ->findOrFail((int) $data['product_id']);

        $quantity = max(1, (int) ($data['quantity'] ?? $product->moq));

        $this->cartService->addItem($request->user(), $product, $quantity);

        return back()->with('success', sprintf('%s added to your cart.', $product->name));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_id' => ['nullable', 'integer', 'exists:cart_items,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'shipping_method' => ['nullable', 'string', \Illuminate\Validation\Rule::in(['standard', 'weight_based', 'own_logistics'])],
        ]);

        $user = $request->user();
        $cart = $this->cartService->currentFor($user);
        
        if (isset($data['shipping_method'])) {
            $cart->forceFill(['shipping_method' => $data['shipping_method']])->save();
        }

        if (isset($data['item_id'])) {
            // Update quantity for specific item
            $item = $cart->items()->whereKey((int) $data['item_id'])->with('product')->first();
            if ($item) {
                $this->cartService->updateItem($item, (int) $data['quantity']);
            }
        }

        return redirect()
            ->back() // Change from route('cart.index') to back() so it works from checkout too
            ->with('success', 'Cart updated successfully.');
    }

    public function remove(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_id' => ['required', 'integer', 'exists:cart_items,id'],
        ]);

        $cart = $this->cartService->currentFor($request->user());
        $item = $cart->items()->whereKey((int) $data['item_id'])->with('product')->firstOrFail();

        $this->cartService->removeItem($item);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Item removed from cart.');
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCartItem(CartItem $item): array
    {
        $product = $item->product;
        $product?->loadMissing(['supplier', 'category', 'images', 'pricingTiers']);

        return [
            'id' => $item->id,
            'quantity' => (int) $item->quantity,
            'unit_price' => (float) $item->unit_price,
            'line_total' => (float) $item->unit_price * (int) $item->quantity,
            'product' => [
                'id' => $product?->id,
                'name' => $product?->name,
                'slug' => $product?->slug,
                'sku' => $product?->sku,
                'moq' => (int) ($product?->moq ?? 1),
                'available_stock' => (int) ($product?->availableStock() ?? 0),
                'status' => $product?->status?->value,
                'primary_image_url' => $product?->primaryImageUrl() ?? asset('images/landing/deal-imac.jpg'),
                'supplier' => [
                    'id' => $product?->supplier?->id,
                    'company_name' => $product?->supplier?->company_name,
                ],
            ],
            'supplier' => [
                'id' => $item->supplier?->id,
                'company_name' => $item->supplier?->company_name,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function suggestions(Cart $cart): array
    {
        $productIds = $cart->items->pluck('product_id')->all();

        return Product::query()
            ->with(['supplier', 'category', 'images', 'pricingTiers'])
            ->where('status', ProductStatus::Active->value)
            ->when(! empty($productIds), fn ($query) => $query->whereNotIn('id', $productIds))
            ->latest('published_at')
            ->limit(4)
            ->get()
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'base_price' => (float) $product->base_price,
                'moq' => (int) $product->moq,
                'available_stock' => (int) $product->availableStock(),
                'primary_image_url' => $product->primaryImageUrl(),
                'supplier' => [
                    'company_name' => $product->supplier?->company_name,
                ],
            ])
            ->values()
            ->all();
    }
}
