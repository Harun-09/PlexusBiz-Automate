<?php

namespace Tests\Feature\CRM;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Models\Interaction;
use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\CRM\Services\InteractionLogger;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_profile_tracks_purchase_history_and_interactions(): void
    {
        $buyer = User::factory()->create();
        $customer = app(CustomerProfileService::class)->ensureForUser($buyer, [
            'company_name' => 'Acme Operations',
            'tags' => ['priority'],
        ]);

        $order = Order::create([
            'buyer_id' => $buyer->id,
            'customer_id' => $customer->id,
            'order_number' => 'PO-TEST-001',
            'status' => OrderStatus::Confirmed,
            'subtotal' => '1250.00',
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => '1250.00',
            'currency' => 'BDT',
            'placed_at' => now(),
        ]);

        app(InteractionLogger::class)->record($customer, InteractionType::Order, 'Order placed.', $order, [
            'order_number' => $order->order_number,
        ], $buyer);

        $summary = app(CustomerProfileService::class)->purchaseSummary($customer);

        $this->assertSame(1, $summary['orders_count']);
        $this->assertSame('1250.00', $summary['total_spent']);
        $this->assertSame(1, Interaction::where('customer_id', $customer->id)->count());
    }
}
