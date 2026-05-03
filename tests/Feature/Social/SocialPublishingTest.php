<?php

namespace Tests\Feature\Social;

use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialAccount;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Social\Services\ContentCalendarService;
use App\Domains\Social\Services\SocialScheduleService;
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

    private function socialPost(SocialPlatform $platform, $scheduledAt): SocialPost
    {
        $account = SocialAccount::create([
            'platform' => $platform,
            'name' => $platform->value.' account',
            'handle' => '@'.$platform->value,
            'status' => 'active',
            'credentials_json' => ['mode' => 'mock'],
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
