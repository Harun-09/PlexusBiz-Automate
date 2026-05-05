<?php

namespace Tests\Feature\Marketing;

use App\Domains\Marketing\Models\Campaign;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarketingCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_manager_can_open_campaign_and_template_crud_pages(): void
    {
        $this->seed(DatabaseSeeder::class);

        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();

        $this->actingAs($marketing)
            ->get('/marketing/campaigns/create')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketing/Campaigns/Create')
                ->where('campaignTypes.0', 'email')
                ->where('statuses.0', 'draft'));

        $this->actingAs($marketing)
            ->get('/marketing/templates/create')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketing/Templates/Create')
                ->where('channels.0', 'email')
                ->where('statuses.0', 'active'));

        $campaign = Campaign::query()->where('slug', 'priority-wholesale-welcome')->firstOrFail();
        $template = CampaignTemplate::query()->where('template_key', 'email_default')->firstOrFail();

        $this->actingAs($marketing)
            ->get('/marketing/campaigns/'.$campaign->id.'/edit')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketing/Campaigns/Edit')
                ->where('campaign.slug', $campaign->slug));

        $this->actingAs($marketing)
            ->get('/marketing/templates/'.$template->id.'/edit')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketing/Templates/Edit')
                ->where('template.template_key', $template->template_key));
    }

    public function test_marketing_manager_can_crud_campaigns_and_templates_from_the_web_and_api(): void
    {
        $this->seed(DatabaseSeeder::class);

        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();
        $campaignName = 'Launch '.Str::upper(Str::random(6));
        $templateName = 'Launch Template '.Str::upper(Str::random(6));

        $this->actingAs($marketing)
            ->post('/marketing/campaigns', [
                'name' => $campaignName,
                'type' => 'email',
                'status' => 'draft',
                'segment_tags' => 'wholesale, eid-buyers',
                'scheduled_at' => '2026-05-12T10:30',
            ])
            ->assertRedirect('/marketing/campaigns');

        $campaign = Campaign::query()->where('name', $campaignName)->firstOrFail();

        $this->assertStringStartsWith('launch-', $campaign->slug);
        $this->assertSame('email', $campaign->type->value);
        $this->assertSame(['tags' => ['wholesale', 'eid-buyers']], $campaign->segment_filters_json);

        $this->actingAs($marketing)
            ->put('/marketing/campaigns/'.$campaign->id, [
                'name' => $campaignName.' Updated',
                'type' => 'email',
                'status' => 'scheduled',
                'segment_tags' => 'wholesale-repeat',
                'scheduled_at' => '2026-05-15T09:15',
            ])
            ->assertRedirect('/marketing/campaigns');

        $campaign->refresh();
        $this->assertSame('scheduled', $campaign->status->value);
        $this->assertSame(['tags' => ['wholesale-repeat']], $campaign->segment_filters_json);

        $this->actingAs($marketing)
            ->post('/marketing/templates', [
                'campaign_id' => $campaign->id,
                'template_key' => 'launch_email_'.Str::lower(Str::random(6)),
                'channel' => 'email',
                'name' => $templateName,
                'subject' => 'Launch update for {{ customer_name }}',
                'body' => 'Hello {{ customer_name }}, our launch is live.',
                'variables' => 'customer_name, company_name',
                'status' => 'active',
            ])
            ->assertRedirect('/marketing/templates');

        $template = CampaignTemplate::query()->where('name', $templateName)->firstOrFail();

        $this->assertSame('email', $template->channel->value);
        $this->assertSame(['customer_name', 'company_name'], $template->variables);

        $this->actingAs($marketing)
            ->put('/marketing/templates/'.$template->id, [
                'campaign_id' => $campaign->id,
                'template_key' => $template->template_key,
                'channel' => 'email',
                'name' => $templateName.' Updated',
                'subject' => 'Updated launch for {{ customer_name }}',
                'body' => 'Hello {{ customer_name }}, launch details are updated.',
                'variables' => 'customer_name, order_number',
                'status' => 'inactive',
            ])
            ->assertRedirect('/marketing/templates');

        $template->refresh();
        $this->assertSame('inactive', $template->status);
        $this->assertSame(['customer_name', 'order_number'], $template->variables);

        $this->actingAs($marketing)
            ->delete('/marketing/templates/'.$template->id)
            ->assertRedirect('/marketing/templates');

        $this->assertSoftDeleted('campaign_templates', [
            'id' => $template->id,
        ]);

        $this->actingAs($marketing)
            ->delete('/marketing/campaigns/'.$campaign->id)
            ->assertRedirect('/marketing/campaigns');

        $this->assertSoftDeleted('campaigns', [
            'id' => $campaign->id,
        ]);
    }

    public function test_marketing_campaign_schedule_round_trips_through_local_datetime_input(): void
    {
        $this->seed(DatabaseSeeder::class);

        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();
        $campaignName = 'Timezone '.Str::upper(Str::random(6));
        $scheduledAt = '2026-05-12T10:30';
        $expectedStored = Carbon::createFromFormat('Y-m-d\TH:i', $scheduledAt, 'Asia/Dhaka')
            ->setTimezone(config('app.timezone', 'UTC'))
            ->toDateTimeString();

        $this->actingAs($marketing)
            ->post('/marketing/campaigns', [
                'name' => $campaignName,
                'type' => 'email',
                'status' => 'scheduled',
                'segment_tags' => 'wholesale',
                'scheduled_at' => $scheduledAt,
            ])
            ->assertRedirect('/marketing/campaigns');

        $campaign = Campaign::query()->where('name', $campaignName)->firstOrFail();

        $this->assertSame($expectedStored, $campaign->scheduled_at?->setTimezone(config('app.timezone', 'UTC'))->toDateTimeString());

        $this->actingAs($marketing)
            ->get('/marketing/campaigns/'.$campaign->id.'/edit')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketing/Campaigns/Edit')
                ->where('campaign.scheduled_at', $scheduledAt));
    }

    public function test_marketing_manager_can_manage_campaigns_and_templates_via_api(): void
    {
        $this->seed(DatabaseSeeder::class);

        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();
        Sanctum::actingAs($marketing);

        $campaignName = 'API Launch '.Str::upper(Str::random(6));
        $campaignResponse = $this->postJson('/api/v1/campaigns', [
            'name' => $campaignName,
            'type' => 'email',
            'status' => 'draft',
            'segment_tags' => 'high-value, wholesale',
            'scheduled_at' => '2026-05-20T11:00',
        ])
            ->assertCreated()
            ->json('data');

        $this->assertSame($campaignName, $campaignResponse['name']);
        $this->assertSame('email', $campaignResponse['type']);

        $campaignId = $campaignResponse['id'];

        $this->putJson('/api/v1/campaigns/'.$campaignId, [
            'name' => $campaignName.' Updated',
            'type' => 'email',
            'status' => 'scheduled',
            'segment_tags' => 'high-value',
            'scheduled_at' => '2026-05-21T09:30',
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'scheduled');

        $templateResponse = $this->postJson('/api/v1/campaign-templates', [
            'campaign_id' => $campaignId,
            'template_key' => 'api_launch_'.Str::lower(Str::random(6)),
            'name' => 'API Launch Template',
            'subject' => 'API Launch for {{ customer_name }}',
            'body' => 'Hello {{ customer_name }}, API launch is ready.',
            'variables' => 'customer_name, company_name',
            'status' => 'active',
        ])
            ->assertCreated()
            ->json('data');

        $this->assertSame('API Launch Template', $templateResponse['name']);
        $this->assertSame('email', $templateResponse['channel']);

        $templateId = $templateResponse['id'];

        $this->putJson('/api/v1/campaign-templates/'.$templateId, [
            'campaign_id' => $campaignId,
            'template_key' => $templateResponse['template_key'],
            'name' => 'API Launch Template Updated',
            'subject' => 'Updated API Launch for {{ customer_name }}',
            'body' => 'Hello {{ customer_name }}, updated API launch copy.',
            'variables' => 'customer_name, order_number',
            'status' => 'inactive',
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');

        $this->deleteJson('/api/v1/campaign-templates/'.$templateId)
            ->assertOk();

        $this->assertSoftDeleted('campaign_templates', [
            'id' => $templateId,
        ]);

        $this->deleteJson('/api/v1/campaigns/'.$campaignId)
            ->assertOk();

        $this->assertSoftDeleted('campaigns', [
            'id' => $campaignId,
        ]);
    }
}
