<?php

namespace App\Domains\Social\Services;

use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Services\WorkflowEngineService;
use Throwable;

class SocialPostPublisherService
{
    public function __construct(
        private readonly SocialPublisherManager $publishers,
        private readonly WorkflowEngineService $workflow,
    ) {
    }

    public function publish(SocialPost $post): SocialPost
    {
        $post->refresh();
        $post->loadMissing(['account']);

        if (! in_array($post->status, [SocialPostStatus::Scheduled, SocialPostStatus::Draft], true)) {
            return $post;
        }

        try {
            $result = $this->publishers->forPost($post)->publish($post);

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

            $this->triggerWorkflow($post->refresh());
        } catch (Throwable $exception) {
            $post->forceFill([
                'status' => SocialPostStatus::Failed,
                'failure_reason' => $exception->getMessage(),
            ])->save();

            $this->triggerWorkflow($post->refresh(), $exception->getMessage());
        }

        return $post->refresh();
    }

    private function triggerWorkflow(SocialPost $post, ?string $error = null): void
    {
        $post->loadMissing(['campaign', 'account']);

        $this->workflow->handle(
            WorkflowTriggerEvent::SocialPostDue->value,
            [
                'social_post_id' => $post->id,
                'platform' => $post->platform->value,
                'status' => $post->status->value,
                'scheduled_at' => $post->scheduled_at?->toIso8601String(),
                'published_at' => $post->published_at?->toIso8601String(),
                'content' => $post->content,
                'campaign_id' => $post->campaign_id,
                'campaign_name' => $post->campaign?->name,
                'social_account_id' => $post->social_account_id,
                'social_account_name' => $post->account?->name,
                'likes_count' => $post->likes_count,
                'comments_count' => $post->comments_count,
                'shares_count' => $post->shares_count,
                'reach_count' => $post->reach_count,
                'clicks_count' => $post->clicks_count,
                'failure_reason' => $error ?: $post->failure_reason,
            ],
        );
    }
}
