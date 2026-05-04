<?php

namespace App\Support\Audit;

use App\Models\User;
use App\Support\Audit\Models\AuditLog;

class AuditLogger
{
    public function record(
        ?User $actor,
        string $moduleKey,
        string $action,
        ?string $description = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?string $subjectLabel = null,
        array $before = [],
        array $after = [],
        array $metadata = [],
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): AuditLog {
        return AuditLog::create([
            'actor_id' => $actor?->id,
            'module_key' => $moduleKey,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'subject_label' => $subjectLabel,
            'description' => $description,
            'before_json' => $before !== [] ? $before : null,
            'after_json' => $after !== [] ? $after : null,
            'metadata_json' => $metadata !== [] ? $metadata : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
