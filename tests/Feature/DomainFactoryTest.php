<?php

namespace Tests\Feature;

use App\Domains\CRM\Models\Customer;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Support\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_domain_factories_create_valid_records(): void
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->for($supplier)->create();
        $customer = Customer::factory()->create();
        $ticket = SupportTicket::factory()->for($supplier)->create();

        $this->assertSame($supplier->id, $product->supplier_id);
        $this->assertNotNull($customer->user_id);
        $this->assertSame($supplier->id, $ticket->supplier_id);
    }
}
