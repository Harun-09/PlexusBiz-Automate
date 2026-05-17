<?php

namespace App\Domains\ECommerce\Models;

use App\Domains\ECommerce\Enums\RfqResponseStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqResponse extends Model
{
    protected $table = 'rfq_responses';

    protected $fillable = [
        'rfq_id',
        'supplier_id',
        'responded_by',
        'quoted_amount',
        'currency',
        'min_order_quantity',
        'lead_time_days',
        'valid_until',
        'message',
        'status',
        'buyer_action_at',
    ];

    protected $casts = [
        'status' => RfqResponseStatus::class,
        'quoted_amount' => 'decimal:2',
        'valid_until' => 'datetime',
        'buyer_action_at' => 'datetime',
    ];

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}

