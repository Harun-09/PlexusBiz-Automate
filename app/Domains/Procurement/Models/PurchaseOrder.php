<?php

namespace App\Domains\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns()
    {
        return $this->hasMany(GoodsReceiptNote::class);
    }
}
