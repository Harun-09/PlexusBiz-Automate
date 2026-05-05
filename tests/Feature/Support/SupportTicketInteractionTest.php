<?php

namespace Tests\Feature\Support;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Models\Interaction;
use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Services\SupportTicketService;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_messages_create_support_ticket_interactions_for_customers(): void
    {
        $this->seed(RbacSeeder::class);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $customer = app(CustomerProfileService::class)->ensureForUser($buyer, [
            'company_name' => 'Support Buyer Ltd',
            'contact_name' => 'Support Buyer Contact',
        ]);

        $tickets = app(SupportTicketService::class);

        $ticket = $tickets->createTicket($buyer, [
            'subject' => 'Packaging issue on arrival',
            'description' => 'The package arrived damaged and needs review.',
            'priority' => 'normal',
        ], SupportChannel::Web);

        $this->assertSame($customer->id, $ticket->customer_id);
        $this->assertSame(1, $ticket->messages()->count());
        $this->assertSame(1, Interaction::where('customer_id', $customer->id)->where('type', InteractionType::SupportTicket->value)->count());
        $this->assertSame(1, Interaction::where('customer_id', $customer->id)->where('direction', 'inbound')->count());

        $tickets->replyTicket($ticket->refresh(), $admin, [
            'message' => 'We are reviewing the packaging issue now.',
        ]);

        $interactions = Interaction::where('customer_id', $customer->id)
            ->where('type', InteractionType::SupportTicket->value);

        $this->assertSame(2, $interactions->count());
        $this->assertSame(1, (clone $interactions)->where('direction', 'outbound')->count());

        $latest = $interactions->latest('id')->firstOrFail();

        $this->assertSame(InteractionType::SupportTicket, $latest->type);
        $this->assertStringContainsString($ticket->ticket_number, $latest->summary);
        $this->assertSame($ticket->id, $latest->related->id);
    }
}
