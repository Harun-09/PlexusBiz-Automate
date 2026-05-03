<?php

namespace App\Domains\Social\Models;

use App\Domains\Social\Enums\SocialPlatform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'platform',
        'name',
        'handle',
        'status',
        'credentials_json',
    ];

    protected $casts = [
        'platform' => SocialPlatform::class,
        'credentials_json' => 'encrypted:array',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(SocialPost::class);
    }
}
