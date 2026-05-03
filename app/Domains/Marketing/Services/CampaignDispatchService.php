<?php

namespace App\Domains\Marketing\Services;

use App\Domains\CRM\Models\Customer;
use App\Domains\Marketing\Contracts\EmailProvider;
use App\Domains\Marketing\Contracts\SmsProvider;
use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Enums\CampaignType;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Enums\MessageStatus;
use App\Domains\Marketing\Jobs\SendCampaignMessageJob;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Marketing\Models\CampaignLog;
use App\Domains\Marketing\Models\CampaignRecipient;
use App\Domains\Marketing\Models\CampaignTemplate;
use Throwable;

class CampaignDispatchService
{
    public function __construct(
        private readonly CampaignRecipientBuilder $recipients,
        private readonly TemplateRenderer $renderer,
        private readonly EmailProvider $email,
        private readonly SmsProvider $sms,
    ) {
    }

    public function dispatch(Campaign $campaign, bool $queued = true): void
    {
        if ($campaign->recipients()->doesntExist()) {
            $this->recipients->build($campaign);
        }

        $campaign->forceFill([
            'status' => CampaignStatus::Running,
            'started_at' => now(),
        ])->save();

        $campaign->recipients()
            ->where('status', MessageStatus::Pending->value)
            ->each(function (CampaignRecipient $recipient) use ($queued): void {
                $queued
                    ? SendCampaignMessageJob::dispatch($recipient->id)
                    : $this->sendRecipient($recipient);
            });

        if (! $queued) {
            $this->completeIfFinished($campaign->refresh());
        }
    }

    public function sendRecipient(CampaignRecipient $recipient): void
    {
        $recipient->loadMissing(['campaign.templates', 'customer']);
        $campaign = $recipient->campaign;

        try {
            foreach ($this->channelsFor($campaign) as $channel) {
                $template = $this->templateFor($campaign, $channel);
                $this->sendChannel($recipient, $template, $channel);
            }

            $recipient->forceFill([
                'status' => MessageStatus::Sent,
                'sent_at' => now(),
                'error' => null,
            ])->save();
        } catch (Throwable $exception) {
            $recipient->forceFill([
                'status' => MessageStatus::Failed,
                'error' => $exception->getMessage(),
            ])->save();

            CampaignLog::create([
                'campaign_id' => $campaign->id,
                'campaign_recipient_id' => $recipient->id,
                'customer_id' => $recipient->customer_id,
                'channel' => MessageChannel::Email,
                'status' => MessageStatus::Failed,
                'error' => $exception->getMessage(),
            ]);
        }

        $this->completeIfFinished($campaign->refresh());
    }

    private function sendChannel(CampaignRecipient $recipient, CampaignTemplate $template, MessageChannel $channel): CampaignLog
    {
        $customer = $recipient->customer;
        $variables = $this->variablesFor($customer);
        $body = $this->renderer->render($template->body, $variables);
        $subject = $template->subject ? $this->renderer->render($template->subject, $variables) : null;

        $result = match ($channel) {
            MessageChannel::Email => $this->email->send($recipient->email ?? $customer->email, $subject ?? $template->name, $body, $variables),
            MessageChannel::Sms => $this->sms->send($recipient->phone ?? $customer->phone ?? '', $body, $variables),
        };

        return CampaignLog::create([
            'campaign_id' => $recipient->campaign_id,
            'campaign_recipient_id' => $recipient->id,
            'customer_id' => $customer->id,
            'channel' => $channel,
            'status' => $result->successful ? MessageStatus::Sent : MessageStatus::Failed,
            'provider' => $result->provider,
            'payload' => [
                'to' => $channel === MessageChannel::Email ? ($recipient->email ?? $customer->email) : ($recipient->phone ?? $customer->phone),
                'subject' => $subject,
                'body' => $body,
            ],
            'response' => $result->response,
            'error' => $result->error,
            'sent_at' => $result->successful ? now() : null,
        ]);
    }

    /**
     * @return array<int, MessageChannel>
     */
    private function channelsFor(Campaign $campaign): array
    {
        return match ($campaign->type) {
            CampaignType::Email => [MessageChannel::Email],
            CampaignType::Sms => [MessageChannel::Sms],
            CampaignType::Mixed => [MessageChannel::Email, MessageChannel::Sms],
        };
    }

    private function templateFor(Campaign $campaign, MessageChannel $channel): CampaignTemplate
    {
        return $campaign->templates->firstWhere('channel', $channel)
            ?? CampaignTemplate::whereNull('campaign_id')->where('template_key', $channel->value.'_default')->firstOrFail();
    }

    /**
     * @return array<string, mixed>
     */
    private function variablesFor(Customer $customer): array
    {
        return [
            'customer_name' => $customer->contact_name,
            'company_name' => $customer->company_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
        ];
    }

    private function completeIfFinished(Campaign $campaign): void
    {
        if ($campaign->recipients()->where('status', MessageStatus::Pending->value)->exists()) {
            return;
        }

        $campaign->forceFill([
            'status' => $campaign->recipients()->where('status', MessageStatus::Failed->value)->exists()
                ? CampaignStatus::Failed
                : CampaignStatus::Completed,
            'completed_at' => now(),
        ])->save();
    }
}
