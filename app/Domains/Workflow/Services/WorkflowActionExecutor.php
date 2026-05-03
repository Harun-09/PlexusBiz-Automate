<?php

namespace App\Domains\Workflow\Services;

use App\Domains\Marketing\Contracts\EmailProvider;
use App\Domains\Marketing\Contracts\SmsProvider;
use App\Domains\Workflow\Enums\WorkflowActionType;

class WorkflowActionExecutor
{
    public function __construct(
        private readonly EmailProvider $email,
        private readonly SmsProvider $sms,
    ) {
    }

    /**
     * @param array<string, mixed> $action
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function execute(array $action, array $payload): array
    {
        $type = WorkflowActionType::from((string) ($action['type'] ?? 'call_webhook_mock'));
        $config = $action['config'] ?? [];

        return match ($type) {
            WorkflowActionType::SendEmail => $this->sendEmail($config, $payload),
            WorkflowActionType::SendSms => $this->sendSms($config, $payload),
            WorkflowActionType::CreateNotification => $this->mocked($type, $config, $payload),
            WorkflowActionType::NotifySupplier => $this->mocked($type, $config, $payload),
            WorkflowActionType::AssignTask => $this->mocked($type, $config, $payload),
            WorkflowActionType::CreateTicketAutoReply => $this->mocked($type, $config, $payload),
            WorkflowActionType::CallWebhookMock => $this->mocked($type, $config, $payload),
        };
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function sendEmail(array $config, array $payload): array
    {
        $to = (string) data_get($payload, (string) ($config['to_path'] ?? 'buyer.email'));
        $subject = (string) ($config['subject'] ?? 'PlexusBiz notification');
        $body = (string) ($config['body'] ?? 'Workflow email action executed.');
        $result = $this->email->send($to, $subject, $body, $payload);

        return [
            'type' => WorkflowActionType::SendEmail->value,
            'provider' => $result->provider,
            'successful' => $result->successful,
            'response' => $result->response,
            'error' => $result->error,
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function sendSms(array $config, array $payload): array
    {
        $to = (string) data_get($payload, (string) ($config['to_path'] ?? 'buyer.phone'));
        $body = (string) ($config['body'] ?? 'Workflow SMS action executed.');
        $result = $this->sms->send($to, $body, $payload);

        return [
            'type' => WorkflowActionType::SendSms->value,
            'provider' => $result->provider,
            'successful' => $result->successful,
            'response' => $result->response,
            'error' => $result->error,
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function mocked(WorkflowActionType $type, array $config, array $payload): array
    {
        return [
            'type' => $type->value,
            'successful' => true,
            'mocked' => true,
            'config' => $config,
            'payload_keys' => array_keys($payload),
        ];
    }
}
