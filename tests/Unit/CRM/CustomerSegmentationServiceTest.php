<?php

namespace Tests\Unit\CRM;

use App\Domains\CRM\Enums\CustomerLifecycleStage;
use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Services\CustomerSegmentationService;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Models\Order;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerSegmentationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_segments_customers_by_tags_and_total_spend(): void
    {
        $matchedUser = User::factory()->create(['account_type' => RoleName::Buyer->value]);
        $otherUser = User::factory()->create(['account_type' => RoleName::Buyer->value]);

        $matched = Customer::create([
            'user_id' => $matchedUser->id,
            'contact_name' => 'Matched Buyer',
            'email' => $matchedUser->email,
            'status' => CustomerStatus::Active,
            'lifecycle_stage' => CustomerLifecycleStage::Customer,
            'tags' => ['priority', 'wholesale'],
            'last_activity_at' => now(),
        ]);

        $other = Customer::create([
            'user_id' => $otherUser->id,
            'contact_name' => 'Other Buyer',
            'email' => $otherUser->email,
            'status' => CustomerStatus::Active,
            'lifecycle_stage' => CustomerLifecycleStage::Customer,
            'tags' => ['retail'],
            'last_activity_at' => now(),
        ]);

        $this->order($matchedUser, $matched, 'PO-SEG-001', '900.00');
        $this->order($otherUser, $other, 'PO-SEG-002', '1200.00');

        $customers = app(CustomerSegmentationService::class)
            ->query(['tags' => ['priority'], 'min_total_spent' => 500])
            ->pluck('id')
            ->all();

        $this->assertSame([$matched->id], $customers);
    }

    private function order(User $buyer, Customer $customer, string $number, string $total): Order
    {
        return Order::create([
            'buyer_id' => $buyer->id,
            'customer_id' => $customer->id,
            'order_number' => $number,
            'status' => OrderStatus::Completed,
            'subtotal' => $total,
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => $total,
            'currency' => 'BDT',
            'placed_at' => now(),
        ]);
    }
}
