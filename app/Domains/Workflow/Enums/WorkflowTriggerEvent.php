<?php

namespace App\Domains\Workflow\Enums;

enum WorkflowTriggerEvent: string
{
    case OrderPlaced = 'order.placed';
    case RfqCreated = 'rfq.created';
    case CartAbandoned = 'cart.abandoned';
    case CustomerCreated = 'customer.created';
    case CampaignScheduled = 'campaign.scheduled';
    case TicketCreated = 'ticket.created';
    case SocialPostDue = 'social.post.due';
    case OrderStatusChanged = 'order.status_changed';
}
