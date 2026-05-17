<?php

namespace App\Domains\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentCalendar extends Model
{
    protected $table = 'content_calendar';

    protected $fillable = [
        'social_post_id',
        'social_campaign_id',
        'platform',
        'title',
        'content',
        'scheduled_for',
        'status',
        'published_at',
        'metadata',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'published_at' => 'datetime',
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

