<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Workflow\Models\WorkflowLog;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\WorkflowLogResource;

class WorkflowLogController extends Controller
{
    use AppliesApiFilters;

    public function index(ApiIndexRequest $request)
    {
        $this->authorize('viewAny', WorkflowLog::class);

        $query = WorkflowLog::query()->with('rule');

        $this->applySearch($query, $request, ['trigger_event', 'error']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'executed_at']);

        return WorkflowLogResource::collection($query->paginate($request->perPage())->withQueryString());
    }

    public function show(WorkflowLog $workflowLog): WorkflowLogResource
    {
        $this->authorize('view', $workflowLog);

        return WorkflowLogResource::make($workflowLog->load('rule'));
    }
}
