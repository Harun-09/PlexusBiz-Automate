<?php

namespace App\Domains\QualityControl\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonConformanceReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function inspection()
    {
        return $this->belongsTo(QualityInspection::class, 'quality_inspection_id');
    }
}
