<?php

namespace App\Domains\ECommerce\Services\Payment;

class RocketPaymentService
{
    /**
     * Create Rocket Payment (Mocked)
     */
    public function createPayment(array $paymentData): array
    {
        // MOCK LOGIC
        return [
            'status' => 'Success',
            'rocketTxnId' => 'R' . strtoupper(uniqid()),
            'rocketURL' => route('payment.rocket.mock.callback', ['rocketTxnId' => 'R' . strtoupper(uniqid())]),
            'amount' => $paymentData['amount'] ?? '0.00',
            'invoice' => $paymentData['merchantInvoiceNumber'] ?? 'INV' . time()
        ];
    }
}
