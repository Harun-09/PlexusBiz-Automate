<?php

namespace App\Domains\Marketing\Models;

use App\Domains\Marketing\Enums\MessageChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'template_key',
        'channel',
        'name',
        'subject',
        'body',
        'variables',
        'status',
    ];

    protected $casts = [
        'channel' => MessageChannel::class,
        'variables' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
