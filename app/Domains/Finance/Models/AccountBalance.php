<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class AccountBalance extends Model
{
    protected $fillable = [
        'account_id',
        'balance',
        'last_calculated_at',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
