<?php

namespace App\Domains\Tax\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domains\ECommerce\Models\Order;

class TaxInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tax_invoice_number',
        'file_path',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function mushakDocuments()
    {
        return $this->hasMany(MushakDocument::class);
    }
}
