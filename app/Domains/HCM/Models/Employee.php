<?php

namespace App\Domains\HCM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_code',
        'basic_salary',
        'joining_date',
        'is_female_or_senior',
        'is_physically_challenged',
        'is_freedom_fighter',
        'is_pf_active',
        'pf_percentage',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'is_female_or_senior' => 'boolean',
        'is_physically_challenged' => 'boolean',
        'is_freedom_fighter' => 'boolean',
        'is_pf_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function payrollSlips()
    {
        return $this->hasMany(PayrollSlip::class);
    }
}
