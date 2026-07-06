<?php

namespace App\Domains\Tax\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseRecorded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    /**
     * Create a new event instance.
     *
     * @param array $payload Contains: purchase_id, amount, vat_amount, vds_amount
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}
