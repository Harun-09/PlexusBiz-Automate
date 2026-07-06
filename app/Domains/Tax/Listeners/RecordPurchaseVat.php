<?php

namespace App\Domains\Tax\Listeners;

use App\Domains\Tax\Events\PurchaseRecorded;
use App\Domains\Tax\Models\MushakRecord;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordPurchaseVat implements ShouldQueue
{
    public function handle(PurchaseRecorded $event)
    {
        $payload = $event->payload;
        
        MushakRecord::create([
            'book_type' => '6.1',
            'reference_id' => $payload['purchase_id'],
            'reference_type' => 'Purchase', // Mock string for now
            'amount' => $payload['amount'] ?? 0.0,
            'vat_amount' => $payload['vat_amount'] ?? 0.0,
            'vds_amount' => $payload['vds_amount'] ?? 0.0,
        ]);
    }
}
