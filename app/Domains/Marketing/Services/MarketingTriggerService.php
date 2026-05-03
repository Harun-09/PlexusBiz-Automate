<?php

namespace App\Domains\Marketing\Services;

use App\Domains\CRM\Models\Customer;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Enums\MessageStatus;
use App\Domains\Marketing\Models\CampaignLog;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Domains\Marketing\Contracts\EmailProvider;
use App\Domains\Marketing\Contracts\SmsProvider;

class MarketingTriggerService
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
        private readonly EmailProvider $email,
        private readonly SmsProvider $sms,
    ) {
    }

    public function welcomeCustomer(Customer $customer): CampaignLog
    {
        return $this->sendTemplate('new_customer_welcome', MessageChannel::Email, $customer);
    }

    public function orderConfirmation(Customer $customer, array $context): CampaignLog
    {
        return $this->sendTemplate('order_confirmation', MessageChannel::Email, $customer, $context);
    }

    public function abandonedCartReminder(Customer $customer, array $context): CampaignLog
    {
        return $this->sendTemplate('abandoned_cart_reminder', MessageChannel::Email, $customer, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function sendTemplate(string $key, MessageChannel $channel, Customer $customer, array $context = []): CampaignLog
    {
        $template = CampaignTemplate::where('template_key', $key)->firstOrFail();
        $variables = [
            'customer_name' => $customer->contact_name,
            'company_name' => $customer->company_name,
            ...$context,
        ];

        $body = $this->renderer->render($template->body, $variables);
        $subject = $template->subject ? $this->renderer->render($template->subject, $variables) : $template->name;

        $result = match ($channel) {
            MessageChannel::Email => $this->email->send($customer->email, $subject, $body, $variables),
            MessageChannel::Sms => $this->sms->send($customer->phone ?? '', $body, $variables),
        };

        return CampaignLog::create([
            'customer_id' => $customer->id,
            'channel' => $channel,
            'status' => $result->successful ? MessageStatus::Sent : MessageStatus::Failed,
            'provider' => $result->provider,
            'payload' => [
                'to' => $channel === MessageChannel::Email ? $customer->email : $customer->phone,
                'subject' => $subject,
                'body' => $body,
            ],
            'response' => $result->response,
            'error' => $result->error,
            'sent_at' => $result->successful ? now() : null,
        ]);
    }
}
