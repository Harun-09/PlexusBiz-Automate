<?php

namespace Tests\Feature;

use App\Domains\Finance\Models\Account;
use App\Domains\Finance\Models\JournalEntry;
use App\Domains\Tax\Events\VatRecorded;
use App\Domains\Finance\Listeners\RecordVatLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordVatLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_vat_recorded_creates_journal_entry()
    {
        $listener = app(RecordVatLedger::class);

        $payload = [
            'order_id' => 999,
            'tax_invoice_number' => 'INV-VAT-999',
            'total_vat_amount' => 150.50,
        ];

        $event = new VatRecorded($payload);
        $listener->handle($event);

        $this->assertDatabaseHas('journal_entries', [
            'idempotency_key' => 'vat_ledger_order_999',
            'reference' => 'INV-VAT-999',
        ]);

        $journalEntry = JournalEntry::where('idempotency_key', 'vat_ledger_order_999')->first();
        
        $expenseAccount = Account::where('code', '5010')->first();
        $payableAccount = Account::where('code', '2010')->first();

        $this->assertNotNull($expenseAccount);
        $this->assertNotNull($payableAccount);

        $this->assertDatabaseHas('postings', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $expenseAccount->id,
            'type' => 'debit',
            'amount' => 150.50,
        ]);

        $this->assertDatabaseHas('postings', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $payableAccount->id,
            'type' => 'credit',
            'amount' => 150.50,
        ]);
        
        // Ensure balance is updated
        $this->assertDatabaseHas('account_balances', [
            'account_id' => $expenseAccount->id,
            'balance' => 150.50,
        ]);
        
        $this->assertDatabaseHas('account_balances', [
            'account_id' => $payableAccount->id,
            'balance' => 150.50,
        ]);
    }
}
