<?php

namespace App\Domains\Tax\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MushakDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_invoice_id',
        'form_type',
        'issue_date',
        'challan_number',
        'total_vat_amount',
        'pdf_path',
    ];

    protected $casts = [
        'issue_date' => 'datetime',
        'total_vat_amount' => 'decimal:2',
    ];

    public function taxInvoice()
    {
        return $this->belongsTo(TaxInvoice::class);
    }
}
