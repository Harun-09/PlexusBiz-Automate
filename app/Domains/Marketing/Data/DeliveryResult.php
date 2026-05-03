<?php

namespace App\Domains\Marketing\Data;

class DeliveryResult
{
    /**
     * @param array<string, mixed> $response
     */
    public function __construct(
        public readonly bool $successful,
        public readonly string $provider,
        public readonly array $response = [],
        public readonly ?string $error = null,
    ) {
    }
}
