<?php

namespace App\Domains\Social\Services;

use App\Domains\Social\Contracts\SocialPublisher;
use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Models\SocialAccount;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Social\Providers\LiveFacebookPublisher;
use App\Domains\Social\Providers\MockFacebookPublisher;
use App\Domains\Social\Providers\MockInstagramPublisher;

class SocialPublisherManager
{
    public function __construct(
        private readonly MockFacebookPublisher $facebook,
        private readonly LiveFacebookPublisher $liveFacebook,
        private readonly MockInstagramPublisher $instagram,
    ) {
    }

    public function forPost(SocialPost $post): SocialPublisher
    {
        return match ($post->platform) {
            SocialPlatform::Facebook => $this->facebookFor($post->account),
            SocialPlatform::Instagram => $this->instagram,
        };
    }

    public function for(SocialPlatform $platform, ?SocialAccount $account = null): SocialPublisher
    {
        return match ($platform) {
            SocialPlatform::Facebook => $this->facebookFor($account),
            SocialPlatform::Instagram => $this->instagram,
        };
    }

    private function facebookFor(?SocialAccount $account = null): SocialPublisher
    {
        return $this->isLiveFacebookAccount($account) ? $this->liveFacebook : $this->facebook;
    }

    private function isLiveFacebookAccount(?SocialAccount $account = null): bool
    {
        $credentials = is_array($account?->credentials_json) ? $account->credentials_json : [];
        $mode = strtolower((string) data_get($credentials, 'mode', config('social.facebook_provider', 'mock')));

        return $mode === 'live';
    }
}
