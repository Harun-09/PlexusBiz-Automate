<?php

namespace App\Domains\Social\Services;

use App\Domains\Social\Contracts\SocialPublisher;
use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Providers\MockFacebookPublisher;
use App\Domains\Social\Providers\MockInstagramPublisher;
use InvalidArgumentException;

class SocialPublisherManager
{
    public function __construct(
        private readonly MockFacebookPublisher $facebook,
        private readonly MockInstagramPublisher $instagram,
    ) {
    }

    public function for(SocialPlatform $platform): SocialPublisher
    {
        return match ($platform) {
            SocialPlatform::Facebook => $this->facebook,
            SocialPlatform::Instagram => $this->instagram,
        };
    }
}
