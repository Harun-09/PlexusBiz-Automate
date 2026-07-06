<?php

namespace App\Domains\Tax\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domains\ECommerce\Models\Category;

class TaxConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'region',
        'category_id',
        'tax_rate',
        'is_active',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
