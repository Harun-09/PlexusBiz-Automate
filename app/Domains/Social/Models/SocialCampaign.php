<?php

namespace App\Domains\Social\Models;

use App\Domains\Marketing\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialCampaign extends Model
{
    use SoftDeletes;

    protected $table = 'social_campaigns';

    protected $fillable = [
        'campaign_id',
        'created_by',
        'name',
        'objective',
        'status',
        'start_at',
        'end_at',
        'budget',
        'metadata',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'budget' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function calendars(): HasMany
    {
        return $this->hasMany(ContentCalendar::class, 'social_campaign_id');
    }

    public function engagementLogs(): HasMany
    {
        return $this->hasMany(EngagementLog::class, 'social_campaign_id');
    }
}

