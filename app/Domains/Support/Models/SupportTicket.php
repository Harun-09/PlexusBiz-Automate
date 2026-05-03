<?php

namespace App\Domains\Support\Models;

use App\Domains\CRM\Models\Customer;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Enums\TicketPriority;
use App\Domains\Support\Enums\TicketStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'requester_id',
        'supplier_id',
        'order_id',
        'customer_id',
        'assigned_to',
        'channel',
        'subject',
        'description',
        'priority',
        'status',
        'tags_json',
        'metadata_json',
        'last_message_at',
        'resolved_at',
    ];

    protected $casts = [
        'channel' => SupportChannel::class,
        'priority' => TicketPriority::class,
        'status' => TicketStatus::class,
        'tags_json' => 'array',
        'metadata_json' => 'array',
        'last_message_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->latest();
    }

    public function supplierNotifications(): HasMany
    {
        return $this->hasMany(SupplierNotification::class);
    }
}
