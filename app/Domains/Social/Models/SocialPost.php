<?php

namespace App\Domains\Social\Models;

use App\Domains\Marketing\Models\Campaign;
use App\Domains\Social\Models\ContentCalendar;
use App\Domains\Social\Models\EngagementLog;
use App\Domains\Social\Models\SocialCampaign;
use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Enums\SocialPostStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocialPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'social_account_id',
        'platform',
        'content',
        'media_url',
        'media_meta',
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
        'media_meta' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'social_account_id');
    }

    public function contentCalendar(): HasOne
    {
        return $this->hasOne(ContentCalendar::class);
    }

    public function engagementLogs(): HasMany
    {
        return $this->hasMany(EngagementLog::class);
    }

    public function socialCampaign(): HasOne
    {
        return $this->hasOne(SocialCampaign::class, 'campaign_id', 'campaign_id');
    }

    public function mediaMeta(): array
    {
        return is_array($this->media_meta) ? $this->media_meta : [];
    }

    public function mediaOriginalPath(): ?string
    {
        $originalPath = data_get($this->mediaMeta(), 'original_path');

        return $originalPath !== null && $originalPath !== '' ? $originalPath : null;
    }

    public function mediaPublicPath(): ?string
    {
        $publicPath = data_get($this->mediaMeta(), 'public_path');

        if ($publicPath !== null && $publicPath !== '') {
            return $publicPath;
        }

        $mediaUrl = trim((string) $this->getRawOriginal('media_url'));

        if ($mediaUrl === '' || $this->isExternalMediaUrl($mediaUrl)) {
            return null;
        }

        return $mediaUrl;
    }

    public function mediaUrl(): ?string
    {
        $publicUrl = data_get($this->mediaMeta(), 'public_url');

        if ($publicUrl !== null && $publicUrl !== '') {
            return $publicUrl;
        }

        $mediaUrl = trim((string) $this->getRawOriginal('media_url'));

        if ($mediaUrl === '') {
            return null;
        }

        if ($this->isExternalMediaUrl($mediaUrl)) {
            return $mediaUrl;
        }

        return Storage::disk(config('media.public_disk', 'public'))->url($mediaUrl);
    }

    public function mediaVariantMeta(string $variant): ?array
    {
        $variantMeta = data_get($this->mediaMeta(), 'variants.'.$variant);

        if (is_array($variantMeta)) {
            return $variantMeta;
        }

        if (is_string($variantMeta) && $variantMeta !== '') {
            return ['path' => $variantMeta];
        }

        return null;
    }

    public function mediaVariantPath(string $variant): ?string
    {
        $variantPath = data_get($this->mediaVariantMeta($variant) ?? [], 'path');

        return is_string($variantPath) && $variantPath !== '' ? $variantPath : null;
    }

    public function mediaVariantUrl(string $variant): ?string
    {
        $path = $this->mediaVariantPath($variant);

        if ($path === null) {
            return null;
        }

        return Storage::disk(config('media.public_disk', 'public'))->url($path);
    }

    public function hasMedia(): bool
    {
        return $this->mediaUrl() !== null || $this->mediaOriginalPath() !== null || $this->mediaPublicPath() !== null;
    }

    public function isExternalMediaUrl(?string $value = null): bool
    {
        $value ??= trim((string) $this->getRawOriginal('media_url'));

        return $value !== '' && Str::startsWith($value, ['http://', 'https://', '//']);
    }
}
