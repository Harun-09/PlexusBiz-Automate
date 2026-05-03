<?php

namespace App\Domains\Support\Models;

use App\Domains\Support\Enums\SupportMessageSenderType;
use App\Domains\Support\Enums\SupportMessageVisibility;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    protected $fillable = [
        'support_ticket_id',
        'sender_id',
        'sender_type',
        'visibility',
        'message',
        'payload_json',
    ];

    protected $casts = [
        'sender_type' => SupportMessageSenderType::class,
        'visibility' => SupportMessageVisibility::class,
        'payload_json' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
