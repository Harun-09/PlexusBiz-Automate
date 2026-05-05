<?php

namespace App\Domains\Marketing\Services;

use App\Domains\CRM\Models\Customer;
use App\Domains\Marketing\Contracts\EmailProvider;
use App\Domains\Marketing\Enums\CampaignStatus;
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
            $template = $this->templateFor($campaign);
            $log = $this->sendEmail($recipient, $template);

            $recipient->forceFill([
                'status' => $log->status,
                'sent_at' => $log->status === MessageStatus::Sent ? now() : null,
                'error' => $log->error,
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

    private function sendEmail(CampaignRecipient $recipient, CampaignTemplate $template): CampaignLog
    {
        $customer = $recipient->customer;
        $variables = $this->variablesFor($customer);
        $body = $this->renderer->render($template->body, $variables);
        $subject = $template->subject ? $this->renderer->render($template->subject, $variables) : null;

        $result = $this->email->send($recipient->email ?? $customer->email, $subject ?? $template->name, $body, $variables);

        return CampaignLog::create([
            'campaign_id' => $recipient->campaign_id,
            'campaign_recipient_id' => $recipient->id,
            'customer_id' => $customer->id,
            'channel' => MessageChannel::Email,
            'status' => $result->successful ? MessageStatus::Sent : MessageStatus::Failed,
            'provider' => $result->provider,
            'payload' => [
                'to' => $recipient->email ?? $customer->email,
                'subject' => $subject,
                'body' => $body,
            ],
            'response' => $result->response,
            'error' => $result->error,
            'sent_at' => $result->successful ? now() : null,
        ]);
    }

    private function templateFor(Campaign $campaign): CampaignTemplate
    {
        return $campaign->templates->firstWhere('channel', MessageChannel::Email)
            ?? CampaignTemplate::whereNull('campaign_id')
                ->where('template_key', 'email_default')
                ->where('channel', MessageChannel::Email->value)
                ->firstOrFail();
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
