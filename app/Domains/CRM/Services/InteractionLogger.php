<?php

namespace App\Domains\CRM\Services;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Models\Interaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class InteractionLogger
{
    /**
     * @param array<string, mixed> $payload
     */
    public function record(
        Customer $customer,
        InteractionType $type,
        string $summary,
        ?Model $related = null,
        array $payload = [],
        ?User $actor = null,
        ?string $direction = null,
    ): Interaction {
        $interaction = Interaction::create([
            'customer_id' => $customer->id,
            'user_id' => $actor?->id,
            'type' => $type,
            'direction' => $direction,
            'related_type' => $related ? $related::class : null,
            'related_id' => $related?->getKey(),
            'summary' => $summary,
            'payload' => $payload,
            'occurred_at' => now(),
        ]);

        $customer->forceFill(['last_activity_at' => $interaction->occurred_at])->save();

        return $interaction;
    }
}
