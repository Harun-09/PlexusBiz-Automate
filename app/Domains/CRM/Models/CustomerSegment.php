<?php

namespace App\Domains\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSegment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'description',
        'filters_json',
    ];

    protected $casts = [
        'filters_json' => 'array',
    ];
}
