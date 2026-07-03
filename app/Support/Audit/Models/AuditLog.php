<?php

namespace App\Support\Audit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_id',
        'module_key',
        'action',
        'subject_type',
        'subject_id',
        'subject_label',
        'description',
        'before_json',
        'after_json',
        'metadata_json',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'before_json' => 'array',
        'after_json' => 'array',
        'metadata_json' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::updating(function ($auditLog) {
            throw new \Exception('Audit logs are immutable and cannot be updated.');
        });

        static::deleting(function ($auditLog) {
            throw new \Exception('Audit logs are immutable and cannot be deleted.');
        });
    }
}
