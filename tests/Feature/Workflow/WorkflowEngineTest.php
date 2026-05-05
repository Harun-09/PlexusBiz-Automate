<?php

namespace Tests\Feature\Workflow;

use App\Domains\ECommerce\Events\RfqCreated;
use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Workflow\Enums\WorkflowActionType;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Jobs\RunWorkflowRuleJob;
use App\Domains\Workflow\Models\AutomationRule;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Domains\Workflow\Services\WorkflowEngineService;
use Database\Seeders\WorkflowSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
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

    public function test_seeded_order_placed_rule_executes_email_and_sms_actions(): void
    {
        Mail::fake();

        $this->seed(WorkflowSeeder::class);

        $log = app(WorkflowEngineService::class)
            ->handle(WorkflowTriggerEvent::OrderPlaced->value, $this->payload(grandTotal: '250.00'))
            ->first();

        $actionTypes = collect($log->result['actions'])->pluck('type')->all();

        $this->assertSame(WorkflowLogStatus::Success, $log->status);
        $this->assertContains(WorkflowActionType::SendEmail->value, $actionTypes);
        $this->assertContains(WorkflowActionType::SendSms->value, $actionTypes);
    }

    public function test_rfq_created_event_runs_supplier_notification_rule(): void
    {
        AutomationRule::create([
            'name' => 'RFQ event supplier proof',
            'trigger_event' => WorkflowTriggerEvent::RfqCreated->value,
            'conditions_json' => [
                ['field' => 'rfq.status', 'operator' => 'equals', 'value' => 'open'],
            ],
            'actions_json' => [
                ['type' => WorkflowActionType::NotifySupplier->value, 'config' => ['message' => 'RFQ supplier notification']],
            ],
            'status' => AutomationRuleStatus::Active,
            'priority' => 1,
            'run_async' => false,
        ]);

        RfqCreated::dispatch([
            'rfq' => [
                'id' => 55,
                'rfq_number' => 'RFQ-100',
                'supplier_id' => 40,
                'status' => 'open',
            ],
            'buyer' => [
                'id' => 20,
                'email' => 'buyer@example.com',
            ],
        ]);

        $log = WorkflowLog::firstOrFail();

        $this->assertSame(WorkflowTriggerEvent::RfqCreated->value, $log->trigger_event);
        $this->assertSame(WorkflowLogStatus::Success, $log->status);
        $this->assertSame('RFQ-100', $log->payload['rfq']['rfq_number']);
    }

    public function test_async_rules_queue_the_workflow_job(): void
    {
        Queue::fake();

        AutomationRule::create([
            'name' => 'Async order proof',
            'trigger_event' => WorkflowTriggerEvent::OrderPlaced->value,
            'conditions_json' => null,
            'actions_json' => [
                ['type' => WorkflowActionType::CallWebhookMock->value, 'config' => []],
            ],
            'status' => AutomationRuleStatus::Active,
            'priority' => 1,
            'run_async' => true,
        ]);

        $log = app(WorkflowEngineService::class)
            ->handle(WorkflowTriggerEvent::OrderPlaced->value, $this->payload(grandTotal: '250.00'))
            ->first();

        $this->assertSame(WorkflowLogStatus::Running, $log->status);
        $this->assertTrue($log->result['queued']);
        Queue::assertPushed(RunWorkflowRuleJob::class);
    }

    public function test_scheduler_command_closes_stale_running_workflow_logs(): void
    {
        $log = WorkflowLog::create([
            'trigger_event' => WorkflowTriggerEvent::OrderPlaced->value,
            'payload' => $this->payload(grandTotal: '250.00'),
            'status' => WorkflowLogStatus::Running,
            'result' => ['queued' => true],
            'executed_at' => now()->subHour(),
        ]);

        $this->artisan('workflow:close-stale-runs', ['--minutes' => 30])
            ->assertExitCode(0);

        $log->refresh();

        $this->assertSame(WorkflowLogStatus::Failed, $log->status);
        $this->assertSame('Workflow run timed out before the queue worker completed it.', $log->error);
        $this->assertSame(30, $log->result['stale_run_closed']['after_minutes']);
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
                'phone' => '+8801700000000',
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
