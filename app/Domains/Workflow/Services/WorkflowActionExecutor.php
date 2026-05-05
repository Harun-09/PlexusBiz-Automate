<?php

namespace App\Domains\Workflow\Services;

use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Marketing\Contracts\EmailProvider;
use App\Domains\Marketing\Contracts\SmsProvider;
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Workflow\Enums\WorkflowActionType;
use App\Domains\Notifications\Services\MessageService;

class WorkflowActionExecutor
{
    public function __construct(
        private readonly EmailProvider $email,
        private readonly SmsProvider $sms,
        private readonly MessageService $messages,
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
            WorkflowActionType::CreateNotification => $this->createNotification($config, $payload),
            WorkflowActionType::NotifySupplier => $this->notifySupplier($config, $payload),
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
    private function createNotification(array $config, array $payload): array
    {
        $receiverId = (int) data_get($config, 'receiver_id', data_get($payload, 'buyer.id', 0));
        $subject = (string) ($config['subject'] ?? 'PlexusBiz notification');
        $body = (string) ($config['message'] ?? $config['body'] ?? 'A workflow notification was created.');

        $receiver = $receiverId > 0 ? \App\Models\User::query()->find($receiverId) : null;

        if (! $receiver) {
            return [
                'type' => WorkflowActionType::CreateNotification->value,
                'successful' => false,
                'error' => 'Notification receiver not found.',
            ];
        }

        $message = $this->messages->send(
            sender: null,
            receiver: $receiver,
            body: $body,
            subject: $subject,
            channel: MessageChannel::System,
            referenceType: data_get($payload, 'order.id') ? Order::class : null,
            referenceId: data_get($payload, 'order.id') ? (int) data_get($payload, 'order.id') : null,
            payload: $payload,
        );

        $message->markAsSent();

        return [
            'type' => WorkflowActionType::CreateNotification->value,
            'successful' => true,
            'provider' => 'in_app',
            'response' => [
                'message_id' => $message->id,
                'receiver_id' => $receiver->id,
            ],
            'error' => null,
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function notifySupplier(array $config, array $payload): array
    {
        $supplierIds = $this->supplierIdsFromPayload($config, $payload);

        if ($supplierIds === []) {
            return [
                'type' => WorkflowActionType::NotifySupplier->value,
                'successful' => false,
                'error' => 'No supplier could be resolved from the workflow payload.',
            ];
        }

        $subject = (string) ($config['subject'] ?? $config['title'] ?? 'Supplier notification');
        $messageBase = (string) ($config['message'] ?? $config['body'] ?? 'You have a new supplier notification.');
        $orderNumber = (string) data_get($payload, 'order.order_number', '');
        $ticketNumber = (string) data_get($payload, 'ticket.number', '');
        $buyerName = (string) data_get($payload, 'buyer.name', '');

        $bodySuffix = collect([$orderNumber ? "Order #{$orderNumber}" : null, $ticketNumber ? "Ticket #{$ticketNumber}" : null, $buyerName !== '' ? "Buyer: {$buyerName}" : null])
            ->filter()
            ->implode(' | ');

        $responses = [];

        foreach ($supplierIds as $supplierId) {
            $supplier = Supplier::query()->with('user')->find($supplierId);

            if (! $supplier?->user) {
                continue;
            }

            $message = trim($messageBase.($bodySuffix !== '' ? ' - '.$bodySuffix : ''));

            $record = $this->messages->send(
                sender: null,
                receiver: $supplier->user,
                body: $message,
                subject: $subject,
                channel: MessageChannel::System,
                referenceType: data_get($payload, 'order.id') ? Order::class : null,
                referenceId: data_get($payload, 'order.id') ? (int) data_get($payload, 'order.id') : null,
                payload: $payload,
            );

            $record->markAsSent();

            $responses[] = [
                'supplier_id' => $supplier->id,
                'message_id' => $record->id,
            ];
        }

        return [
            'type' => WorkflowActionType::NotifySupplier->value,
            'successful' => $responses !== [],
            'provider' => 'in_app',
            'response' => [
                'notifications' => $responses,
            ],
            'error' => $responses === [] ? 'No supplier user was available for notification.' : null,
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $payload
     * @return array<int, int>
     */
    private function supplierIdsFromPayload(array $config, array $payload): array
    {
        $ids = [];

        $explicit = data_get($config, 'supplier_id');
        if (is_numeric($explicit)) {
            $ids[] = (int) $explicit;
        }

        $supplier = data_get($payload, 'supplier.id');
        if (is_numeric($supplier)) {
            $ids[] = (int) $supplier;
        }

        $ticketSupplier = data_get($payload, 'ticket.supplier.id');
        if (is_numeric($ticketSupplier)) {
            $ids[] = (int) $ticketSupplier;
        }

        $rfqSupplier = data_get($payload, 'rfq.supplier_id');
        if (is_numeric($rfqSupplier)) {
            $ids[] = (int) $rfqSupplier;
        }

        $productSupplier = data_get($payload, 'product.supplier_id');
        if (is_numeric($productSupplier)) {
            $ids[] = (int) $productSupplier;
        }

        $items = data_get($payload, 'items', []);
        if (is_array($items)) {
            foreach ($items as $item) {
                $supplierId = data_get($item, 'supplier_id');
                if (is_numeric($supplierId)) {
                    $ids[] = (int) $supplierId;
                }
            }
        }

        return array_values(array_unique(array_filter($ids)));
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
