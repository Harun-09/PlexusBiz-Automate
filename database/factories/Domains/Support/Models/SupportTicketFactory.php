<?php

namespace Database\Factories\Domains\Support\Models;

use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Enums\TicketPriority;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        return [
            'ticket_number' => 'TKT-'.now()->format('Ymd').'-'.fake()->unique()->numerify('#####'),
            'requester_id' => User::factory(),
            'supplier_id' => Supplier::factory(),
            'channel' => SupportChannel::Web,
            'subject' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'priority' => TicketPriority::Normal,
            'status' => TicketStatus::Open,
            'tags_json' => ['factory'],
            'metadata_json' => ['source' => 'factory'],
            'last_message_at' => now(),
        ];
    }
}
