<?php

namespace Tests\Feature;

use App\Domains\Finance\Models\Account;
use App\Domains\Finance\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoubleEntryBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_ledger_service_requires_balanced_entries()
    {
        $cash = Account::create([
            'name' => 'Cash',
            'code' => '1001',
            'type' => 'asset',
            'normal_balance' => 'debit',
        ]);

        $revenue = Account::create([
            'name' => 'Sales Revenue',
            'code' => '4001',
            'type' => 'revenue',
            'normal_balance' => 'credit',
        ]);

        $ledgerService = new LedgerService();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Journal Entry unbalanced');

        // Unbalanced: Debit 100, Credit 50
        $ledgerService->recordEntry(
            'idempotency_unbalanced',
            'Test unbalanced entry',
            [
                ['account_id' => $cash->id, 'type' => 'debit', 'amount' => 100, 'currency' => 'BDT'],
                ['account_id' => $revenue->id, 'type' => 'credit', 'amount' => 50, 'currency' => 'BDT'],
            ]
        );
    }

    public function test_ledger_service_records_balanced_entry()
    {
        $cash = Account::create([
            'name' => 'Cash',
            'code' => '1001',
            'type' => 'asset',
            'normal_balance' => 'debit',
        ]);

        $revenue = Account::create([
            'name' => 'Sales Revenue',
            'code' => '4001',
            'type' => 'revenue',
            'normal_balance' => 'credit',
        ]);

        $ledgerService = new LedgerService();

        // Balanced: Debit 100, Credit 100
        $entry = $ledgerService->recordEntry(
            'idempotency_balanced',
            'Test balanced entry',
            [
                ['account_id' => $cash->id, 'type' => 'debit', 'amount' => 100, 'currency' => 'BDT'],
                ['account_id' => $revenue->id, 'type' => 'credit', 'amount' => 100, 'currency' => 'BDT'],
            ]
        );

        $this->assertEquals(2, $entry->postings()->count());
        $this->assertEquals(100, $cash->balance->balance); // Debit normal balance
        $this->assertEquals(100, $revenue->balance->balance); // Credit normal balance
    }
}
