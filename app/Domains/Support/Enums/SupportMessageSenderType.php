<?php

namespace App\Domains\Support\Enums;

enum SupportMessageSenderType: string
{
    case Buyer = 'buyer';
    case Customer = 'customer';
    case Supplier = 'supplier';
    case Agent = 'agent';
    case Automation = 'automation';
    case Chatbot = 'chatbot';
}
