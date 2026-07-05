<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;

class JournalEntry extends Model
{
    const UPDATED_AT = null; // Enforce no update timestamps

    protected $fillable = [
        'reference',
        'description',
        'idempotency_key',
    ];

    public function postings()
    {
        return $this->hasMany(Posting::class);
    }

    /**
     * Enforce immutability: Cannot update a JournalEntry once created.
     */
    public function update(array $attributes = [], array $options = [])
    {
        throw new Exception("Immutable Ledger: Journal Entries cannot be updated. You must create a reversing entry.");
    }

    /**
     * Enforce immutability: Cannot delete a JournalEntry once created.
     */
    public function delete()
    {
        throw new Exception("Immutable Ledger: Journal Entries cannot be deleted. You must create a reversing entry.");
    }
}
