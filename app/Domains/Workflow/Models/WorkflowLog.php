<?php

namespace App\Domains\Workflow\Models;

use App\Domains\Workflow\Enums\WorkflowLogStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowLog extends Model
{
    protected $fillable = [
        'rule_id',
        'trigger_event',
        'payload',
        'status',
        'result',
        'error',
        'executed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
        'executed_at' => 'datetime',
        'status' => WorkflowLogStatus::class,
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AutomationRule::class, 'rule_id');
    }
}
