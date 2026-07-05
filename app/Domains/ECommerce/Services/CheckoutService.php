<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\CRM\Services\InteractionLogger;
use App\Domains\ECommerce\Enums\CartStatus;
use App\Domains\ECommerce\Enums\InvoiceStatus;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentTerm;
use App\Domains\ECommerce\Enums\EscrowStatus;
use App\Domains\ECommerce\Enums\DeliveryStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Events\OrderPlaced;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\SupplierOrder;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\User;
use App\Models\B2CCustomer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use App\Domains\ECommerce\Services\Payment\BkashTokenizedService;

class CheckoutService
{
    public function __construct(
        private readonly InventoryService $inventory,
        private readonly PricingService $pricing,
        private readonly NumberSequenceService $numbers,
        private readonly CustomerProfileService $customers,
        private readonly InteractionLogger $interactions,
        private readonly InvoicePdfService $invoicePdf,
        private readonly BkashTokenizedService $bkash,
    ) {
    }

    public function checkout(Authenticatable $buyer, ?Cart $cart = null, string $paymentTerm = 'cash'): Order
    {
        return DB::transaction(function () use ($buyer, $cart, $paymentTerm): Order {
            $cart = $this->lockCart($buyer, $cart);
            $cart->load('items.product');
            
            // For B2C, we don't need a full CRM profile yet, but let's just create one or use a dummy.
            // Wait, CustomerProfileService is for B2B. If it's a B2CCustomer, they are their own customer.
            $customer = null;
            if ($buyer instanceof User) {
                $customer = $this->customers->ensureForUser($buyer);
            }

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Cart is empty.']);
            }

            $preparedItems = [];
            $subtotal = 0.0;
            $totalWeight = 0.0;

            foreach ($cart->items as $item) {
                $product = clone $item->product; // Keep original without lock if needed
                // Actually lock the product
                $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->firstOrFail();

                if ($product->status !== ProductStatus::Active) {
                    throw ValidationException::withMessages(['product' => sprintf('%s is not active.', $product->name)]);
                }

                // If B2C, check inventory differently? No, inventory is the same
                $this->inventory->assertAvailable($product, $item->quantity);
                $unitPrice = $this->pricing->unitPrice($product, $item->quantity);
                $lineTotal = (float) $unitPrice * $item->quantity;
                $subtotal += $lineTotal;
                $totalWeight += (float) ($product->weight ?? 0) * $item->quantity;

                $preparedItems[] = [
                    'cart_item' => $item,
                    'product' => $product,
                    'unit_price' => $unitPrice,
                    'total' => number_format($lineTotal, 2, '.', ''),
                ];
            }

            $paymentTermEnum = PaymentTerm::tryFrom($paymentTerm) ?? PaymentTerm::Cash;
            $dueDate = match($paymentTermEnum) {
                PaymentTerm::Net30 => now()->addDays(30),
                PaymentTerm::Net60 => now()->addDays(60),
                default => null,
            };

            // Credit logic is for B2B only
            if ($customer && $paymentTermEnum !== PaymentTerm::Cash) {
                if ($customer->is_credit_restricted) {
                    throw ValidationException::withMessages(['payment' => 'Your account is restricted from using net terms due to overdue invoices.']);
                }
                $availableCredit = $customer->credit_limit - $customer->credit_used;
                if ($subtotal > $availableCredit) {
                    throw ValidationException::withMessages(['payment' => 'Order total exceeds available credit limit.']);
                }
                
                // Update credit used
                $customer->forceFill(['credit_used' => $customer->credit_used + $subtotal])->save();
            }
            
            // Shipping calculation
            $shippingCost = 0.0;
            $shippingMethod = $cart->shipping_method ?? 'weight_based';
            
            if ($buyer instanceof B2CCustomer) {
                $shippingCost = 5.00;
                $shippingMethod = 'standard';
            } else {
                if ($shippingMethod === 'weight_based') {
                    $rate = config('commerce.shipping_rate_per_kg', 2.00);
                    $shippingCost = $totalWeight * $rate; // configurable rate
                } else {
                    $shippingCost = 0.00; // own_logistics
                }
            }

            $grandTotal = $subtotal + $shippingCost;

            $order = Order::create([
                'buyer_id' => $buyer instanceof User ? $buyer->id : null,
                'customer_id' => $customer ? $customer->id : ($buyer instanceof B2CCustomer ? $buyer->id : null), // B2C customer ID is used in customer_id directly here as a simplification, though customer_id in orders is nullable. Let's keep it null if B2C, or store the B2CCustomer ID in a new column. Wait, I made buyer_id nullable. So we can just leave customer_id null for B2C, and buyer_id null, and we don't have a way to link it back! 
                // Ah, wait. For B2C, buyer_id is null. We need a way to link the order. I should just use `customer_id` for B2C customer!
                // Yes, `customer_id` is an unsignedBigInteger. 
                'order_number' => $this->numbers->orderNumber(),
                'status' => OrderStatus::Confirmed,
                'payment_term' => $paymentTermEnum->value,
                'due_date' => $dueDate,
                'escrow_status' => EscrowStatus::Held->value,
                'delivery_status' => DeliveryStatus::Pending->value,
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'tax_total' => '0.00',
                'shipping_total' => number_format($shippingCost, 2, '.', ''),
                'shipping_method' => $shippingMethod,
                'discount_total' => '0.00',
                'grand_total' => number_format($grandTotal, 2, '.', ''),
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

                // inventory deduct expects User for B2B, but can handle B2CCustomer if we modify it, or we just pass the object
                $this->inventory->deductForOrder($product, $prepared['cart_item']->quantity, $order, $buyer instanceof User ? $buyer : null);
            }

            $this->createSupplierOrders($order, $preparedItems);

            $invoice = $order->invoice()->create([
                'invoice_number' => $this->numbers->invoiceNumber(),
                'status' => InvoiceStatus::Issued,
                'subtotal' => $order->subtotal,
                'tax_total' => $order->tax_total,
                'total' => $order->grand_total,
                'issued_at' => now(),
                'due_at' => $dueDate ?? now()->addDays(7),
            ]);

            $this->invoicePdf->generatePdf($invoice);

            if ($customer) {
                $this->customers->attachOrder($customer, $order);
                $this->interactions->record(
                    customer: $customer,
                    type: InteractionType::Order,
                    summary: sprintf('Order %s placed for %s.', $order->order_number, $order->grand_total),
                    related: $order,
                    payload: ['order_number' => $order->order_number, 'grand_total' => $order->grand_total],
                    actor: $buyer instanceof User ? $buyer : null,
                    direction: 'inbound',
                );
            }

            $cart->forceFill([
                'status' => CartStatus::Converted,
                'converted_order_id' => $order->id,
            ])->save();

            DB::afterCommit(fn () => event(new OrderPlaced($order->fresh(['items', 'invoice', 'buyer']))));

            $order = $order->fresh(['items', 'invoice']);

            if ($paymentTermEnum === PaymentTerm::Bkash) {
                $bkashResponse = $this->bkash->createPayment([
                    'amount' => $order->grand_total,
                    'merchantInvoiceNumber' => $order->order_number,
                    'callbackURL' => route('api.payment.bkash.callback', ['order' => $order->id]),
                ]);
                $order->payment_url = $bkashResponse['bkashURL'] ?? null;
            } elseif ($paymentTermEnum === PaymentTerm::Nagad) {
                $nagadService = new \App\Domains\ECommerce\Services\Payment\NagadPaymentService();
                $nagadResponse = $nagadService->createPayment([
                    'amount' => $order->grand_total,
                    'merchantInvoiceNumber' => $order->order_number,
                ]);
                $order->payment_url = $nagadResponse['nagadURL'] ?? null;
            } elseif ($paymentTermEnum === PaymentTerm::Rocket) {
                $rocketService = new \App\Domains\ECommerce\Services\Payment\RocketPaymentService();
                $rocketResponse = $rocketService->createPayment([
                    'amount' => $order->grand_total,
                    'merchantInvoiceNumber' => $order->order_number,
                ]);
                $order->payment_url = $rocketResponse['rocketURL'] ?? null;
            }

            return $order;
        });
    }

    private function lockCart(Authenticatable $buyer, ?Cart $cart): Cart
    {
        $column = $buyer instanceof B2CCustomer ? 'b2c_customer_id' : 'user_id';
        
        $query = Cart::query()
            ->where($column, $buyer->id)
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

    /**
     * @param array<int, array{cart_item:mixed,product:Product,unit_price:string,total:string}> $preparedItems
     */
    private function createSupplierOrders(Order $order, array $preparedItems): void
    {
        collect($preparedItems)
            ->groupBy(fn (array $prepared): int => (int) $prepared['product']->supplier_id)
            ->each(function ($supplierItems, int $supplierId) use ($order): void {
                $subtotal = $supplierItems->sum(fn (array $prepared): float => (float) $prepared['total']);

                SupplierOrder::create([
                    'order_id' => $order->id,
                    'supplier_id' => $supplierId,
                    'supplier_order_number' => $this->numbers->supplierOrderNumber(),
                    'status' => OrderStatus::Pending->value,
                    'subtotal' => number_format($subtotal, 2, '.', ''),
                    'grand_total' => number_format($subtotal, 2, '.', ''),
                    'currency' => $order->currency,
                    'placed_at' => now(),
                ]);
            });
    }
}
