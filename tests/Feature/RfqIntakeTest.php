<?php

namespace Tests\Feature;

use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Models\Lead;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Rfq;
use App\Domains\ECommerce\Models\Supplier;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RfqIntakeTest extends TestCase
{
    use RefreshDatabase;

    public function test_rfq_intake_page_is_displayed(): void
    {
        $this->get('/rfq')->assertOk();
    }

    public function test_buyer_rfq_submission_creates_lead_and_rfq(): void
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create([
            'supplier_id' => $supplier->id,
            'name' => 'Industrial Forklift Battery',
        ]);

        $buyer = User::factory()->create();
        $buyer->assignRole(Role::findOrCreate(RoleName::Buyer->value));

        $response = $this->actingAs($buyer)->post('/rfq', [
            'contact_name' => 'Ayesha Rahman',
            'company_name' => 'Plexus Industrial Supply',
            'email' => 'buyer.rfq@example.com',
            'phone' => '+8801712345678',
            'business_type' => 'Wholesale buyer',
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 100,
            'target_price' => '45.00',
            'needed_by' => now()->addWeek()->toDateString(),
            'message' => 'Need quotation for monthly bulk supply.',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('rfq.product', $product));

        $customer = Customer::where('user_id', $buyer->id)->firstOrFail();
        $lead = Lead::where('email', 'buyer.rfq@example.com')->firstOrFail();
        $rfq = Rfq::where('buyer_id', $buyer->id)->firstOrFail();

        $this->assertSame($customer->id, $lead->customer_id);
        $this->assertSame($supplier->id, $rfq->supplier_id);
        $this->assertSame('open', $rfq->status->value);
        $this->assertSame(1, $rfq->items()->count());
        $this->assertSame($product->id, $rfq->items()->firstOrFail()->product_id);
    }
}
