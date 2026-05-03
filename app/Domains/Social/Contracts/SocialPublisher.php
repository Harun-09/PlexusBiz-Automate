<?php

namespace App\Domains\Social\Contracts;

use App\Domains\Social\Data\SocialPublishResult;
use App\Domains\Social\Models\SocialPost;

interface SocialPublisher
{
    public function publish(SocialPost $post): SocialPublishResult;
}
