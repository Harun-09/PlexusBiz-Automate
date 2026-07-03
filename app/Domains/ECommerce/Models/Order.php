<?php

namespace App\Domains\ECommerce\Models;

use App\Domains\CRM\Models\Customer;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'buyer_id',
        'customer_id',
        'order_number',
        'status',
        'checkout_token',
        'payment_method',
        'payment_status',
        'transaction_id',
        'subtotal',
        'tax_total',
        'shipping_total',
        'discount_total',
        'grand_total',
        'currency',
        'placed_at',
        'payment_term',
        'due_date',
        'escrow_status',
        'delivery_status',
        'commission_amount',
        'late_fee_amount',
        'is_subscription',
        'subscription_active',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'placed_at' => 'datetime',
        'due_date' => 'datetime',
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'is_subscription' => 'boolean',
        'subscription_active' => 'boolean',
        'payment_term' => \App\Domains\ECommerce\Enums\PaymentTerm::class,
        'escrow_status' => \App\Domains\ECommerce\Enums\EscrowStatus::class,
        'delivery_status' => \App\Domains\ECommerce\Enums\DeliveryStatus::class,
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function supplierOrders(): HasMany
    {
        return $this->hasMany(SupplierOrder::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::Completed->value;
    }
}
