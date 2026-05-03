<?php

namespace App\Domains\Marketing\Providers;

use App\Domains\Marketing\Contracts\SmsProvider;
use App\Domains\Marketing\Data\DeliveryResult;

class MockSmsProvider implements SmsProvider
{
    public function send(string $to, string $body, array $context = []): DeliveryResult
    {
        return new DeliveryResult(
            successful: true,
            provider: 'mock_sms',
            response: [
                'to' => $to,
                'message_id' => 'mock-sms-'.sha1($to.$body.microtime(true)),
            ],
        );
    }
}
