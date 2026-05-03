<?php

namespace App\Domains\Workflow\Enums;

enum WorkflowLogStatus: string
{
    case Running = 'running';
    case Success = 'success';
    case Failed = 'failed';
    case Skipped = 'skipped';
}
