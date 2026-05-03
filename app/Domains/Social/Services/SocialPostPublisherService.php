<?php

namespace App\Domains\Social\Services;

use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialPost;
use Throwable;

class SocialPostPublisherService
{
    public function __construct(private readonly SocialPublisherManager $publishers)
    {
    }

    public function publish(SocialPost $post): SocialPost
    {
        $post->refresh();

        if (! in_array($post->status, [SocialPostStatus::Scheduled, SocialPostStatus::Draft], true)) {
            return $post;
        }

        try {
            $result = $this->publishers->for($post->platform)->publish($post);

            $post->forceFill([
                'status' => $result->successful ? SocialPostStatus::Published : SocialPostStatus::Failed,
                'external_post_id' => $result->externalPostId,
                'published_at' => $result->successful ? now() : null,
                'failure_reason' => $result->error,
                'likes_count' => $result->engagement['likes_count'] ?? 0,
                'comments_count' => $result->engagement['comments_count'] ?? 0,
                'shares_count' => $result->engagement['shares_count'] ?? 0,
                'reach_count' => $result->engagement['reach_count'] ?? 0,
                'clicks_count' => $result->engagement['clicks_count'] ?? 0,
            ])->save();
        } catch (Throwable $exception) {
            $post->forceFill([
                'status' => SocialPostStatus::Failed,
                'failure_reason' => $exception->getMessage(),
            ])->save();
        }

        return $post->refresh();
    }
}
