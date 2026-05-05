<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignTemplateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'template_key' => $this->template_key,
            'channel' => is_object($this->channel) ? $this->channel->value : (string) $this->channel,
            'name' => $this->name,
            'subject' => $this->subject,
            'body' => $this->body,
            'variables' => $this->variables ?? [],
            'status' => (string) $this->status,
            'campaign' => $this->whenLoaded('campaign', fn (): ?array => $this->campaign ? [
                'id' => $this->campaign->id,
                'name' => $this->campaign->name,
                'slug' => $this->campaign->slug,
            ] : null),
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}
