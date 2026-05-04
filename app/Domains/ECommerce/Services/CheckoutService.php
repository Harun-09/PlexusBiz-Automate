<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\CRM\Services\InteractionLogger;
use App\Domains\ECommerce\Enums\CartStatus;
use App\Domains\ECommerce\Enums\InvoiceStatus;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Events\OrderPlaced;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        private readonly InventoryService $inventory,
        private readonly PricingService $pricing,
        private readonly NumberSequenceService $numbers,
        private readonly CustomerProfileService $customers,
        private readonly InteractionLogger $interactions,
        private readonly InvoicePdfService $invoicePdf,
    ) {
    }

    public function checkout(User $buyer, ?Cart $cart = null): Order
    {
        return DB::transaction(function () use ($buyer, $cart): Order {
            $cart = $this->lockCart($buyer, $cart);
            $cart->load('items.product');
            $customer = $this->customers->ensureForUser($buyer);

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Cart is empty.']);
            }

            $preparedItems = [];
            $subtotal = 0.0;

            foreach ($cart->items as $item) {
                $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->firstOrFail();

                if ($product->status !== ProductStatus::Active) {
                    throw ValidationException::withMessages(['product' => sprintf('%s is not active.', $product->name)]);
                }

                $this->inventory->assertAvailable($product, $item->quantity);
                $unitPrice = $this->pricing->unitPrice($product, $item->quantity);
                $lineTotal = (float) $unitPrice * $item->quantity;
                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'cart_item' => $item,
                    'product' => $product,
                    'unit_price' => $unitPrice,
                    'total' => number_format($lineTotal, 2, '.', ''),
                ];
            }

            $order = Order::create([
                'buyer_id' => $buyer->id,
                'customer_id' => $customer->id,
                'order_number' => $this->numbers->orderNumber(),
                'status' => OrderStatus::Confirmed,
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'tax_total' => '0.00',
                'shipping_total' => '0.00',
                'discount_total' => '0.00',
                'grand_total' => number_format($subtotal, 2, '.', ''),
                'currency' => config('commerce.currency', 'BDT'),
                'placed_at' => now(),
            ]);

            foreach ($preparedItems as $prepared) {
                $product = $prepared['product'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'supplier_id' => $product->supplier_id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $prepared['cart_item']->quantity,
                    'unit_price' => $prepared['unit_price'],
                    'total' => $prepared['total'],
                    'status' => OrderStatus::Confirmed->value,
                ]);

                $this->inventory->deductForOrder($product, $prepared['cart_item']->quantity, $order, $buyer);
            }

            $invoice = $order->invoice()->create([
                'invoice_number' => $this->numbers->invoiceNumber(),
                'status' => InvoiceStatus::Issued,
                'subtotal' => $order->subtotal,
                'tax_total' => $order->tax_total,
                'total' => $order->grand_total,
                'issued_at' => now(),
                'due_at' => now()->addDays(7),
            ]);

            // Generate PDF for invoice
            $this->invoicePdf->generatePdf($invoice);

            $this->customers->attachOrder($customer, $order);
            $this->interactions->record(
                customer: $customer,
                type: InteractionType::Order,
                summary: sprintf('Order %s placed for %s.', $order->order_number, $order->grand_total),
                related: $order,
                payload: ['order_number' => $order->order_number, 'grand_total' => $order->grand_total],
                actor: $buyer,
                direction: 'inbound',
            );

            $cart->forceFill([
                'status' => CartStatus::Converted,
                'converted_order_id' => $order->id,
            ])->save();

            DB::afterCommit(fn () => event(new OrderPlaced($order->fresh(['items', 'invoice', 'buyer']))));

            return $order->fresh(['items', 'invoice']);
        });
    }

    private function lockCart(User $buyer, ?Cart $cart): Cart
    {
        $query = Cart::query()
            ->where('user_id', $buyer->id)
            ->where('status', CartStatus::Active->value)
            ->lockForUpdate();

        if ($cart) {
            $query->whereKey($cart->id);
        }

        $lockedCart = $query->first();

        if (! $lockedCart) {
            throw ValidationException::withMessages(['cart' => 'Active cart was not found.']);
        }

        return $lockedCart;
    }
}
