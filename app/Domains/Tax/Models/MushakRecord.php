<?php

namespace App\Domains\Tax\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MushakRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_type',
        'reference_id',
        'reference_type',
        'amount',
        'vat_amount',
        'vds_amount',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'vds_amount' => 'decimal:2',
        'date' => 'datetime',
    ];
}
