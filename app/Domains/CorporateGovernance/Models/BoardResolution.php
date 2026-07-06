<?php

namespace App\Domains\CorporateGovernance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardResolution extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function signatures()
    {
        return $this->morphMany(DigitalSignature::class, 'signable');
    }
}
