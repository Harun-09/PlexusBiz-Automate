<?php

namespace App\Domains\Marketing\Models;

use App\Domains\CRM\Models\Customer;
use App\Domains\Marketing\Enums\MessageStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignRecipient extends Model
{
    protected $fillable = [
        'campaign_id',
        'customer_id',
        'email',
        'phone',
        'status',
        'sent_at',
        'error',
    ];

    protected $casts = [
        'status' => MessageStatus::class,
        'sent_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
