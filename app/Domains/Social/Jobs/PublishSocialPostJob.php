<?php

namespace App\Domains\Social\Jobs;

use App\Domains\Social\Models\SocialPost;
use App\Domains\Social\Services\SocialPostPublisherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishSocialPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(private readonly int $postId)
    {
    }

    public function handle(SocialPostPublisherService $publisher): void
    {
        $publisher->publish(SocialPost::findOrFail($this->postId));
    }
}
