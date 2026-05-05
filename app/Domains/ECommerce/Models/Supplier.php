<?php

namespace App\Domains\ECommerce\Models;

use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\SupplierOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'approved_by',
        'company_name',
        'slug',
        'status',
        'contact_email',
        'phone',
        'tax_number',
        'address',
        'approved_at',
    ];

    protected $casts = [
        'address' => 'array',
        'approved_at' => 'datetime',
        'status' => SupplierStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function supplierOrders(): HasMany
    {
        return $this->hasMany(SupplierOrder::class);
    }

    public function isApproved(): bool
    {
        return $this->status === SupplierStatus::Approved;
    }

    public function isPending(): bool
    {
        return $this->status === SupplierStatus::Pending;
    }
}
