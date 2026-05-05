<?php

namespace App\Domains\ECommerce\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RfqCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(private readonly array $payload)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function payloadSnapshot(): array
    {
        return $this->payload;
    }
}
