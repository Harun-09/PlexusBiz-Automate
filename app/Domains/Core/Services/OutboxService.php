<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Models\OutboxMessage;
use Illuminate\Support\Facades\Log;

class OutboxService
{
    /**
     * Persist an event to the outbox table to be processed later.
     * This method must be called from within the SAME database transaction 
     * as the domain state change to guarantee atomic consistency.
     *
     * @param string $eventType The fully qualified class name of the Event
     * @param array $payload The serializable data required to reconstruct the Event
     * @return OutboxMessage
     */
    public function saveEvent(string $eventType, array $payload): OutboxMessage
    {
        return OutboxMessage::create([
            'event_type' => $eventType,
            'payload' => $payload,
            'processed' => false,
        ]);
    }
}
