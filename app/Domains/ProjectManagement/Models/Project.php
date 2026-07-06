<?php

namespace App\Domains\ProjectManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }
}
