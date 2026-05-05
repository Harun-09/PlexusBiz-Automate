<?php

namespace App\Domains\Social\Services;

use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Jobs\PublishSocialPostJob;
use App\Domains\Social\Models\SocialPost;

class SocialScheduleService
{
    public function dispatchDuePosts(bool $queued = true): int
    {
        $count = 0;

        SocialPost::query()
            ->where('status', SocialPostStatus::Scheduled->value)
            ->where(function ($query): void {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
            ->each(function (SocialPost $post) use ($queued, &$count): void {
                $queued
                    ? PublishSocialPostJob::dispatch($post->id)
                    : app(SocialPostPublisherService::class)->publish($post);

                $count++;
            });

        return $count;
    }
}
