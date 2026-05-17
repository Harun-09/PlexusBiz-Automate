<?php

namespace App\DTOs\ECommerce;

class RfqResponseData
{
    public function __construct(
        public readonly int $rfqId,
        public readonly int $supplierId,
        public readonly ?int $respondedBy,
        public readonly string $quotedAmount,
        public readonly string $currency,
        public readonly ?int $minOrderQuantity,
        public readonly ?int $leadTimeDays,
        public readonly ?string $validUntil,
        public readonly ?string $message,
    ) {
    }

    /**
     * @param array<string, mixed> $validated
     */
    public static function fromValidated(array $validated, int $rfqId, int $supplierId, ?int $respondedBy = null): self
    {
        return new self(
            rfqId: $rfqId,
            supplierId: $supplierId,
            respondedBy: $respondedBy,
            quotedAmount: number_format((float) $validated['quoted_amount'], 2, '.', ''),
            currency: strtoupper((string) ($validated['currency'] ?? 'BDT')),
            minOrderQuantity: isset($validated['min_order_quantity']) ? (int) $validated['min_order_quantity'] : null,
            leadTimeDays: isset($validated['lead_time_days']) ? (int) $validated['lead_time_days'] : null,
            validUntil: isset($validated['valid_until']) ? (string) $validated['valid_until'] : null,
            message: isset($validated['message']) ? trim((string) $validated['message']) : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        return [
            'responded_by' => $this->respondedBy,
            'quoted_amount' => $this->quotedAmount,
            'currency' => $this->currency,
            'min_order_quantity' => $this->minOrderQuantity,
            'lead_time_days' => $this->leadTimeDays,
            'valid_until' => $this->validUntil,
            'message' => $this->message,
        ];
    }
}

