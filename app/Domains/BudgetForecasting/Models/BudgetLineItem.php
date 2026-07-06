<?php

namespace App\Domains\BudgetForecasting\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetLineItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'spent_amount'     => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
