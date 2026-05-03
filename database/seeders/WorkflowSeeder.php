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
    }
}
