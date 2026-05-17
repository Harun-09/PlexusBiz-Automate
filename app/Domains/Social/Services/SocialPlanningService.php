<?php

namespace App\Domains\Social\Services;

use App\Domains\Social\Models\ContentCalendar;
use App\Domains\Social\Models\EngagementLog;
use App\Domains\Social\Models\SocialCampaign;
use App\Domains\Social\Models\SocialPost;
use Illuminate\Support\Str;

class SocialPlanningService
{
    public function syncPostArtifacts(SocialPost $post, ?int $actorId = null): void
    {
        $post->loadMissing(['campaign', 'socialCampaign']);

        $socialCampaign = $this->ensureSocialCampaign($post, $actorId);

        ContentCalendar::updateOrCreate(
            ['social_post_id' => $post->id],
            [
                'social_campaign_id' => $socialCampaign?->id,
                'platform' => $post->platform->value,
                'title' => Str::limit(trim($post->content), 120, ''),
                'content' => $post->content,
                'scheduled_for' => $post->scheduled_at,
                'status' => $post->status->value,
                'published_at' => $post->published_at,
                'metadata' => [
                    'campaign_id' => $post->campaign_id,
                    'social_account_id' => $post->social_account_id,
                    'external_post_id' => $post->external_post_id,
                ],
            ],
        );

        $this->syncEngagementMetrics($post, $socialCampaign);
    }

    private function ensureSocialCampaign(SocialPost $post, ?int $actorId = null): ?SocialCampaign
    {
        if (! $post->campaign_id) {
            return null;
        }

        $campaignName = $post->campaign?->name ?: 'Social Campaign '.$post->campaign_id;

        return SocialCampaign::updateOrCreate(
            ['campaign_id' => $post->campaign_id],
            [
                'created_by' => $actorId,
                'name' => $campaignName,
                'objective' => $post->campaign?->name ? 'Post distribution for '.$post->campaign->name : null,
                'status' => $post->status->value,
                'start_at' => $post->scheduled_at,
                'end_at' => $post->published_at,
                'metadata' => [
                    'platform' => $post->platform->value,
                ],
            ],
        );
    }

    private function syncEngagementMetrics(SocialPost $post, ?SocialCampaign $socialCampaign): void
    {
        $metrics = [
            'likes' => (int) ($post->likes_count ?? 0),
            'comments' => (int) ($post->comments_count ?? 0),
            'shares' => (int) ($post->shares_count ?? 0),
            'reach' => (int) ($post->reach_count ?? 0),
            'clicks' => (int) ($post->clicks_count ?? 0),
        ];

        foreach ($metrics as $metricType => $metricValue) {
            EngagementLog::updateOrCreate(
                [
                    'social_post_id' => $post->id,
                    'metric_type' => $metricType,
                ],
                [
                    'social_campaign_id' => $socialCampaign?->id,
                    'platform' => $post->platform->value,
                    'metric_value' => $metricValue,
                    'recorded_at' => now(),
                    'metadata' => [
                        'status' => $post->status->value,
                    ],
                ],
            );
        }
    }
}

