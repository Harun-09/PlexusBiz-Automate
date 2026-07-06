<?php

namespace App\Domains\ProjectManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function task()
    {
        return $this->belongsTo(ProjectTask::class, 'project_task_id');
    }
}
