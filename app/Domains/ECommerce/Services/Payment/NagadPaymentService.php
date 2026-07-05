<?php

namespace App\Domains\ECommerce\Services\Payment;

class NagadPaymentService
{
    /**
     * Create Nagad Payment (Mocked)
     */
    public function createPayment(array $paymentData): array
    {
        // MOCK LOGIC
        return [
            'status' => 'Success',
            'paymentRefId' => 'N' . strtoupper(uniqid()),
            'nagadURL' => route('payment.nagad.mock.callback', ['paymentRefId' => 'N' . strtoupper(uniqid())]),
            'amount' => $paymentData['amount'] ?? '0.00',
            'invoice' => $paymentData['merchantInvoiceNumber'] ?? 'INV' . time()
        ];
    }
}
