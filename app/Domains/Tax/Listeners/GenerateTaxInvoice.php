<?php

namespace App\Domains\Tax\Listeners;

use App\Domains\ECommerce\Events\OrderPlaced;
use App\Domains\Tax\Services\MushakGenerator;
use App\Domains\Core\Services\OutboxService;
use App\Domains\Tax\Events\VatRecorded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateTaxInvoice implements ShouldQueue
{
    use InteractsWithQueue;

    protected MushakGenerator $mushakGenerator;
    protected OutboxService $outboxService;

    public function __construct(MushakGenerator $mushakGenerator, OutboxService $outboxService)
    {
        $this->mushakGenerator = $mushakGenerator;
        $this->outboxService = $outboxService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        try {
            DB::transaction(function () use ($event) {
                // Generate Mushak 6.3 PDF & record
                $taxInvoice = $this->mushakGenerator->generateMushak63($event->order);
                
                // Get the total vat from the generated mushak document
                $mushakDoc = $taxInvoice->mushakDocuments()->where('form_type', '6.3')->first();
                $totalVat = $mushakDoc ? $mushakDoc->total_vat_amount : 0.0;

                // Create Mushak 6.2 (Sales Account Book) record
                \App\Domains\Tax\Models\MushakRecord::create([
                    'book_type' => '6.2',
                    'reference_id' => $event->order->id,
                    'reference_type' => get_class($event->order),
                    'amount' => $event->order->grand_total ?? 0,
                    'vat_amount' => $totalVat,
                    'vds_amount' => 0.0, // Assuming customer does not deduct VDS from us for standard sales in this flow, or handled via payment
                ]);

                if ($totalVat > 0) {
                    // Send to Outbox for Finance ledger
                    $this->outboxService->saveEvent(VatRecorded::class, [
                        'order_id' => $event->order->id,
                        'tax_invoice_number' => $taxInvoice->tax_invoice_number,
                        'total_vat_amount' => $totalVat,
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::error("Failed to generate Tax Invoice for Order ID {$event->order->id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
