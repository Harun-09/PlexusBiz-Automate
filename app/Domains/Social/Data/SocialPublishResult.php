<?php

namespace App\Domains\Social\Data;

class SocialPublishResult
{
    /**
     * @param array<string, int> $engagement
     * @param array<string, mixed> $response
     */
    public function __construct(
        public readonly bool $successful,
        public readonly string $provider,
        public readonly ?string $externalPostId = null,
        public readonly array $engagement = [],
        public readonly array $response = [],
        public readonly ?string $error = null,
    ) {
    }
}
