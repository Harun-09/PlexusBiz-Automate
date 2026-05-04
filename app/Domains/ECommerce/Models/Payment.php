<?php

namespace App\Domains\ECommerce\Models;

use App\Domains\ECommerce\Enums\InvoiceStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_method',
        'transaction_id',
        'gateway_transaction_id',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'payer_reference',
        'payer_phone',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'status' => PaymentStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment): void {
            if (blank($payment->transaction_id)) {
                $payment->transaction_id = 'TXN-'.strtoupper(Str::random(12));
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsPaid(?string $gatewayTransactionId = null, array $gatewayResponse = []): void
    {
        $this->forceFill([
            'status' => PaymentStatus::Completed,
            'gateway_transaction_id' => $gatewayTransactionId,
            'gateway_response' => $gatewayResponse,
            'paid_at' => now(),
        ])->save();

        $order = $this->order()->with('invoice')->first();
        if (! $order) {
            return;
        }

        $order->forceFill([
            'payment_status' => PaymentStatus::Completed->value,
            'transaction_id' => $gatewayTransactionId ?: $this->transaction_id,
        ])->save();

        if ($order->invoice) {
            $order->invoice->forceFill([
                'status' => InvoiceStatus::Paid,
            ])->save();
        }
    }

    public function markAsFailed(array $gatewayResponse = []): void
    {
        $this->forceFill([
            'status' => PaymentStatus::Failed,
            'gateway_response' => $gatewayResponse,
        ])->save();

        $order = $this->order()->first();
        if ($order) {
            $order->forceFill([
                'payment_status' => PaymentStatus::Failed->value,
            ])->save();
        }
    }

    public function markAsCancelled(array $gatewayResponse = []): void
    {
        $this->forceFill([
            'status' => PaymentStatus::Cancelled,
            'gateway_response' => $gatewayResponse,
        ])->save();

        $order = $this->order()->first();
        if ($order) {
            $order->forceFill([
                'payment_status' => PaymentStatus::Cancelled->value,
            ])->save();
        }
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }

    public function isCompleted(): bool
    {
        return $this->status === PaymentStatus::Completed;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::Failed;
    }
}
