<?php

namespace Tests\Feature;

use App\Domains\Finance\Models\Account;
use App\Domains\Finance\Models\JournalEntry;
use App\Domains\Finance\Models\Posting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceLedgerImmutabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_journal_entry_cannot_be_updated()
    {
        $entry = JournalEntry::create([
            'reference' => 'TEST-001',
            'description' => 'Test Entry',
            'idempotency_key' => 'unique_key_1',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Immutable Ledger: Journal Entries cannot be updated');

        $entry->update(['description' => 'Changed Description']);
    }

    public function test_journal_entry_cannot_be_deleted()
    {
        $entry = JournalEntry::create([
            'reference' => 'TEST-002',
            'description' => 'Test Entry',
            'idempotency_key' => 'unique_key_2',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Immutable Ledger: Journal Entries cannot be deleted');

        $entry->delete();
    }

    public function test_posting_cannot_be_updated()
    {
        $account = Account::create([
            'name' => 'Cash',
            'code' => '1001',
            'type' => 'asset',
            'normal_balance' => 'debit',
        ]);

        $entry = JournalEntry::create([
            'reference' => 'TEST-003',
            'description' => 'Test Entry',
            'idempotency_key' => 'unique_key_3',
        ]);

        $posting = Posting::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $account->id,
            'type' => 'debit',
            'amount' => 100,
            'currency' => 'BDT',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Immutable Ledger: Postings cannot be updated');

        $posting->update(['amount' => 200]);
    }
}
