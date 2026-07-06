<?php

namespace App\Domains\CorporateGovernance\Services;

use App\Domains\CorporateGovernance\Models\DigitalSignature;
use Exception;
use Illuminate\Database\Eloquent\Model;

class DigitalSignatureService
{
    /**
     * Mathematically signs a payload and saves the proof.
     */
    public function signDocument(Model $signable, Model $signer, string $ipAddress = '127.0.0.1')
    {
        // For a real-world PKI we would hash the PDF or the JSON string and sign it with a private key.
        // Here we simulate the cryptographic hash of the document ID, class, signer ID, and timestamp.
        $timestamp = now();
        $payloadToSign = get_class($signable) . '_' . $signable->id . '_' . get_class($signer) . '_' . $signer->id . '_' . $timestamp->timestamp;
        
        // Simulating SHA-256 HMAC or RSA signature hash
        $signatureHash = hash_hmac('sha256', $payloadToSign, config('app.key'));

        return DigitalSignature::create([
            'signable_type' => get_class($signable),
            'signable_id' => $signable->id,
            'signer_type' => get_class($signer),
            'signer_id' => $signer->id,
            'signature_hash' => $signatureHash,
            'ip_address' => $ipAddress,
            'signed_at' => $timestamp
        ]);
    }

    /**
     * Verify if a signature is cryptographically valid.
     */
    public function verifySignature(DigitalSignature $signature)
    {
        $payloadToSign = $signature->signable_type . '_' . $signature->signable_id . '_' . $signature->signer_type . '_' . $signature->signer_id . '_' . $signature->signed_at->timestamp;
        $expectedHash = hash_hmac('sha256', $payloadToSign, config('app.key'));

        if (!hash_equals($expectedHash, $signature->signature_hash)) {
            throw new Exception("Cryptographic verification failed. Document signature has been tampered with.");
        }

        return true;
    }
}
