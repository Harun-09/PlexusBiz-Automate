<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Domains\ECommerce\Services\Logistics\CourierWebhookStateEngine;

class ProcessCourierWebhookJob implements ShouldQueue
{
    use Queueable;

    public string $courier;
    public array $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(string $courier, array $payload)
    {
        $this->courier = $courier;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(CourierWebhookStateEngine $engine): void
    {
        $engine->processWebhook($this->courier, $this->payload);
    }
}
