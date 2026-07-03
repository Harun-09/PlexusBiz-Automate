<?php

namespace App\Http\Controllers\Marketplace;

use App\Domains\ECommerce\Models\CartItem;
use App\Domains\ECommerce\Services\CartService;
use App\Domains\ECommerce\Services\CheckoutService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CheckoutService $checkoutService,
    ) {
    }

    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $isB2C = $user instanceof \App\Models\B2CCustomer;

        $cart = $this->cartService->currentFor($user);
        $cart->load(['items.product.images', 'items.product.supplier', 'items.supplier']);

        if ($cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $summary = $this->cartService->totals($cart);

        return Inertia::render('Marketplace/Checkout/Index', [
            'buyer' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'isB2C' => $isB2C,
            'cartCount' => (int) $summary['items_count'],
            'cart' => [
                'id' => $cart->id,
                'summary' => $summary,
                'items' => $cart->items->map(fn (CartItem $item): array => $this->presentCartItem($item))->values()->all(),
            ],
            'csrfToken' => csrf_token(),
            'currency' => config('commerce.currency', 'BDT'),
            'defaultGateway' => config('commerce.default_payment_gateway', 'stripe'),
            'gateways' => $this->gateways(),
        ]);
    }

    public function process(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'gateway' => ['required', Rule::in(['stripe', 'sslcommerz'])],
            'shipping_method' => ['nullable', 'string', Rule::in(['standard', 'weight_based', 'own_logistics'])],
        ]);

        $user = $request->user();
        $cart = $this->cartService->currentFor($user);
        
        if ($cart->items()->count() === 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Save selected shipping method before checkout calculation
        if (isset($data['shipping_method'])) {
            $cart->forceFill(['shipping_method' => $data['shipping_method']])->save();
        }

        $order = $this->checkoutService->checkout($user, $cart);

        $request->merge(['gateway' => $data['gateway']]);

        return app(PaymentController::class)->process($request, $order->order_number);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCartItem(CartItem $item): array
    {
        $product = $item->product;
        $product?->loadMissing(['supplier', 'images']);

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
                'primary_image_url' => $product?->primaryImageUrl() ?? asset('images/landing/deal-imac.jpg'),
                'moq' => (int) ($product?->moq ?? 1),
                'available_stock' => (int) ($product?->availableStock() ?? 0),
                'supplier' => [
                    'company_name' => $product?->supplier?->company_name,
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function gateways(): array
    {
        return [
            [
                'key' => 'stripe',
                'label' => 'Stripe',
                'description' => 'Fast card checkout and international payments.',
                'accent' => 'blue',
            ],
            [
                'key' => 'sslcommerz',
                'label' => 'SSLCOMMERZ',
                'description' => 'Local gateway with Bangladesh-friendly payment methods.',
                'accent' => 'amber',
            ],
        ];
    }
}
