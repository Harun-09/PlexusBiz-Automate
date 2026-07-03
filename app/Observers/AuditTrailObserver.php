<?php

namespace App\Observers;

use App\Support\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditTrailObserver
{
    protected AuditLogger $logger;

    public function __construct(AuditLogger $logger)
    {
        $this->logger = $logger;
    }

    public function created(Model $model): void
    {
        $this->logAction($model, 'created', null, $model->toArray());
    }

    public function updated(Model $model): void
    {
        $before = array_intersect_key($model->getOriginal(), $model->getDirty());
        $after = $model->getDirty();
        $this->logAction($model, 'updated', $before, $after);
    }

    public function deleted(Model $model): void
    {
        $this->logAction($model, 'deleted', $model->toArray(), null);
    }

    protected function logAction(Model $model, string $action, ?array $before, ?array $after): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $moduleKey = strtolower(class_basename($model));

        $this->logger->record(
            actor: $user,
            moduleKey: $moduleKey,
            action: "{$moduleKey}.{$action}",
            description: ucfirst($moduleKey) . " was {$action}.",
            subjectType: get_class($model),
            subjectId: $model->getKey(),
            subjectLabel: $model->name ?? $model->title ?? (string) $model->getKey(),
            before: $before ?? [],
            after: $after ?? [],
            metadata: [],
            ipAddress: request()->ip(),
            userAgent: request()->userAgent()
        );
    }
}
