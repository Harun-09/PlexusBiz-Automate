<?php

namespace App\Domains\Workflow\Enums;

enum WorkflowActionType: string
{
    case SendEmail = 'send_email';
    case SendSms = 'send_sms';
    case CreateNotification = 'create_notification';
    case NotifySupplier = 'notify_supplier';
    case AssignTask = 'assign_task';
    case CreateTicketAutoReply = 'create_ticket_auto_reply';
    case CallWebhookMock = 'call_webhook_mock';
}
