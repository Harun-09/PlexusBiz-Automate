<?php

namespace App\Domains\CRM\Models;

use App\Domains\CRM\Enums\LeadStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'assigned_user_id',
        'source',
        'status',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'value',
        'score',
        'notes',
        'next_follow_up_at',
        'converted_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'next_follow_up_at' => 'datetime',
        'converted_at' => 'datetime',
        'status' => LeadStatus::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
