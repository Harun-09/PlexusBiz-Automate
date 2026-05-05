<?php

namespace Tests\Feature\Marketing;

use App\Domains\CRM\Enums\CustomerLifecycleStage;
use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Enums\CampaignType;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Mail\MarketingCampaignMail;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Marketing\Models\CampaignLog;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Domains\Marketing\Services\CampaignDispatchService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CampaignDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_campaign_dispatch_builds_recipients_sends_message_and_logs_delivery(): void
    {
        Mail::fake();

        $customer = $this->customer(tags: ['priority', 'wholesale']);
        $campaign = Campaign::create([
            'name' => 'Wholesale Follow Up',
            'slug' => 'wholesale-follow-up-'.Str::random(8),
            'type' => CampaignType::Email,
            'status' => CampaignStatus::Draft,
            'segment_filters_json' => ['tags' => ['priority']],
        ]);

        CampaignTemplate::create([
            'campaign_id' => $campaign->id,
            'channel' => MessageChannel::Email,
            'name' => 'Wholesale Email',
            'subject' => 'Hello {{ customer_name }}',
            'body' => 'Hi {{ customer_name }}, pricing is ready for {{ company_name }}.',
            'variables' => ['customer_name', 'company_name'],
            'status' => 'active',
        ]);

        app(CampaignDispatchService::class)->dispatch($campaign, queued: false);

        $campaign->refresh();
        $log = CampaignLog::firstOrFail();

        Mail::assertSent(MarketingCampaignMail::class, function (MarketingCampaignMail $mail) use ($customer): bool {
            return $mail->subjectLine === 'Hello Acme Buyer'
                && str_contains($mail->body, 'Hi Acme Buyer, pricing is ready for Acme Wholesale.');
        });

        $this->assertSame(CampaignStatus::Completed, $campaign->status);
        $this->assertSame($customer->id, $log->customer_id);
        $this->assertSame(config('mail.default'), $log->provider);
        $this->assertStringContainsString('pricing is ready', $log->payload['body']);
        $this->assertDatabaseHas('campaign_recipients', [
            'campaign_id' => $campaign->id,
            'customer_id' => $customer->id,
            'status' => 'sent',
        ]);
    }

    private function customer(array $tags): Customer
    {
        $user = User::factory()->create([
            'account_type' => 'buyer',
        ]);

        return Customer::create([
            'user_id' => $user->id,
            'company_name' => 'Acme Wholesale',
            'contact_name' => 'Acme Buyer',
            'email' => $user->email,
            'phone' => '+8801700000101',
            'status' => CustomerStatus::Active,
            'lifecycle_stage' => CustomerLifecycleStage::Customer,
            'tags' => $tags,
            'last_activity_at' => now(),
        ]);
    }
}
