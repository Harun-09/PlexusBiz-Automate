<?php

namespace App\Domains\HCM\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSlip extends Model
{
    protected $fillable = [
        'employee_id',
        'month_year',
        'basic_pay',
        'house_rent',
        'medical_allowance',
        'gross_pay',
        'tax_deducted',
        'pf_deducted',
        'net_pay',
        'is_disbursed',
        'disbursed_at',
    ];

    protected $casts = [
        'is_disbursed' => 'boolean',
        'disbursed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
