<?php

namespace Tests\Feature\ECommerce;

use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Supplier;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierOnboardingNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_application_notifies_admins_and_decision_notifies_supplier(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $response = $this->post('/supplier/apply', [
            'name' => 'Nova Supplier',
            'company_name' => 'Nova Wholesale',
            'email' => 'nova-supplier@plexus.test',
            'phone' => '01720000000',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'tax_number' => 'TIN-NOVA-001',
            'address_line1' => 'House 12',
            'address_line2' => 'Road 5',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
        ]);

        $response->assertRedirect('/dashboard');

        $supplierUser = User::where('email', 'nova-supplier@plexus.test')->firstOrFail();
        $supplier = Supplier::where('user_id', $supplierUser->id)->firstOrFail();

        $this->assertDatabaseHas('messages', [
            'receiver_id' => $admin->id,
            'subject' => 'New supplier application: Nova Wholesale',
        ]);

        $this->assertSame(SupplierStatus::Pending, $supplier->status);

        $this->actingAs($admin)
            ->patch("/admin/suppliers/{$supplier->id}", [
                'company_name' => 'Nova Wholesale',
                'contact_email' => 'nova-supplier@plexus.test',
                'phone' => '01720000000',
                'tax_number' => 'TIN-NOVA-001',
                'status' => SupplierStatus::Approved->value,
            ])
            ->assertRedirect('/admin/suppliers');

        $this->assertDatabaseHas('messages', [
            'receiver_id' => $supplierUser->id,
            'subject' => 'Supplier application approved',
        ]);

        $this->actingAs($admin)
            ->patch("/admin/suppliers/{$supplier->id}", [
                'company_name' => 'Nova Wholesale',
                'contact_email' => 'nova-supplier@plexus.test',
                'phone' => '01720000000',
                'tax_number' => 'TIN-NOVA-001',
                'status' => SupplierStatus::Rejected->value,
            ])
            ->assertRedirect('/admin/suppliers');

        $this->assertDatabaseHas('messages', [
            'receiver_id' => $supplierUser->id,
            'subject' => 'Supplier application rejected',
        ]);
    }
}
