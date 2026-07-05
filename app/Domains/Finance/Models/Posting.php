<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;

class Posting extends Model
{
    const UPDATED_AT = null; // Enforce no update timestamps

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'type', // debit, credit
        'amount',
        'currency',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Enforce immutability: Cannot update a Posting once created.
     */
    public function update(array $attributes = [], array $options = [])
    {
        throw new Exception("Immutable Ledger: Postings cannot be updated. You must create a reversing entry.");
    }

    /**
     * Enforce immutability: Cannot delete a Posting once created.
     */
    public function delete()
    {
        throw new Exception("Immutable Ledger: Postings cannot be deleted. You must create a reversing entry.");
    }
}
