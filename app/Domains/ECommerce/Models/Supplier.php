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
use Illuminate\Support\Facades\Storage;

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
        'logo_path',
        'verification_document_path',
        'verification_document_name',
        'wallet_balance',
        'trade_license_path',
        'tin_number',
        'bin_number',
        'corporate_certificate_path',
        'address',
        'approved_at',
    ];

    protected $casts = [
        'address' => 'array',
        'approved_at' => 'datetime',
        'wallet_balance' => 'decimal:2',
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

    public function rfqResponses(): HasMany
    {
        return $this->hasMany(RfqResponse::class);
    }

    public function isApproved(): bool
    {
        return $this->status === SupplierStatus::Approved;
    }

    public function isPending(): bool
    {
        return $this->status === SupplierStatus::Pending;
    }

    public function logoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    public function verificationDocumentUrl(): ?string
    {
        if (! $this->verification_document_path) {
            return null;
        }

        return Storage::disk('public')->url($this->verification_document_path);
    }
}
