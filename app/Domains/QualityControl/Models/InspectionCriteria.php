<?php

namespace App\Domains\QualityControl\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionCriteria extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'inspection_criteria';

    public function inspection()
    {
        return $this->belongsTo(QualityInspection::class, 'quality_inspection_id');
    }
}
