<?php

namespace Database\Seeders;

use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Workflow\Enums\WorkflowActionType;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Models\AutomationRule;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        AutomationRule::updateOrCreate(
            ['name' => 'Order confirmation automation'],
            [
                'trigger_event' => WorkflowTriggerEvent::OrderPlaced->value,
                'conditions_json' => [
                    ['field' => 'order.grand_total', 'operator' => 'greater_than_or_equal', 'value' => 0],
                ],
                'actions_json' => [
                    [
                        'type' => WorkflowActionType::SendEmail->value,
                        'config' => [
                            'to_path' => 'buyer.email',
                            'subject' => 'Your PlexusBiz order is confirmed',
                            'body' => 'The order confirmation workflow completed.',
                        ],
                    ],
                    [
                        'type' => WorkflowActionType::NotifySupplier->value,
                        'config' => [
                            'message' => 'Supplier notification prepared for order placement.',
                        ],
                    ],
                ],
                'status' => AutomationRuleStatus::Active,
                'priority' => 10,
                'run_async' => false,
            ],
        );

        AutomationRule::updateOrCreate(
            ['name' => 'Ticket supplier notification automation'],
            [
                'trigger_event' => WorkflowTriggerEvent::TicketCreated->value,
                'conditions_json' => [
                    ['field' => 'ticket.status', 'operator' => 'not_equals', 'value' => 'closed'],
                ],
                'actions_json' => [
                    [
                        'type' => WorkflowActionType::NotifySupplier->value,
                        'config' => [
                            'message' => 'Support ticket supplier notification was prepared.',
                        ],
                    ],
                    [
                        'type' => WorkflowActionType::CreateTicketAutoReply->value,
                        'config' => [
                            'message' => 'Support auto response action recorded.',
                        ],
                    ],
                ],
                'status' => AutomationRuleStatus::Active,
                'priority' => 20,
                'run_async' => false,
            ],
        );

        AutomationRule::updateOrCreate(
            ['name' => 'RFQ supplier notification automation'],
            [
                'trigger_event' => WorkflowTriggerEvent::RfqCreated->value,
                'conditions_json' => [
                    ['field' => 'rfq.status', 'operator' => 'equals', 'value' => 'open'],
                ],
                'actions_json' => [
                    [
                        'type' => WorkflowActionType::NotifySupplier->value,
                        'config' => [
                            'subject' => 'New RFQ received',
                            'message' => 'New RFQ received for the requested product.',
                        ],
                    ],
                ],
                'status' => AutomationRuleStatus::Active,
                'priority' => 15,
                'run_async' => false,
            ],
        );

        $this->seedOrderStatusRules();
    }

    private function seedOrderStatusRules(): void
    {
        $rules = [
            [
                'name' => 'Order status confirmed automation',
                'status' => 'confirmed',
                'subject' => 'Your PlexusBiz order has been confirmed',
                'body' => 'Your order has been confirmed. We are preparing it for the next fulfillment step.',
                'priority' => 30,
            ],
            [
                'name' => 'Order status shipped automation',
                'status' => 'shipped',
                'subject' => 'Your PlexusBiz order has shipped',
                'body' => 'Your order has been shipped and is on the way.',
                'priority' => 31,
            ],
            [
                'name' => 'Order status completed automation',
                'status' => 'completed',
                'subject' => 'Your PlexusBiz order has been completed',
                'body' => 'Your order has been completed successfully.',
                'priority' => 32,
            ],
            [
                'name' => 'Order status cancelled automation',
                'status' => 'cancelled',
                'subject' => 'Your PlexusBiz order has been cancelled',
                'body' => 'Your order has been cancelled. Please contact support if you need help.',
                'priority' => 33,
                'notify_supplier' => true,
            ],
        ];

        foreach ($rules as $rule) {
            $actions = [
                [
                    'type' => WorkflowActionType::SendEmail->value,
                    'config' => [
                        'to_path' => 'buyer.email',
                        'subject' => $rule['subject'],
                        'body' => $rule['body'],
                    ],
                ],
                [
                    'type' => WorkflowActionType::CreateNotification->value,
                    'config' => [
                        'subject' => $rule['subject'],
                        'message' => $rule['body'],
                    ],
                ],
            ];

            if (($rule['notify_supplier'] ?? false) === true) {
                $actions[] = [
                    'type' => WorkflowActionType::NotifySupplier->value,
                    'config' => [
                        'subject' => 'Supplier order cancellation notice',
                        'message' => 'The buyer cancelled the order.',
                    ],
                ];
            }

            AutomationRule::updateOrCreate(
                ['name' => $rule['name']],
                [
                    'trigger_event' => WorkflowTriggerEvent::OrderStatusChanged->value,
                    'conditions_json' => [
                        ['field' => 'order.status', 'operator' => 'equals', 'value' => $rule['status']],
                    ],
                    'actions_json' => $actions,
                    'status' => AutomationRuleStatus::Active,
                    'priority' => $rule['priority'],
                    'run_async' => false,
                ],
            );
        }
    }
}
