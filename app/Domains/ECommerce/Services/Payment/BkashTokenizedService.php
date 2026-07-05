<?php

namespace App\Domains\ECommerce\Services\Payment;

use Illuminate\Support\Facades\Http;

class BkashTokenizedService
{
    /**
     * Create bKash Tokenized Payment (Mocked)
     */
    public function createPayment(array $paymentData): array
    {
        // REAL API LOGIC
        /*
        $token = $this->grantToken();
        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key' => config('services.bkash.app_key')
        ])->post(config('services.bkash.base_url') . '/tokenized/checkout/create', $paymentData);
        
        return $response->json();
        */

        // MOCK LOGIC
        return [
            'statusCode' => '0000',
            'statusMessage' => 'Successful',
            'paymentID' => 'TRX' . strtoupper(uniqid()),
            'bkashURL' => route('payment.bkash.mock.callback', ['paymentID' => 'TRX' . strtoupper(uniqid())]),
            'callbackURL' => $paymentData['callbackURL'] ?? '',
            'successCallbackURL' => $paymentData['callbackURL'] ?? '',
            'failureCallbackURL' => $paymentData['callbackURL'] ?? '',
            'cancelledCallbackURL' => $paymentData['callbackURL'] ?? '',
            'amount' => $paymentData['amount'] ?? '0.00',
            'intent' => 'sale',
            'currency' => 'BDT',
            'merchantInvoiceNumber' => $paymentData['merchantInvoiceNumber'] ?? 'INV' . time()
        ];
    }

    /**
     * Execute bKash Tokenized Payment (Mocked)
     */
    public function executePayment(string $paymentID): array
    {
        // REAL API LOGIC
        /*
        $token = $this->grantToken();
        $response = Http::withHeaders([
            'Authorization' => $token,
            'X-APP-Key' => config('services.bkash.app_key')
        ])->post(config('services.bkash.base_url') . '/tokenized/checkout/execute', [
            'paymentID' => $paymentID
        ]);
        
        return $response->json();
        */

        // MOCK LOGIC
        return [
            'statusCode' => '0000',
            'statusMessage' => 'Successful',
            'paymentID' => $paymentID,
            'trxID' => 'BK' . time(),
            'transactionStatus' => 'Completed',
            'amount' => '1000.00', // Mocked amount
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => 'INV' . time()
        ];
    }
}
