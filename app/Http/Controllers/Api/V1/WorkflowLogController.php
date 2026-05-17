<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\WorkflowLogResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkflowLogController extends Controller
{
    use AppliesApiFilters;
    use FormatsApiResponses;

    public function index(ApiIndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', WorkflowLog::class);

        $query = WorkflowLog::query()->with('rule');

        $this->applySearch($query, $request, ['trigger_event', 'error']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'executed_at']);
        $query->orderByDesc('id');

        $paginator = $query->paginate($request->perPage())->withQueryString();

        return $this->paginatedResourceResponse(
            paginator: $paginator,
            resourceClass: WorkflowLogResource::class,
            message: 'Workflow logs fetched successfully.',
        );
    }

    public function show(WorkflowLog $workflowLog): JsonResponse
    {
        $this->authorize('view', $workflowLog);

        return $this->resourceResponse(
            WorkflowLogResource::make($workflowLog->load('rule')),
            'Workflow log details fetched successfully.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', WorkflowLog::class);

        $validated = $this->validateWorkflowLog($request);
        $workflowLog = WorkflowLog::create([
            'rule_id' => $validated['rule_id'] ?? null,
            'trigger_event' => trim((string) $validated['trigger_event']),
            'payload' => $validated['payload'],
            'status' => $validated['status'],
            'result' => $validated['result'] ?? null,
            'error' => $validated['error'] ?? null,
            'executed_at' => $validated['executed_at'] ?? null,
        ]);

        return $this->resourceResponse(
            WorkflowLogResource::make($workflowLog->load('rule')),
            'Workflow log created successfully',
            201,
        );
    }

    public function update(Request $request, WorkflowLog $workflowLog): JsonResponse
    {
        $this->authorize('update', $workflowLog);

        $validated = $this->validateWorkflowLog($request, $workflowLog);

        if ($validated === []) {
            return $this->resourceResponse(
                WorkflowLogResource::make($workflowLog->load('rule')),
                'No changes submitted',
            );
        }

        $payload = [];

        foreach (['rule_id', 'payload', 'result', 'error', 'executed_at'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $validated[$field];
            }
        }

        if (array_key_exists('trigger_event', $validated)) {
            $payload['trigger_event'] = trim((string) $validated['trigger_event']);
        }

        if (array_key_exists('status', $validated)) {
            $payload['status'] = $validated['status'];
        }

        $workflowLog->forceFill($payload)->save();

        return $this->resourceResponse(
            WorkflowLogResource::make($workflowLog->refresh()->load('rule')),
            'Workflow log updated successfully',
        );
    }

    public function destroy(WorkflowLog $workflowLog): JsonResponse
    {
        $this->authorize('delete', $workflowLog);

        $workflowLog->delete();

        return $this->successResponse(
            data: null,
            message: 'Workflow log deleted successfully',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validateWorkflowLog(Request $request, ?WorkflowLog $workflowLog = null): array
    {
        $required = $workflowLog === null ? 'required' : 'sometimes';

        return $request->validate([
            'rule_id' => [$workflowLog === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'exists:automation_rules,id'],
            'trigger_event' => [$required, 'string', 'max:255'],
            'payload' => [$required, 'array'],
            'status' => [$required, 'string', Rule::in($this->statuses())],
            'result' => [$workflowLog === null ? 'nullable' : 'sometimes', 'nullable', 'array'],
            'error' => [$workflowLog === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:5000'],
            'executed_at' => [$workflowLog === null ? 'nullable' : 'sometimes', 'nullable', 'date'],
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function statuses(): array
    {
        return array_map(fn (WorkflowLogStatus $status): string => $status->value, WorkflowLogStatus::cases());
    }
}
