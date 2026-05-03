<?php

namespace App\Domains\Workflow\Models;

use App\Domains\Workflow\Enums\AutomationRuleStatus;
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

    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowLog::class, 'rule_id');
    }
}
