<?php

namespace App\Domains\Marketing\Providers;

use App\Domains\Marketing\Contracts\EmailProvider;
use App\Domains\Marketing\Data\DeliveryResult;
use App\Domains\Marketing\Mail\MarketingCampaignMail;
use Illuminate\Support\Facades\Mail;
use Throwable;

class LaravelEmailProvider implements EmailProvider
{
    public function send(string $to, string $subject, string $body, array $context = []): DeliveryResult
    {
        $mailer = config('mail.default', 'smtp');

        try {
            Mail::to($to)->send(new MarketingCampaignMail($subject, $body));

            return new DeliveryResult(
                successful: true,
                provider: $mailer,
                response: [
                    'to' => $to,
                    'subject' => $subject,
                    'mailer' => $mailer,
                ],
            );
        } catch (Throwable $exception) {
            return new DeliveryResult(
                successful: false,
                provider: $mailer,
                error: $exception->getMessage(),
            );
        }
    }
}
