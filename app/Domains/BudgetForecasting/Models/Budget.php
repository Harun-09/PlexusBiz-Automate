<?php

namespace App\Domains\BudgetForecasting\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $guarded = [];

    protected $casts = [
        'total_allocated' => 'decimal:2',
    ];

    public function lineItems()
    {
        return $this->hasMany(BudgetLineItem::class);
    }

    public function forecastEntries()
    {
        return $this->hasMany(ForecastEntry::class);
    }
}
