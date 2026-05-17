<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTokenEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_login_refresh_and_logout_endpoints_issue_and_rotate_tokens(): void
    {
        $registerResponse = $this->postJson('/api/auth/register', [
            'name' => 'API User',
            'email' => 'api.user@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'device_name' => 'test-device',
        ])->assertCreated()
            ->assertJsonPath('success', true);

        $token = $registerResponse->json('data.token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'api.user@example.test');

        $refreshResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/refresh')
            ->assertOk()
            ->assertJsonPath('success', true);

        $refreshedToken = $refreshResponse->json('data.token');

        $this->withHeader('Authorization', 'Bearer '.$refreshedToken)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}

