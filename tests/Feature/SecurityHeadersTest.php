<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    /**
     * Test if global security headers are applied to responses.
     */
    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/'); // Adjust to a valid route like '/'

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->assertHeader('Content-Security-Policy');
    }
}
