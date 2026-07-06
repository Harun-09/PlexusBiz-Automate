<?php

namespace App\Domains\Tax\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VatRecorded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    /**
     * Create a new event instance.
     *
     * @param array $payload Contains: order_id, tax_invoice_number, total_vat_amount
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}
