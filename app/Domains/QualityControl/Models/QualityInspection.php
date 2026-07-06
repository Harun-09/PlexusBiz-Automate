<?php

namespace App\Domains\QualityControl\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityInspection extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    public function criteria()
    {
        return $this->hasMany(InspectionCriteria::class);
    }

    public function nonConformanceReports()
    {
        return $this->hasMany(NonConformanceReport::class);
    }
}
