<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_submit_pending_registration(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'account_type' => RoleName::Buyer->value,
            'company_name' => 'Test Buyer Ltd',
            'job_title' => 'Procurement Lead',
            'phone' => '+8801700000000',
            'employees' => '11 - 25',
            'country' => 'Bangladesh',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'agree_terms' => true,
        ]);

        $response->assertRedirect('/register/pending?account_type=buyer');

        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'status' => UserStatus::Pending->value,
            'account_type' => RoleName::Buyer->value,
        ]);
        $this->assertTrue(User::where('email', 'test@example.com')->firstOrFail()->roles->isEmpty());
    }
}
