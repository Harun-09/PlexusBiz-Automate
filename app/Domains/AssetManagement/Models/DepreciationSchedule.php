<?php

namespace App\Domains\AssetManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepreciationSchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'period_date' => 'date',
        'depreciation_amount' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'book_value' => 'decimal:2',
    ];

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class);
    }
}
