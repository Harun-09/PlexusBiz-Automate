<?php

namespace App\Domains\Support\Services;

use App\Domains\Support\Models\SupplierNotification;
use App\Domains\Support\Models\SupportTicket;

class SupplierNotificationService
{
    public function notifyTicketCreated(SupportTicket $ticket): ?SupplierNotification
    {
        if ($ticket->supplier_id === null) {
            return null;
        }

        return SupplierNotification::create([
            'supplier_id' => $ticket->supplier_id,
            'support_ticket_id' => $ticket->id,
            'type' => 'support.ticket.created',
            'title' => 'New support ticket '.$ticket->ticket_number,
            'body' => $ticket->subject,
            'payload_json' => [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'requester_id' => $ticket->requester_id,
                'priority' => $ticket->priority->value,
                'status' => $ticket->status->value,
            ],
        ]);
    }
}
