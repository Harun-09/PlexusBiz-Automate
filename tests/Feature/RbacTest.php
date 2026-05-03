<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_assigns_buyer_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Buyer Account',
            'email' => 'buyer.account@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'buyer.account@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole(RoleName::Buyer->value));
        $this->assertAuthenticatedAs($user);
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
