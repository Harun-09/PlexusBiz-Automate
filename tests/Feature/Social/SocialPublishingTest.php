<?php

namespace Tests\Feature\Social;

use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialAccount;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Social\Services\ContentCalendarService;
use App\Domains\Social\Services\SocialScheduleService;
use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Models\AutomationRule;
use App\Domains\Workflow\Models\WorkflowLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_due_social_posts_publish_through_mock_provider(): void
    {
        $post = $this->socialPost(SocialPlatform::Facebook, now()->subMinute());

        $count = app(SocialScheduleService::class)->dispatchDuePosts(queued: false);

        $post->refresh();

        $this->assertSame(1, $count);
        $this->assertSame(SocialPostStatus::Published, $post->status);
        $this->assertNotNull($post->external_post_id);
        $this->assertNotNull($post->published_at);
    }

    public function test_content_calendar_returns_posts_in_date_range(): void
    {
        $included = $this->socialPost(SocialPlatform::Instagram, now()->addDay());
        $this->socialPost(SocialPlatform::Facebook, now()->addMonth());

        $posts = app(ContentCalendarService::class)->between(now(), now()->addWeek());

        $this->assertSame([$included->id], $posts->pluck('id')->all());
    }

    public function test_due_social_posts_trigger_workflow_rules(): void
    {
        AutomationRule::create([
            'name' => 'Social post due alert',
            'trigger_event' => WorkflowTriggerEvent::SocialPostDue->value,
            'conditions_json' => [],
            'actions_json' => [],
            'status' => AutomationRuleStatus::Active,
            'priority' => 1,
            'run_async' => false,
        ]);

        $post = $this->socialPost(SocialPlatform::Facebook, now()->subMinute());

        $count = app(SocialScheduleService::class)->dispatchDuePosts(queued: false);

        $post->refresh();

        $this->assertSame(1, $count);
        $this->assertSame(SocialPostStatus::Published, $post->status);
        $this->assertSame(1, WorkflowLog::where('trigger_event', WorkflowTriggerEvent::SocialPostDue->value)->count());
        $this->assertDatabaseHas('workflow_logs', [
            'trigger_event' => WorkflowTriggerEvent::SocialPostDue->value,
        ]);
    }

    public function test_artisan_publish_due_command_processes_due_posts_in_process(): void
    {
        $post = $this->socialPost(SocialPlatform::Facebook, now()->subMinute());

        $this->artisan('social-posts:publish-due')
            ->expectsOutput('Published 1 due social post.')
            ->assertSuccessful();

        $post->refresh();

        $this->assertSame(SocialPostStatus::Published, $post->status);
        $this->assertNotNull($post->published_at);
    }

    private function socialPost(SocialPlatform $platform, $scheduledAt): SocialPost
    {
        $account = SocialAccount::create([
            'platform' => $platform,
            'name' => $platform->value.' account',
            'handle' => '@'.$platform->value,
            'status' => 'active',
            'credentials_json' => [
                'mode' => 'mock',
                'page_id' => $platform === SocialPlatform::Facebook ? '123456789012345' : null,
                'access_token' => $platform === SocialPlatform::Facebook ? 'unit-test-facebook-token' : null,
            ],
        ]);

        return SocialPost::create([
            'social_account_id' => $account->id,
            'platform' => $platform,
            'content' => 'Scheduled content for '.$platform->value,
            'scheduled_at' => $scheduledAt,
            'status' => SocialPostStatus::Scheduled,
        ]);
    }
}
