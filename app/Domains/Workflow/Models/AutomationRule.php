<?php

namespace App\Domains\Workflow\Models;

use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Support\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutomationRule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'trigger_event',
        'conditions_json',
        'actions_json',
        'status',
        'priority',
        'run_async',
    ];

    protected $casts = [
        'conditions_json' => 'array',
        'actions_json' => 'array',
        'status' => AutomationRuleStatus::class,
        'run_async' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::created(function (AutomationRule $rule): void {
            app(AuditLogger::class)->record(
                actor: auth()->user(),
                moduleKey: 'workflow',
                action: 'workflow.rules.created',
                description: 'Automation rule created.',
                subjectType: self::class,
                subjectId: $rule->id,
                subjectLabel: $rule->name,
                after: $rule->auditSnapshot(),
                metadata: [
                    'trigger_event' => $rule->trigger_event,
                ],
            );
        });

        static::updating(function (AutomationRule $rule): void {
            $before = $rule->auditSnapshot();
            $dirty = $rule->getDirty();
            $after = array_merge($before, $dirty);

            app(AuditLogger::class)->record(
                actor: auth()->user(),
                moduleKey: 'workflow',
                action: 'workflow.rules.updated',
                description: 'Automation rule updated.',
                subjectType: self::class,
                subjectId: $rule->id,
                subjectLabel: $rule->name,
                before: $before,
                after: $after,
                metadata: [
                    'trigger_event' => $rule->trigger_event,
                    'changed_fields' => array_values(array_diff(array_keys($dirty), ['updated_at'])),
                ],
            );
        });

        static::deleting(function (AutomationRule $rule): void {
            app(AuditLogger::class)->record(
                actor: auth()->user(),
                moduleKey: 'workflow',
                action: 'workflow.rules.deleted',
                description: 'Automation rule deleted.',
                subjectType: self::class,
                subjectId: $rule->id,
                subjectLabel: $rule->name,
                before: $rule->auditSnapshot(),
                metadata: [
                    'trigger_event' => $rule->trigger_event,
                ],
            );
        });
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowLog::class, 'rule_id');
    }

    /**
     * @return array<string, mixed>
     */
    private function auditSnapshot(): array
    {
        return [
            'name' => $this->name,
            'trigger_event' => $this->trigger_event,
            'conditions_json' => $this->conditions_json,
            'actions_json' => $this->actions_json,
            'status' => $this->status->value,
            'priority' => $this->priority,
            'run_async' => $this->run_async,
        ];
    }
}
