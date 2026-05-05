<?php

namespace App\Http\Controllers\Workflow;

use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Workflow\Enums\WorkflowActionType;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Models\AutomationRule;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AutomationRuleController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Workflow/Rules/Create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        AutomationRule::create($this->payload($this->validateRule($request)));

        return redirect()
            ->route('workflow.rules.index')
            ->with('success', 'Automation rule created successfully.');
    }

    public function edit(AutomationRule $rule): Response
    {
        return Inertia::render('Workflow/Rules/Edit', array_merge($this->formOptions(), [
            'rule' => [
                'id' => $rule->id,
                'name' => $rule->name,
                'trigger_event' => $rule->trigger_event,
                'status' => $rule->status->value,
                'priority' => $rule->priority,
                'run_async' => $rule->run_async,
                'condition_field' => data_get($rule->conditions_json, '0.field', ''),
                'condition_operator' => data_get($rule->conditions_json, '0.operator', 'equals'),
                'condition_value' => data_get($rule->conditions_json, '0.value', ''),
                'action_types' => collect($rule->actions_json ?? [])->pluck('type')->values()->all(),
                'subject' => data_get($rule->actions_json, '0.config.subject', ''),
                'message' => data_get($rule->actions_json, '0.config.body', data_get($rule->actions_json, '0.config.message', '')),
            ],
        ]));
    }

    public function update(Request $request, AutomationRule $rule): RedirectResponse
    {
        $rule->forceFill($this->payload($this->validateRule($request)))->save();

        return redirect()
            ->route('workflow.rules.index')
            ->with('success', 'Automation rule updated successfully.');
    }

    public function destroy(AutomationRule $rule): RedirectResponse
    {
        $rule->delete();

        return redirect()
            ->route('workflow.rules.index')
            ->with('success', 'Automation rule deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateRule(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'trigger_event' => ['required', 'string', Rule::in($this->triggers())],
            'status' => ['required', 'string', Rule::in($this->statuses())],
            'priority' => ['required', 'integer', 'min:1', 'max:9999'],
            'run_async' => ['nullable', 'boolean'],
            'condition_field' => ['nullable', 'string', 'max:255'],
            'condition_operator' => ['nullable', 'string', Rule::in($this->operators())],
            'condition_value' => ['nullable', 'string', 'max:1000'],
            'action_types' => ['required', 'array', 'min:1'],
            'action_types.*' => ['required', 'string', Rule::in($this->actions())],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated): array
    {
        return [
            'name' => trim($validated['name']),
            'trigger_event' => $validated['trigger_event'],
            'conditions_json' => $this->conditions($validated),
            'actions_json' => $this->actionsJson($validated),
            'status' => AutomationRuleStatus::from($validated['status']),
            'priority' => (int) $validated['priority'],
            'run_async' => (bool) ($validated['run_async'] ?? false),
        ];
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<int, array<string, mixed>>|null
     */
    private function conditions(array $validated): ?array
    {
        $field = trim((string) ($validated['condition_field'] ?? ''));

        if ($field === '') {
            return null;
        }

        return [[
            'field' => $field,
            'operator' => $validated['condition_operator'] ?? 'equals',
            'value' => $validated['condition_value'] ?? '',
        ]];
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<int, array<string, mixed>>
     */
    private function actionsJson(array $validated): array
    {
        $subject = trim((string) ($validated['subject'] ?? 'PlexusBiz automation'));
        $message = trim((string) ($validated['message'] ?? 'Automation action executed successfully.'));

        return collect($validated['action_types'])
            ->unique()
            ->values()
            ->map(function (string $type) use ($subject, $message): array {
                $config = match ($type) {
                    WorkflowActionType::SendEmail->value => [
                        'to_path' => 'buyer.email',
                        'subject' => $subject !== '' ? $subject : 'PlexusBiz automation email',
                        'body' => $message !== '' ? $message : 'Automation email action executed.',
                    ],
                    WorkflowActionType::SendSms->value => [
                        'to_path' => 'buyer.phone',
                        'body' => $message !== '' ? $message : 'Automation SMS action executed.',
                    ],
                    WorkflowActionType::CreateNotification->value => [
                        'subject' => $subject !== '' ? $subject : 'PlexusBiz notification',
                        'message' => $message !== '' ? $message : 'Automation notification created.',
                    ],
                    WorkflowActionType::NotifySupplier->value => [
                        'subject' => $subject !== '' ? $subject : 'Supplier notification',
                        'message' => $message !== '' ? $message : 'A supplier workflow notification was created.',
                    ],
                    default => [
                        'subject' => $subject,
                        'message' => $message,
                    ],
                };

                return [
                    'type' => $type,
                    'config' => $config,
                ];
            })
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'triggers' => $this->triggers(),
            'actions' => $this->actions(),
            'statuses' => $this->statuses(),
            'operators' => $this->operators(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function triggers(): array
    {
        return array_map(fn (WorkflowTriggerEvent $event): string => $event->value, WorkflowTriggerEvent::cases());
    }

    /**
     * @return array<int, string>
     */
    private function actions(): array
    {
        return [
            WorkflowActionType::SendEmail->value,
            WorkflowActionType::SendSms->value,
            WorkflowActionType::CreateNotification->value,
            WorkflowActionType::NotifySupplier->value,
            WorkflowActionType::AssignTask->value,
            WorkflowActionType::CreateTicketAutoReply->value,
            WorkflowActionType::CallWebhookMock->value,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function statuses(): array
    {
        return array_map(fn (AutomationRuleStatus $status): string => $status->value, AutomationRuleStatus::cases());
    }

    /**
     * @return array<int, string>
     */
    private function operators(): array
    {
        return [
            'equals',
            'not_equals',
            'greater_than',
            'greater_than_or_equal',
            'less_than',
            'contains',
            'truthy',
        ];
    }
}
