<?php

namespace App\Domains\ECommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStoreVariation extends Model
{
    protected $fillable = ['sku_id', 'supplier_id', 'custom_price'];

    public function sku(): BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
