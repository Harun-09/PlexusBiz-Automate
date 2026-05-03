<?php

namespace Database\Seeders;

use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Enums\CampaignType;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketingSeeder extends Seeder
{
    public function run(): void
    {
        $marketingUser = User::where('email', 'marketing@plexus.test')->firstOrFail();

        CampaignTemplate::updateOrCreate(
            ['template_key' => 'new_customer_welcome'],
            [
                'channel' => MessageChannel::Email,
                'name' => 'New Customer Welcome',
                'subject' => 'Welcome to PlexusBiz, {{ customer_name }}',
                'body' => 'Hello {{ customer_name }}, your B2B buyer workspace is ready.',
                'variables' => ['customer_name', 'company_name'],
                'status' => 'active',
            ],
        );

        CampaignTemplate::updateOrCreate(
            ['template_key' => 'order_confirmation'],
            [
                'channel' => MessageChannel::Email,
                'name' => 'Order Confirmation',
                'subject' => 'Order {{ order_number }} confirmed',
                'body' => 'Hello {{ customer_name }}, order {{ order_number }} has been confirmed. Invoice: {{ invoice_url }}',
                'variables' => ['customer_name', 'order_number', 'invoice_url'],
                'status' => 'active',
            ],
        );

        CampaignTemplate::updateOrCreate(
            ['template_key' => 'abandoned_cart_reminder'],
            [
                'channel' => MessageChannel::Email,
                'name' => 'Abandoned Cart Reminder',
                'subject' => 'Complete your PlexusBiz order',
                'body' => 'Hello {{ customer_name }}, your cart is waiting: {{ abandoned_cart_url }}',
                'variables' => ['customer_name', 'abandoned_cart_url'],
                'status' => 'active',
            ],
        );

        $campaign = Campaign::updateOrCreate(
            ['slug' => 'priority-wholesale-welcome'],
            [
                'created_by' => $marketingUser->id,
                'name' => 'Priority Wholesale Welcome',
                'type' => CampaignType::Email,
                'status' => CampaignStatus::Draft,
                'segment_filters_json' => [
                    'tags' => ['priority', 'wholesale'],
                ],
            ],
        );

        CampaignTemplate::updateOrCreate(
            ['campaign_id' => $campaign->id, 'channel' => MessageChannel::Email->value],
            [
                'name' => 'Priority Wholesale Campaign Email',
                'subject' => 'Priority supply options for {{ company_name }}',
                'body' => 'Hello {{ customer_name }}, your priority wholesale pricing is ready for review.',
                'variables' => ['customer_name', 'company_name'],
                'status' => 'active',
            ],
        );
    }
}
