<?php

namespace App\Domains\Marketing\Providers;

use App\Domains\Marketing\Contracts\EmailProvider;
use App\Domains\Marketing\Data\DeliveryResult;

class MockEmailProvider implements EmailProvider
{
    public function send(string $to, string $subject, string $body, array $context = []): DeliveryResult
    {
        return new DeliveryResult(
            successful: true,
            provider: 'mock_email',
            response: [
                'to' => $to,
                'subject' => $subject,
                'message_id' => 'mock-email-'.sha1($to.$subject.$body.microtime(true)),
            ],
        );
    }
}
