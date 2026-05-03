<?php

namespace App\Domains\Support\Models;

use App\Domains\Support\Enums\SupportFaqStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportFaq extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'question',
        'answer',
        'keywords_json',
        'status',
        'priority',
    ];

    protected $casts = [
        'keywords_json' => 'array',
        'status' => SupportFaqStatus::class,
    ];
}
