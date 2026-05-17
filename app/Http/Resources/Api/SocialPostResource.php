<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialPostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'platform' => $this->platform->value,
            'content' => $this->content,
            'media_url' => $this->mediaUrl(),
            'media' => $this->hasMedia() ? [
                'url' => $this->mediaUrl(),
                'original_path' => $this->mediaOriginalPath(),
                'public_path' => $this->mediaPublicPath(),
                'variants' => [
                    'thumbnail' => $this->mediaVariantPath('thumbnail') ? [
                        'path' => $this->mediaVariantPath('thumbnail'),
                        'url' => $this->mediaVariantUrl('thumbnail'),
                        'generated' => (bool) data_get($this->mediaMeta(), 'variants.thumbnail.generated', false),
                        'max_width' => data_get($this->mediaMeta(), 'variants.thumbnail.max_width'),
                        'max_height' => data_get($this->mediaMeta(), 'variants.thumbnail.max_height'),
                    ] : null,
                    'preview' => $this->mediaVariantPath('preview') ? [
                        'path' => $this->mediaVariantPath('preview'),
                        'url' => $this->mediaVariantUrl('preview'),
                        'generated' => (bool) data_get($this->mediaMeta(), 'variants.preview.generated', false),
                        'max_width' => data_get($this->mediaMeta(), 'variants.preview.max_width'),
                        'max_height' => data_get($this->mediaMeta(), 'variants.preview.max_height'),
                    ] : null,
                ],
                'storage_meta' => $this->mediaMeta(),
                'is_external' => $this->isExternalMediaUrl(),
            ] : null,
            'scheduled_at' => $this->scheduled_at?->toJSON(),
            'status' => $this->status->value,
            'external_post_id' => $this->external_post_id,
            'published_at' => $this->published_at?->toJSON(),
            'failure_reason' => $this->failure_reason,
            'engagement' => [
                'likes' => $this->likes_count,
                'comments' => $this->comments_count,
                'shares' => $this->shares_count,
                'reach' => $this->reach_count,
                'clicks' => $this->clicks_count,
            ],
            'campaign' => $this->whenLoaded('campaign', fn (): ?array => $this->campaign ? [
                'id' => $this->campaign->id,
                'name' => $this->campaign->name,
            ] : null),
            'social_campaign' => $this->whenLoaded('socialCampaign', fn (): ?array => $this->socialCampaign ? [
                'id' => $this->socialCampaign->id,
                'name' => $this->socialCampaign->name,
                'status' => $this->socialCampaign->status,
            ] : null),
            'calendar_entry' => $this->whenLoaded('contentCalendar', fn (): ?array => $this->contentCalendar ? [
                'id' => $this->contentCalendar->id,
                'scheduled_for' => $this->contentCalendar->scheduled_for?->toJSON(),
                'status' => $this->contentCalendar->status,
            ] : null),
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}
