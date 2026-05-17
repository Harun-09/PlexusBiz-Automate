<?php

namespace App\Domains\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngagementLog extends Model
{
    protected $table = 'engagement_logs';

    protected $fillable = [
        'social_post_id',
        'social_campaign_id',
        'platform',
        'metric_type',
        'metric_value',
        'recorded_at',
        'metadata',
    ];

    protected $casts = [
        'metric_value' => 'integer',
        'recorded_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function socialPost(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class);
    }

    public function socialCampaign(): BelongsTo
    {
        return $this->belongsTo(SocialCampaign::class, 'social_campaign_id');
    }
}

