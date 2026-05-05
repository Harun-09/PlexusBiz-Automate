<?php

namespace App\Domains\CRM\Enums;

enum InteractionType: string
{
    case Note = 'note';
    case Email = 'email';
    case Message = 'message';
    case Sms = 'sms';
    case Order = 'order';
    case Rfq = 'rfq';
    case Campaign = 'campaign';
    case SupportTicket = 'support_ticket';
}
