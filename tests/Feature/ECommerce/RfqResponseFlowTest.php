<?php

namespace Tests\Feature\ECommerce;

use App\Domains\ECommerce\Enums\RfqResponseStatus;
use App\Domains\ECommerce\Enums\RfqStatus;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Rfq;
use App\Domains\ECommerce\Models\RfqResponse;
use App\Domains\ECommerce\Models\Supplier;
use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RfqResponseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_can_submit_quote_and_buyer_can_accept_it(): void
    {
        $this->seed(RbacSeeder::class);

        $supplier = Supplier::factory()->create();
        $supplier->user->assignRole(Role::findOrCreate(RoleName::Supplier->value));

        $buyer = User::factory()->create();
        $buyer->assignRole(Role::findOrCreate(RoleName::Buyer->value));

        $product = Product::factory()->create([
            'supplier_id' => $supplier->id,
            'name' => 'Industrial Steel Coil',
            'sku' => 'RFQ-COIL-001',
        ]);

        $this->actingAs($buyer)->post('/rfq', [
            'contact_name' => 'Buyer One',
            'company_name' => 'Buyer Company',
            'email' => 'buyer.one@example.test',
            'phone' => '+8801711111111',
            'business_type' => 'Wholesale',
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 40,
            'target_price' => '520.00',
            'needed_by' => now()->addDays(5)->toDateString(),
            'message' => 'Need monthly quote with delivery lead time.',
        ])->assertRedirect();

        $rfq = Rfq::query()->latest('id')->firstOrFail();

        $this->actingAs($supplier->user)
            ->get("/commerce/rfq-responses/{$rfq->id}/create")
            ->assertOk();

        $this->actingAs($supplier->user)
            ->post("/commerce/rfq-responses/{$rfq->id}", [
                'quoted_amount' => '499.99',
                'currency' => 'BDT',
                'min_order_quantity' => 20,
                'lead_time_days' => 7,
                'valid_until' => now()->addDays(10)->toDateString(),
                'message' => 'Price includes packaging and standard logistics.',
            ])
            ->assertRedirect('/commerce/rfq-responses');

        $response = RfqResponse::query()->where('rfq_id', $rfq->id)->firstOrFail();

        $this->assertSame(RfqResponseStatus::Pending, $response->status);
        $this->assertSame(RfqStatus::Quoted, $rfq->fresh()->status);

        $this->actingAs($buyer)
            ->post("/commerce/rfq-responses/{$response->id}/accept")
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(RfqResponseStatus::Accepted, $response->fresh()->status);
        $this->assertSame(RfqStatus::Accepted, $rfq->fresh()->status);
    }
}

