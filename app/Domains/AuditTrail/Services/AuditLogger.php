<?php

namespace App\Domains\AuditTrail\Services;

use App\Domains\AuditTrail\Models\AuditTrail;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * Log a change to any Eloquent model.
     */
    public static function log(Model $model, string $action, ?array $oldValues = null, ?array $newValues = null, ?int $userId = null)
    {
        return AuditTrail::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'CLI',
        ]);
    }
}
