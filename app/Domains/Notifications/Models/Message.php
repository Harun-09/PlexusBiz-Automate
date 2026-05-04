<?php

namespace App\Domains\Notifications\Models;

use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Enums\MessageStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'customer_id',
        'channel',
        'subject',
        'body',
        'payload_json',
        'status',
        'sent_at',
        'read_at',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'status' => MessageStatus::class,
        'channel' => MessageChannel::class,
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\CRM\Models\Customer::class);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => MessageStatus::Sent,
            'sent_at' => now(),
        ]);
    }

    public function markAsRead(): void
    {
        $this->update([
            'status' => MessageStatus::Read,
            'read_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => MessageStatus::Failed,
            'payload_json' => array_merge($this->payload_json ?? [], ['error' => $error]),
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('receiver_id', $userId);
    }

    public function scopeByChannel($query, MessageChannel $channel)
    {
        return $query->where('channel', $channel->value);
    }
}
