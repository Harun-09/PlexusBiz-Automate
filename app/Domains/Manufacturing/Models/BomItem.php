<?php

namespace App\Domains\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bom()
    {
        return $this->belongsTo(BillOfMaterial::class, 'bill_of_material_id');
    }
}
