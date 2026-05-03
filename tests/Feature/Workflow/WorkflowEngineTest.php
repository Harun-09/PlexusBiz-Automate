<?php

namespace Tests\Feature\Workflow;

use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Workflow\Enums\WorkflowActionType;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Models\AutomationRule;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Domains\Workflow\Services\WorkflowEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_engine_executes_matching_rule_and_stores_full_payload_snapshot(): void
    {
        $rule = AutomationRule::create([
            'name' => 'High value order webhook',
            'trigger_event' => WorkflowTriggerEvent::OrderPlaced->value,
            'conditions_json' => [
                ['field' => 'order.grand_total', 'operator' => 'greater_than_or_equal', 'value' => 500],
            ],
            'actions_json' => [
                ['type' => WorkflowActionType::CallWebhookMock->value, 'config' => ['url' => 'https://example.test/webhook']],
            ],
            'status' => AutomationRuleStatus::Active,
            'priority' => 1,
            'run_async' => false,
        ]);

        $payload = $this->payload(grandTotal: '750.00');

        $logs = app(WorkflowEngineService::class)->handle(WorkflowTriggerEvent::OrderPlaced->value, $payload);

        $log = $logs->first();

        $this->assertSame(1, $logs->count());
        $this->assertSame($rule->id, $log->rule_id);
        $this->assertSame(WorkflowLogStatus::Success, $log->status);
        $this->assertSame($payload, $log->payload);
        $this->assertSame('PX-100', $log->payload['items'][0]['sku']);
        $this->assertSame(WorkflowActionType::CallWebhookMock->value, $log->result['actions'][0]['type']);
    }

    public function test_engine_logs_skipped_rules_with_same_payload_snapshot(): void
    {
        AutomationRule::create([
            'name' => 'Skipped order rule',
            'trigger_event' => WorkflowTriggerEvent::OrderPlaced->value,
            'conditions_json' => [
                ['field' => 'order.grand_total', 'operator' => 'greater_than', 'value' => 1000],
            ],
            'actions_json' => [
                ['type' => WorkflowActionType::NotifySupplier->value, 'config' => []],
            ],
            'status' => AutomationRuleStatus::Active,
            'priority' => 1,
            'run_async' => false,
        ]);

        $payload = $this->payload(grandTotal: '250.00');

        app(WorkflowEngineService::class)->handle(WorkflowTriggerEvent::OrderPlaced->value, $payload);

        $log = WorkflowLog::firstOrFail();

        $this->assertSame(WorkflowLogStatus::Skipped, $log->status);
        $this->assertSame($payload, $log->payload);
        $this->assertFalse($log->result['condition_matched']);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(string $grandTotal): array
    {
        return [
            'order' => [
                'id' => 10,
                'order_number' => 'PO-100',
                'status' => 'confirmed',
                'grand_total' => $grandTotal,
            ],
            'buyer' => [
                'id' => 20,
                'name' => 'Buyer',
                'email' => 'buyer@example.com',
            ],
            'items' => [
                [
                    'product_id' => 30,
                    'supplier_id' => 40,
                    'sku' => 'PX-100',
                    'quantity' => 2,
                    'unit_price' => '375.00',
                    'total' => $grandTotal,
                ],
            ],
            'invoice' => [
                'invoice_number' => 'INV-100',
            ],
        ];
    }
}
