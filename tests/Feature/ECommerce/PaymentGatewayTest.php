<?php

namespace Tests\Feature\ECommerce;

use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Payment;
use App\Domains\ECommerce\Services\SslCommerzService;
use App\Domains\ECommerce\Services\StripeGatewayService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_process_initiates_stripe_checkout_session(): void
    {
        $buyer = User::factory()->create();
        $order = $this->createOrder($buyer, [
            'grand_total' => '1499.99',
            'currency' => 'BDT',
        ]);

        $this->mock(StripeGatewayService::class, function ($mock): void {
            $mock->shouldReceive('isConfigured')->andReturnTrue();
            $mock->shouldReceive('createCheckoutSession')->once()->andReturn([
                'status' => 'SUCCESS',
                'id' => 'cs_test_process',
                'url' => 'https://stripe.test/checkout',
            ]);
        });

        $response = $this->actingAs($buyer)->post('/checkout/'.$order->order_number.'/payment');

        $response->assertRedirect('https://stripe.test/checkout');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_method' => 'stripe',
            'payment_status' => PaymentStatus::Processing->value,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'stripe',
            'status' => PaymentStatus::Processing->value,
            'currency' => 'BDT',
        ]);

        $this->assertNotNull($order->fresh()->checkout_token);
    }

    public function test_payment_process_falls_back_to_sslcommerz_when_stripe_is_unavailable(): void
    {
        $buyer = User::factory()->create();
        $order = $this->createOrder($buyer, [
            'grand_total' => '1499.99',
            'currency' => 'BDT',
        ]);

        $this->mock(StripeGatewayService::class, function ($mock): void {
            $mock->shouldReceive('isConfigured')->andReturnFalse();
        });

        $this->mock(SslCommerzService::class, function ($mock): void {
            $mock->shouldReceive('isConfigured')->andReturnTrue();
            $mock->shouldReceive('initiatePayment')->once()->andReturn([
                'status' => 'SUCCESS',
                'sessionkey' => 'ssl_session_fallback',
                'GatewayPageURL' => 'https://sslcommerz.test/checkout',
            ]);
        });

        $response = $this->actingAs($buyer)->post('/checkout/'.$order->order_number.'/payment');

        $response->assertRedirect('https://sslcommerz.test/checkout');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_method' => 'sslcommerz',
            'payment_status' => PaymentStatus::Processing->value,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'sslcommerz',
            'status' => PaymentStatus::Processing->value,
            'currency' => 'BDT',
        ]);
    }

    public function test_stripe_success_redirect_marks_payment_completed_and_renders_checkout_success(): void
    {
        $buyer = User::factory()->create();
        $order = $this->createOrder($buyer, [
            'payment_method' => 'stripe',
            'checkout_token' => (string) Str::uuid(),
            'grand_total' => '899.00',
            'currency' => 'BDT',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $buyer->id,
            'payment_method' => 'stripe',
            'transaction_id' => 'TXN-STRIPE-001',
            'amount' => '899.00',
            'currency' => 'BDT',
            'status' => PaymentStatus::Processing,
        ]);

        $this->mock(StripeGatewayService::class, function ($mock): void {
            $mock->shouldReceive('retrieveCheckoutSession')->once()->andReturn([
                'id' => 'cs_test_success',
                'payment_status' => 'paid',
                'status' => 'complete',
                'amount_total' => 89900,
                'payment_intent' => [
                    'id' => 'pi_test_success',
                    'object' => 'payment_intent',
                ],
                'metadata' => [
                    'payment_id' => 1,
                ],
            ]);
        });

        $this->followingRedirects();

        $response = $this->actingAs($buyer)->get('/payments/stripe/'.$order->order_number.'/success?session_id=cs_test_success&access_token='.urlencode($order->checkout_token));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Checkout/Success')
            ->where('orderNumber', $order->order_number)
            ->where('paymentStatus', PaymentStatus::Completed->value)
            ->where('transactionId', 'pi_test_success')
            ->where('flash.success', 'Stripe payment completed successfully.'));

        $this->assertTrue($payment->fresh()->isCompleted());
        $this->assertSame(PaymentStatus::Completed->value, $order->fresh()->payment_status);
        $this->assertSame('pi_test_success', $order->fresh()->transaction_id);
    }

    public function test_sslcommerz_success_redirect_marks_payment_completed(): void
    {
        $buyer = User::factory()->create();
        $order = $this->createOrder($buyer, [
            'payment_method' => 'sslcommerz',
            'checkout_token' => (string) Str::uuid(),
            'grand_total' => '219.99',
            'currency' => 'BDT',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $buyer->id,
            'payment_method' => 'sslcommerz',
            'transaction_id' => 'TXN-SSL-001',
            'amount' => '219.99',
            'currency' => 'BDT',
            'status' => PaymentStatus::Processing,
        ]);

        $this->mock(SslCommerzService::class, function ($mock): void {
            $mock->shouldReceive('validateOrder')->once()->with('val-ssl-001')->andReturn([
                'status' => 'VALID',
                'amount' => '219.99',
                'currency_amount' => '219.99',
                'tran_id' => 'ssl-tran-001',
            ]);
            $mock->shouldReceive('isTransactionValid')->andReturnTrue();
        });

        $this->followingRedirects();

        $response = $this->actingAs($buyer)->post('/payments/sslcommerz/'.$order->order_number.'/success?access_token='.urlencode($order->checkout_token), [
            'status' => 'VALID',
            'val_id' => 'val-ssl-001',
            'tran_id' => 'TXN-SSL-001',
            'amount' => '219.99',
            'currency' => 'BDT',
        ]);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Checkout/Success')
            ->where('orderNumber', $order->order_number)
            ->where('paymentStatus', PaymentStatus::Completed->value)
            ->where('transactionId', 'ssl-tran-001')
            ->where('flash.success', 'SSLCOMMERZ payment completed successfully.'));

        $this->assertTrue($payment->fresh()->isCompleted());
        $this->assertSame(PaymentStatus::Completed->value, $order->fresh()->payment_status);
        $this->assertSame('ssl-tran-001', $order->fresh()->transaction_id);
    }

    public function test_stripe_webhook_marks_payment_completed(): void
    {
        $buyer = User::factory()->create();
        $order = $this->createOrder($buyer, [
            'payment_method' => 'stripe',
            'checkout_token' => (string) Str::uuid(),
            'grand_total' => '123.45',
            'currency' => 'BDT',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $buyer->id,
            'payment_method' => 'stripe',
            'transaction_id' => 'TXN-WEBHOOK-001',
            'amount' => '123.45',
            'currency' => 'BDT',
            'status' => PaymentStatus::Processing,
        ]);

        $this->mock(StripeGatewayService::class, function ($mock): void {
            $mock->shouldReceive('verifyWebhookSignature')->once()->andReturnTrue();
        });

        $response = $this->postJson('/payments/stripe/webhook', [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_webhook_001',
                    'payment_status' => 'paid',
                    'status' => 'complete',
                    'amount_total' => 12345,
                    'payment_intent' => [
                        'id' => 'pi_webhook_001',
                        'object' => 'payment_intent',
                    ],
                    'metadata' => [
                        'payment_id' => $payment->id,
                        'order_number' => $order->order_number,
                    ],
                ],
            ],
        ]);

        $response->assertOk()->assertJson([
            'message' => 'Webhook processed',
        ]);

        $this->assertTrue($payment->fresh()->isCompleted());
        $this->assertSame(PaymentStatus::Completed->value, $order->fresh()->payment_status);
        $this->assertSame('pi_webhook_001', $order->fresh()->transaction_id);
    }

    public function test_sslcommerz_ipn_marks_payment_completed(): void
    {
        $buyer = User::factory()->create();
        $order = $this->createOrder($buyer, [
            'payment_method' => 'sslcommerz',
            'checkout_token' => (string) Str::uuid(),
            'grand_total' => '349.00',
            'currency' => 'BDT',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $buyer->id,
            'payment_method' => 'sslcommerz',
            'transaction_id' => 'TXN-IPN-001',
            'amount' => '349.00',
            'currency' => 'BDT',
            'status' => PaymentStatus::Processing,
        ]);

        $this->mock(SslCommerzService::class, function ($mock): void {
            $mock->shouldReceive('verifyIpnHash')->once()->andReturnTrue();
            $mock->shouldReceive('validateOrder')->once()->with('val-ipn-001')->andReturn([
                'status' => 'VALID',
                'amount' => '349.00',
                'currency_amount' => '349.00',
                'tran_id' => 'ssl-ipn-001',
            ]);
            $mock->shouldReceive('isTransactionValid')->andReturnTrue();
        });

        $response = $this->postJson('/payments/sslcommerz/ipn', [
            'verify_sign' => 'valid',
            'verify_key' => 'tran_id,val_id,status,amount,currency',
            'tran_id' => 'TXN-IPN-001',
            'val_id' => 'val-ipn-001',
            'status' => 'VALID',
            'amount' => '349.00',
            'currency' => 'BDT',
            'value_a' => $payment->id,
            'value_c' => $order->order_number,
        ]);

        $response->assertOk()->assertJson([
            'message' => 'IPN processed',
        ]);

        $this->assertTrue($payment->fresh()->isCompleted());
        $this->assertSame(PaymentStatus::Completed->value, $order->fresh()->payment_status);
        $this->assertSame('ssl-ipn-001', $order->fresh()->transaction_id);
    }

    private function createOrder(User $buyer, array $attributes = []): Order
    {
        return Order::create(array_merge([
            'buyer_id' => $buyer->id,
            'order_number' => 'ORD-'.Str::upper(Str::random(10)),
            'status' => OrderStatus::Pending,
            'subtotal' => '0.00',
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => '0.00',
            'currency' => 'BDT',
            'placed_at' => now(),
            'checkout_token' => (string) Str::uuid(),
            'payment_method' => null,
            'payment_status' => PaymentStatus::Pending->value,
            'transaction_id' => null,
        ], $attributes));
    }
}
