<?php

namespace App\Domains\Social\Providers;

use App\Domains\Social\Contracts\SocialPublisher;
use App\Domains\Social\Data\SocialPublishResult;
use App\Domains\Social\Models\SocialPost;

class MockInstagramPublisher implements SocialPublisher
{
    public function publish(SocialPost $post): SocialPublishResult
    {
        return new SocialPublishResult(
            successful: true,
            provider: 'mock_instagram',
            externalPostId: 'ig_'.sha1($post->id.$post->content.microtime(true)),
            engagement: [
                'likes_count' => 0,
                'comments_count' => 0,
                'shares_count' => 0,
                'reach_count' => 0,
                'clicks_count' => 0,
            ],
            response: ['platform' => 'instagram'],
        );
    }
}
