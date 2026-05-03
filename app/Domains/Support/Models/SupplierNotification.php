<?php

namespace App\Domains\Support\Models;

use App\Domains\ECommerce\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierNotification extends Model
{
    protected $fillable = [
        'supplier_id',
        'support_ticket_id',
        'type',
        'title',
        'body',
        'payload_json',
        'read_at',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'read_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }
}
