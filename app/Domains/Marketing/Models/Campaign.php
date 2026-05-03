<?php

namespace App\Domains\Marketing\Models;

use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Enums\CampaignType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'name',
        'slug',
        'type',
        'status',
        'segment_filters_json',
        'scheduled_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'type' => CampaignType::class,
        'status' => CampaignStatus::class,
        'segment_filters_json' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function templates(): HasMany
    {
        return $this->hasMany(CampaignTemplate::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CampaignLog::class);
    }
}
