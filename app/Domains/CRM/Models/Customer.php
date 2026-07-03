<?php

namespace App\Domains\CRM\Models;

use App\Domains\CRM\Enums\CustomerLifecycleStage;
use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\ECommerce\Models\Order;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'business_type',
        'address',
        'status',
        'lifecycle_stage',
        'tags',
        'notes',
        'last_activity_at',
        'credit_limit',
        'credit_used',
        'net_terms',
        'is_credit_restricted',
    ];

    protected $casts = [
        'tags' => 'array',
        'address' => 'array',
        'last_activity_at' => 'datetime',
        'status' => CustomerStatus::class,
        'lifecycle_stage' => CustomerLifecycleStage::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope customer records to buyer-linked accounts only.
     */
    public function scopeBuyerAccounts(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereHas('user', function (Builder $user): void {
                $user->where('account_type', RoleName::Buyer->value);
            })->orWhereHas('user', function (Builder $user): void {
                $user->whereHas('roles', function (Builder $roles): void {
                    $roles->where('name', RoleName::Buyer->value);
                });
            });
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }
}
