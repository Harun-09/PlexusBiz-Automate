<?php

namespace App\Domains\AssetManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetTransfer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class);
    }
}
