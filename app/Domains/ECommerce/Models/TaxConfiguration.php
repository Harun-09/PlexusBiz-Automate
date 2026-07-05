<?php

namespace App\Domains\ECommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'region',
        'category_id',
        'tax_rate',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
