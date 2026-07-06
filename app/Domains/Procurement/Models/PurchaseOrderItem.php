<?php

namespace App\Domains\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domains\ECommerce\Models\Product;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
