<?php

namespace App\Domains\CorporateGovernance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalSignature extends Model
{
    use HasFactory;

    protected $guarded = [];

    // The document being signed (Form, Resolution, etc.)
    public function signable()
    {
        return $this->morphTo();
    }

    // The entity signing (User, Supplier, etc.)
    public function signer()
    {
        return $this->morphTo();
    }
}
