<?php

namespace App\Domains\Finance\Services;

use App\Domains\Finance\Models\Account;
use App\Domains\Finance\Models\JournalEntry;
use App\Domains\Finance\Models\Posting;
use Illuminate\Support\Facades\DB;
use Exception;

class LedgerService
{
    /**
     * Record a balanced journal entry.
     * 
     * @param string $idempotencyKey Unique key to prevent duplicates
     * @param string $description Description of the entry
     * @param array $postings Array of posting data: [['account_id' => 1, 'type' => 'debit', 'amount' => 100], ...]
     * @param string|null $reference Optional reference code
     * @return JournalEntry
     * @throws Exception
     */
    public function recordEntry(string $idempotencyKey, string $description, array $postings, ?string $reference = null): JournalEntry
    {
        return DB::transaction(function () use ($idempotencyKey, $description, $postings, $reference) {
            // Check for idempotency
            $existing = JournalEntry::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }

            // Calculate debits and credits
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($postings as $posting) {
                if (!isset($posting['account_id'], $posting['type'], $posting['amount'])) {
                    throw new Exception("Invalid posting format.");
                }
                if ($posting['type'] === 'debit') {
                    $totalDebit += (float) $posting['amount'];
                } elseif ($posting['type'] === 'credit') {
                    $totalCredit += (float) $posting['amount'];
                } else {
                    throw new Exception("Invalid posting type.");
                }
            }

            // Enforce Double Entry Rule
            if (round($totalDebit, 6) !== round($totalCredit, 6)) {
                throw new Exception("Journal Entry unbalanced. Total Debits ({$totalDebit}) must equal Total Credits ({$totalCredit}).");
            }

            // Create Journal Entry
            $journalEntry = JournalEntry::create([
                'idempotency_key' => $idempotencyKey,
                'description' => $description,
                'reference' => $reference,
            ]);

            // Create Postings and update materialized view
            foreach ($postings as $postingData) {
                $posting = $journalEntry->postings()->create([
                    'account_id' => $postingData['account_id'],
                    'type' => $postingData['type'],
                    'amount' => $postingData['amount'],
                    'currency' => $postingData['currency'] ?? 'BDT',
                ]);

                $this->updateAccountBalance($posting->account_id);
            }

            return $journalEntry;
        });
    }

    /**
     * Update the materialized view for the account balance.
     */
    protected function updateAccountBalance(int $accountId)
    {
        $account = Account::findOrFail($accountId);
        
        $debits = Posting::where('account_id', $accountId)->where('type', 'debit')->sum('amount');
        $credits = Posting::where('account_id', $accountId)->where('type', 'credit')->sum('amount');
        
        // Normal Balance Logic:
        // Asset/Expense normally Debit. So Balance = Debits - Credits.
        // Liability/Equity/Revenue normally Credit. So Balance = Credits - Debits.
        if (in_array($account->normal_balance, ['debit'])) {
            $balance = $debits - $credits;
        } else {
            $balance = $credits - $debits;
        }

        $account->balance()->updateOrCreate(
            ['account_id' => $accountId],
            [
                'balance' => $balance,
                'last_calculated_at' => now(),
            ]
        );
    }
}
