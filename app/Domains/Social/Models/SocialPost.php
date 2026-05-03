<?php

namespace App\Domains\Social\Models;

use App\Domains\Marketing\Models\Campaign;
use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Enums\SocialPostStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'social_account_id',
        'platform',
        'content',
        'media_url',
        'scheduled_at',
        'status',
        'external_post_id',
        'published_at',
        'failure_reason',
        'likes_count',
        'comments_count',
        'shares_count',
        'reach_count',
        'clicks_count',
    ];

    protected $casts = [
        'platform' => SocialPlatform::class,
        'status' => SocialPostStatus::class,
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'social_account_id');
    }
}
