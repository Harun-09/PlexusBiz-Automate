<?php

namespace App\Domains\Marketing\Services;

use App\Domains\CRM\Models\Customer;
use App\Domains\Marketing\Contracts\EmailProvider;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Enums\MessageStatus;
use App\Domains\Marketing\Models\CampaignLog;
use App\Domains\Marketing\Models\CampaignTemplate;

class MarketingTriggerService
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
        private readonly EmailProvider $email,
    ) {
    }

    public function welcomeCustomer(Customer $customer): CampaignLog
    {
        return $this->sendTemplate('new_customer_welcome', $customer);
    }

    public function orderConfirmation(Customer $customer, array $context): CampaignLog
    {
        return $this->sendTemplate('order_confirmation', $customer, $context);
    }

    public function abandonedCartReminder(Customer $customer, array $context): CampaignLog
    {
        return $this->sendTemplate('abandoned_cart_reminder', $customer, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function sendTemplate(string $key, Customer $customer, array $context = []): CampaignLog
    {
        $template = CampaignTemplate::where('template_key', $key)
            ->where('channel', MessageChannel::Email->value)
            ->first();

        if (! $template) {
            return CampaignLog::create([
                'customer_id' => $customer->id,
                'channel' => MessageChannel::Email,
                'status' => MessageStatus::Skipped,
                'provider' => null,
                'payload' => [
                    'to' => $customer->email,
                    'template_key' => $key,
                    'context' => $context,
                ],
                'response' => [
                    'reason' => 'template_missing',
                ],
                'sent_at' => null,
            ]);
        }

        $variables = [
            'customer_name' => $customer->contact_name,
            'company_name' => $customer->company_name,
            ...$context,
        ];

        $body = $this->renderer->render($template->body, $variables);
        $subject = $template->subject ? $this->renderer->render($template->subject, $variables) : $template->name;

        $result = $this->email->send($customer->email, $subject, $body, $variables);

        return CampaignLog::create([
            'customer_id' => $customer->id,
            'channel' => MessageChannel::Email,
            'status' => $result->successful ? MessageStatus::Sent : MessageStatus::Failed,
            'provider' => $result->provider,
            'payload' => [
                'to' => $customer->email,
                'subject' => $subject,
                'body' => $body,
            ],
            'response' => $result->response,
            'error' => $result->error,
            'sent_at' => $result->successful ? now() : null,
        ]);
    }
}
