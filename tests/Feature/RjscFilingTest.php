<?php

namespace Tests\Feature;

use App\Domains\CorporateGovernance\Models\ComplianceDocument;
use App\Domains\CorporateGovernance\Services\DigitalSignatureService;
use App\Domains\CorporateGovernance\Services\RjscFilingService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RjscFilingTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_cannot_be_filed_without_signatures()
    {
        $filingService = new RjscFilingService();
        $document = $filingService->generateDraft('Form_XII', ['directors' => ['Alice']]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Document must be digitally signed before filing.");

        $filingService->markAsFiled($document);
    }

    public function test_document_files_successfully_when_signed()
    {
        $user = User::factory()->create();
        
        $filingService = new RjscFilingService();
        $document = $filingService->generateDraft('Form_XII', ['directors' => ['Alice']]);

        $signatureService = new DigitalSignatureService();
        $signatureService->signDocument($document, $user);

        // Should not throw exception
        $document = $filingService->markAsFiled($document);

        $this->assertEquals('filed', $document->status);
        $this->assertNotNull($document->filing_date);
    }
}
