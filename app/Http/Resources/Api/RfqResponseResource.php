<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RfqResponseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rfq_id' => $this->rfq_id,
            'quoted_amount' => $this->quoted_amount,
            'currency' => $this->currency,
            'min_order_quantity' => $this->min_order_quantity,
            'lead_time_days' => $this->lead_time_days,
            'valid_until' => $this->valid_until?->toJSON(),
            'message' => $this->message,
            'status' => $this->status->value,
            'buyer_action_at' => $this->buyer_action_at?->toJSON(),
            'supplier' => $this->whenLoaded('supplier', fn (): ?array => $this->supplier ? [
                'id' => $this->supplier->id,
                'company_name' => $this->supplier->company_name,
                'contact_email' => $this->supplier->contact_email,
            ] : null),
            'responder' => $this->whenLoaded('responder', fn (): ?array => $this->responder ? [
                'id' => $this->responder->id,
                'name' => $this->responder->name,
                'email' => $this->responder->email,
            ] : null),
            'rfq' => $this->whenLoaded('rfq', fn (): ?array => $this->rfq ? [
                'id' => $this->rfq->id,
                'rfq_number' => $this->rfq->rfq_number,
                'status' => $this->rfq->status->value,
                'message' => $this->rfq->message,
                'needed_by' => $this->rfq->needed_by?->toJSON(),
                'buyer_id' => $this->rfq->buyer_id,
                'supplier_id' => $this->rfq->supplier_id,
            ] : null),
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}

