<?php

namespace App\Domains\ECommerce\Models;

use App\Domains\ECommerce\Enums\RfqStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rfq extends Model
{
    use SoftDeletes;

    protected $table = 'rfqs';

    protected $fillable = [
        'buyer_id',
        'supplier_id',
        'rfq_number',
        'status',
        'message',
        'needed_by',
    ];

    protected $casts = [
        'needed_by' => 'datetime',
        'status' => RfqStatus::class,
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RfqItem::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(RfqResponse::class);
    }
}
