<?php

namespace App\Domains\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptNote extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'received_quantities' => 'array',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
