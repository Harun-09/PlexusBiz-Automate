<?php

namespace Tests\Feature\Marketing;

use App\Domains\CRM\Enums\CustomerLifecycleStage;
use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Domains\Marketing\Services\MarketingTriggerService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingTriggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_customer_welcome_trigger_sends_from_template(): void
    {
        $customer = $this->customer();

        CampaignTemplate::create([
            'template_key' => 'new_customer_welcome',
            'channel' => MessageChannel::Email,
            'name' => 'Welcome',
            'subject' => 'Welcome {{ customer_name }}',
            'body' => 'Hello {{ customer_name }}, your account is ready.',
            'variables' => ['customer_name'],
            'status' => 'active',
        ]);

        $log = app(MarketingTriggerService::class)->welcomeCustomer($customer);

        $this->assertSame('mock_email', $log->provider);
        $this->assertSame($customer->id, $log->customer_id);
        $this->assertSame('Hello Trigger Buyer, your account is ready.', $log->payload['body']);
    }

    private function customer(): Customer
    {
        $user = User::factory()->create();

        return Customer::create([
            'user_id' => $user->id,
            'contact_name' => 'Trigger Buyer',
            'email' => $user->email,
            'status' => CustomerStatus::Active,
            'lifecycle_stage' => CustomerLifecycleStage::Customer,
            'tags' => [],
            'last_activity_at' => now(),
        ]);
    }
}
