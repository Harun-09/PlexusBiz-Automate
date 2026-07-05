<?php

namespace App\Domains\HCM\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'employee_id',
        'punch_time',
        'punch_type',
        'biometric_device_id',
    ];

    protected $casts = [
        'punch_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
