<?php

namespace Tests\Feature\Api;

use App\Domains\ECommerce\Enums\RfqStatus;
use App\Domains\ECommerce\Models\Rfq;
use App\Domains\ECommerce\Models\Supplier;
use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RfqResponseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_can_create_rfq_response_and_buyer_can_accept_via_api(): void
    {
        $this->seed(RbacSeeder::class);

        $supplier = Supplier::factory()->create();
        $supplierUser = $supplier->user;
        $supplierUser->assignRole(Role::findOrCreate(RoleName::Supplier->value));

        $buyer = User::factory()->create();
        $buyer->assignRole(Role::findOrCreate(RoleName::Buyer->value));

        $rfq = Rfq::create([
            'buyer_id' => $buyer->id,
            'supplier_id' => $supplier->id,
            'rfq_number' => 'RFQ-API-1001',
            'status' => RfqStatus::Open->value,
            'message' => 'Need API quote response for monthly order.',
            'needed_by' => now()->addDays(7),
        ]);

        Sanctum::actingAs($supplierUser);

        $response = $this->postJson('/api/v1/rfq-responses', [
            'rfq_id' => $rfq->id,
            'quoted_amount' => '799.50',
            'currency' => 'BDT',
            'min_order_quantity' => 10,
            'lead_time_days' => 5,
            'valid_until' => now()->addDays(9)->toDateString(),
            'message' => 'Offer includes standard warranty.',
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'pending')
            ->json('data');

        Sanctum::actingAs($buyer);

        $this->postJson('/api/v1/rfq-responses/'.$response['id'].'/accept')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'accepted');
    }
}

