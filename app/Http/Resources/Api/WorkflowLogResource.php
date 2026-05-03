<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rule' => $this->whenLoaded('rule', fn (): ?array => $this->rule ? [
                'id' => $this->rule->id,
                'name' => $this->rule->name,
            ] : null),
            'trigger_event' => $this->trigger_event,
            'status' => $this->status->value,
            'payload' => $this->payload ?? [],
            'result' => $this->result ?? [],
            'error' => $this->error,
            'executed_at' => $this->executed_at?->toJSON(),
            'created_at' => $this->created_at?->toJSON(),
        ];
    }
}
