<?php

namespace App\Policies;

use App\Domains\Workflow\Models\WorkflowLog;
use App\Models\User;

class WorkflowLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager', 'workflow_manager']);
    }

    public function view(User $user, WorkflowLog $workflowLog): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager', 'workflow_manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager', 'workflow_manager']);
    }

    public function update(User $user, WorkflowLog $workflowLog): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager', 'workflow_manager']);
    }

    public function delete(User $user, WorkflowLog $workflowLog): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager', 'workflow_manager']);
    }
}
