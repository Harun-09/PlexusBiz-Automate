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
            'media_url' => $this->media_url,
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
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}
