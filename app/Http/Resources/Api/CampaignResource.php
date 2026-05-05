<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_by' => $this->created_by,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'segment_filters' => $this->segment_filters_json ?? [],
            'scheduled_at' => $this->scheduled_at?->toJSON(),
            'started_at' => $this->started_at?->toJSON(),
            'completed_at' => $this->completed_at?->toJSON(),
            'templates_count' => $this->whenCounted('templates'),
            'recipients_count' => $this->whenCounted('recipients'),
            'logs_count' => $this->whenCounted('logs'),
            'creator' => $this->whenLoaded('creator', fn (): ?array => $this->creator ? [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ] : null),
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}
