<?php

namespace App\Domains\BudgetForecasting\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastEntry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'forecast_month'    => 'date',
        'projected_revenue' => 'decimal:2',
        'projected_expense' => 'decimal:2',
        'actual_revenue'    => 'decimal:2',
        'actual_expense'    => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
