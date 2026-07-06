<?php

namespace App\Domains\Finance\Listeners;

use App\Domains\Tax\Events\VatRecorded;
use App\Domains\Finance\Services\LedgerService;
use App\Domains\Finance\Models\Account;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordVatLedger implements ShouldQueue
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function handle(VatRecorded $event)
    {
        $payload = $event->payload;
        
        $orderId = $payload['order_id'];
        $taxInvoiceNumber = $payload['tax_invoice_number'];
        $totalVatAmount = $payload['total_vat_amount'];

        if ($totalVatAmount <= 0) {
            return;
        }

        // Ideally, fetch accounts using a ChartOfAccounts configuration or constants.
        // For this demo, we assume specific account codes or we fetch them dynamically.
        // Let's assume:
        // VAT Expense Account (or VAT Receivable if we paid it, but if it's collected on sales, it's VAT Payable)
        // Here we are generating Mushak for sales, so we collected VAT, so we Debit Accounts Receivable (or Cash) and Credit VAT Payable (Liability).
        // Wait, the sales entry itself might handle AR/Cash. The VAT part might just be recorded as:
        // Debit: VAT Expense (if input VAT) / AR (if output VAT). 
        // For simplicity as requested in the plan: "debiting VAT expense, crediting VAT payable" 
        
        $vatExpenseAccount = Account::firstOrCreate(
            ['code' => '5010'],
            ['name' => 'VAT Expense', 'type' => 'expense', 'normal_balance' => 'debit']
        );

        $vatPayableAccount = Account::firstOrCreate(
            ['code' => '2010'],
            ['name' => 'VAT Payable', 'type' => 'liability', 'normal_balance' => 'credit']
        );

        $idempotencyKey = "vat_ledger_order_{$orderId}";
        $description = "VAT Recorded for Order #{$orderId}, Invoice {$taxInvoiceNumber}";
        
        $postings = [
            [
                'account_id' => $vatExpenseAccount->id,
                'type' => 'debit',
                'amount' => $totalVatAmount,
            ],
            [
                'account_id' => $vatPayableAccount->id,
                'type' => 'credit',
                'amount' => $totalVatAmount,
            ]
        ];

        $this->ledgerService->recordEntry($idempotencyKey, $description, $postings, $taxInvoiceNumber);
    }
}
