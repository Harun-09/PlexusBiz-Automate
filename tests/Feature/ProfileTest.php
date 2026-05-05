<?php

namespace Tests\Feature;

use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Services\CustomerProfileService;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Inertia\Testing\AssertableInertia as Assert;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_buyer_profile_page_shows_customer_profile_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate(RoleName::Buyer->value));

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Profile/Edit')
                ->where('customer.contact_name', $user->name)
                ->where('customerSummary.orders_count', 0));
    }

    public function test_supplier_profile_page_does_not_show_customer_profile_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate(RoleName::Supplier->value));

        Customer::create([
            'user_id' => $user->id,
            'company_name' => 'Supplier Company',
            'contact_name' => $user->name,
            'email' => $user->email,
            'status' => 'active',
            'lifecycle_stage' => 'customer',
            'tags' => [],
            'last_activity_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Profile/Edit')
                ->where('customer', null));
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_buyer_email_updates_keep_customer_email_in_sync(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate(RoleName::Buyer->value));

        $customer = app(CustomerProfileService::class)->ensureForUser($user, [
            'company_name' => 'Plexus Trading',
        ]);

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Buyer User',
                'email' => 'buyer.new@example.com',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertSame('buyer.new@example.com', $user->refresh()->email);
        $this->assertSame('buyer.new@example.com', $customer->refresh()->email);
    }

    public function test_buyer_customer_profile_can_be_updated(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate(RoleName::Buyer->value));

        app(CustomerProfileService::class)->ensureForUser($user);

        $this->actingAs($user)
            ->patch('/profile/customer', [
                'contact_name' => 'Ayesha Rahman',
                'company_name' => 'Plexus Industrial Supply',
                'phone' => '+8801712345678',
                'business_type' => 'Wholesale distributor',
                'address_line1' => 'House 12',
                'address_line2' => 'Road 5',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'postal_code' => '1205',
                'country' => 'Bangladesh',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $customer = $user->fresh()->customer;

        $this->assertNotNull($customer);
        $this->assertSame('Ayesha Rahman', $customer->contact_name);
        $this->assertSame('Plexus Industrial Supply', $customer->company_name);
        $this->assertSame('Wholesale distributor', $customer->business_type);
        $this->assertSame('House 12', $customer->address['line_1']);
        $this->assertSame('Dhaka', $customer->address['city']);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
