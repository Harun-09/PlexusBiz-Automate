<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type', // asset, liability, equity, revenue, expense
        'normal_balance', // debit, credit
        'description',
        'is_active',
    ];

    public function postings()
    {
        return $this->hasMany(Posting::class);
    }

    public function balance()
    {
        return $this->hasOne(AccountBalance::class);
    }
}
