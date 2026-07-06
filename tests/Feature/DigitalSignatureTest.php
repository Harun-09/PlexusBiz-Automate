<?php

namespace Tests\Feature;

use App\Domains\CorporateGovernance\Models\ComplianceDocument;
use App\Domains\CorporateGovernance\Services\DigitalSignatureService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DigitalSignatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_is_cryptographically_signed_and_verified()
    {
        $user = User::factory()->create();

        $document = ComplianceDocument::create([
            'document_type' => 'Form_XII',
            'status' => 'draft',
            'payload' => json_encode(['directors' => ['John Doe']])
        ]);

        $service = new DigitalSignatureService();
        $signature = $service->signDocument($document, $user, '192.168.1.1');

        $this->assertNotNull($signature->signature_hash);
        $this->assertEquals(get_class($user), $signature->signer_type);
        
        // Verification should pass
        $this->assertTrue($service->verifySignature($signature));
    }

    public function test_tampered_signature_fails_verification()
    {
        $user = User::factory()->create();

        $document = ComplianceDocument::create([
            'document_type' => 'Schedule_X',
            'status' => 'draft',
            'payload' => json_encode(['capital' => '1000000'])
        ]);

        $service = new DigitalSignatureService();
        $signature = $service->signDocument($document, $user);

        // Tamper with the hash in DB
        $signature->signature_hash = 'tampered_fake_hash_123';
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cryptographic verification failed. Document signature has been tampered with.");
        
        $service->verifySignature($signature);
    }
}
