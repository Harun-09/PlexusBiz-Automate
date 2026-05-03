<?php

namespace App\Domains\Marketing\Models;

use App\Domains\CRM\Models\Customer;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Enums\MessageStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'campaign_recipient_id',
        'customer_id',
        'channel',
        'status',
        'provider',
        'payload',
        'response',
        'error',
        'sent_at',
    ];

    protected $casts = [
        'channel' => MessageChannel::class,
        'status' => MessageStatus::class,
        'payload' => 'array',
        'response' => 'array',
        'sent_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(CampaignRecipient::class, 'campaign_recipient_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
