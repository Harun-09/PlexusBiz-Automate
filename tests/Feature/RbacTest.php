<?php

namespace Tests\Feature;

use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Supplier;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_pending_application(): void
    {
        $this->seed(RbacSeeder::class);

        $response = $this->post('/register', [
            'first_name' => 'Buyer',
            'last_name' => 'Account',
            'account_type' => RoleName::Buyer->value,
            'company_name' => 'Buyer Account Ltd',
            'job_title' => 'Procurement Manager',
            'phone' => '+8801700000000',
            'employees' => '11 - 25',
            'country' => 'Bangladesh',
            'email' => 'buyer.account@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'agree_terms' => true,
        ]);

        $response->assertRedirect('/register/pending?account_type=buyer');

        $user = User::where('email', 'buyer.account@example.com')->firstOrFail();

        $this->assertSame(UserStatus::Pending, $user->status);
        $this->assertSame(RoleName::Buyer->value, $user->account_type);
        $this->assertTrue($user->roles->isEmpty());
        $this->assertGuest();

        $this->assertDatabaseHas('messages', [
            'receiver_id' => User::role(RoleName::Admin->value)->firstOrFail()->id,
            'subject' => 'New buyer application: Buyer Account Ltd',
        ]);
    }

    public function test_admin_can_approve_pending_buyer_application(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $user = User::create([
            'name' => 'Pending Buyer',
            'company_name' => 'Pending Buyer Ltd',
            'job_title' => 'Buyer',
            'phone' => '+8801700000001',
            'employees' => '26 - 50',
            'country' => 'Bangladesh',
            'account_type' => RoleName::Buyer->value,
            'email' => 'pending-buyer@example.com',
            'password' => Hash::make('Password123!'),
            'status' => UserStatus::Pending->value,
        ]);

        $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/approve")
            ->assertRedirect('/admin/users');

        $user->refresh();

        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertTrue($user->hasRole(RoleName::Buyer->value));
        $this->assertDatabaseHas('customers', [
            'user_id' => $user->id,
            'company_name' => 'Pending Buyer Ltd',
        ]);
    }

    public function test_admin_can_approve_pending_supplier_application(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $user = User::create([
            'name' => 'Pending Supplier',
            'company_name' => 'Pending Supplier Ltd',
            'job_title' => 'Supply Manager',
            'phone' => '+8801700000002',
            'employees' => '51 - 200',
            'country' => 'Bangladesh',
            'account_type' => RoleName::Supplier->value,
            'email' => 'pending-supplier@example.com',
            'password' => Hash::make('Password123!'),
            'status' => UserStatus::Pending->value,
        ]);

        $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/approve")
            ->assertRedirect('/admin/users');

        $user->refresh();
        $supplier = Supplier::where('user_id', $user->id)->firstOrFail();

        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertTrue($user->hasRole(RoleName::Supplier->value));
        $this->assertSame(SupplierStatus::Approved, $supplier->status);
        $this->assertSame($user->email, $supplier->contact_email);
    }

    public function test_admin_can_reject_pending_application(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $user = User::create([
            'name' => 'Rejected Applicant',
            'company_name' => 'Rejected Applicant Ltd',
            'job_title' => 'Manager',
            'phone' => '+8801700000003',
            'employees' => '1 - 10',
            'country' => 'Bangladesh',
            'account_type' => RoleName::Buyer->value,
            'email' => 'rejected-applicant@example.com',
            'password' => Hash::make('Password123!'),
            'status' => UserStatus::Pending->value,
        ]);

        $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/reject")
            ->assertRedirect('/admin/users');

        $user->refresh();

        $this->assertSame(UserStatus::Rejected, $user->status);
        $this->assertTrue($user->roles->isEmpty());
    }

    public function test_role_middleware_blocks_non_admin_users(): void
    {
        $this->seed(RbacSeeder::class);

        Route::middleware(['web', 'auth', 'role:admin'])->get('/rbac-test/admin-only', fn (): string => 'ok');

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();

        $this->actingAs($admin)->get('/rbac-test/admin-only')->assertOk();
        $this->actingAs($buyer)->get('/rbac-test/admin-only')->assertForbidden();
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $user->forceFill(['status' => UserStatus::Suspended])->save();

        $response = $this->post('/login', [
            'email' => 'buyer@plexus.test',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
