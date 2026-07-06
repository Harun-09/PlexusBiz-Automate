<?php

namespace App\Domains\AssetManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
    ];

    public function depreciationSchedules()
    {
        return $this->hasMany(DepreciationSchedule::class);
    }

    public function transfers()
    {
        return $this->hasMany(AssetTransfer::class);
    }
}
