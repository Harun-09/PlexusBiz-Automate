<?php

namespace App\Domains\Support\Enums;

enum SupportChannel: string
{
    case Web = 'web';
    case Email = 'email';
    case Chatbot = 'chatbot';
    case Automation = 'automation';
}
