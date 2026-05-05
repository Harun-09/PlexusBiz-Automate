<?php

namespace App\Listeners\Marketing;

use App\Domains\ECommerce\Events\OrderPlaced;
use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Workflow\Enums\WorkflowActionType;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Models\AutomationRule;
use App\Domains\Marketing\Services\MarketingTriggerService;

class SendOrderConfirmationEmail
{
    public function __construct(private readonly MarketingTriggerService $marketing)
    {
    }

    public function handle(OrderPlaced $event): void
    {
        if ($this->workflowAlreadyHandlesOrderConfirmation()) {
            return;
        }

        $order = $event->order->loadMissing(['customer', 'invoice']);

        if (! $order->customer) {
            return;
        }

        $this->marketing->orderConfirmation($order->customer, [
            'order_number' => $order->order_number,
            'invoice_url' => $order->invoice ? route('invoices.show', $order->invoice) : '',
            'grand_total' => $order->grand_total,
        ]);
    }

    private function workflowAlreadyHandlesOrderConfirmation(): bool
    {
        return AutomationRule::query()
            ->where('trigger_event', WorkflowTriggerEvent::OrderPlaced->value)
            ->where('status', AutomationRuleStatus::Active->value)
            ->get()
            ->contains(function (AutomationRule $rule): bool {
                return collect($rule->actions_json ?? [])
                    ->contains(fn (array $action): bool => ($action['type'] ?? null) === WorkflowActionType::SendEmail->value);
            });
    }
}
