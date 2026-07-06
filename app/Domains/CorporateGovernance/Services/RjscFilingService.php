<?php

namespace App\Domains\CorporateGovernance\Services;

use App\Domains\CorporateGovernance\Models\ComplianceDocument;

class RjscFilingService
{
    /**
     * Generates a draft Form XII or Schedule X document.
     */
    public function generateDraft(string $type, array $payloadData)
    {
        return ComplianceDocument::create([
            'document_type' => $type,
            'status' => 'draft',
            'payload' => json_encode($payloadData)
        ]);
    }

    /**
     * Mark document as filed with the RJSC.
     */
    public function markAsFiled(ComplianceDocument $document)
    {
        // Require at least one signature before filing
        if ($document->signatures()->count() === 0) {
            throw new \Exception("Document must be digitally signed before filing.");
        }

        $document->status = 'filed';
        $document->filing_date = now();
        $document->save();

        return $document;
    }
}
