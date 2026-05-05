<?php

namespace Tests\Feature\Support;

use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Support\Enums\TicketPriority;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Models\User;
use Database\Seeders\ECommerceSeeder;
use Database\Seeders\RbacSeeder;
use Database\Seeders\SupportSeeder;
use Database\Seeders\WorkflowSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SupportTicketManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_create_reply_and_view_support_ticket_workflow(): void
    {
        $this->seed([
            RbacSeeder::class,
            ECommerceSeeder::class,
            SupportSeeder::class,
            WorkflowSeeder::class,
        ]);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $supplier = Supplier::query()->firstOrFail();
        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $this->actingAs($buyer)
            ->get('/support/tickets/create')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Support/Tickets/Create')
                ->where('priorities.1', TicketPriority::Normal->value));

        $this->actingAs($buyer)
            ->post('/support/tickets', [
                'subject' => 'Shipping tracking ETA',
                'description' => 'Please help me check the shipping tracking ETA for this order.',
                'priority' => TicketPriority::High->value,
                'supplier_id' => $supplier->id,
            ])
            ->assertRedirect();

        $ticket = SupportTicket::query()->latest('id')->firstOrFail();

        $this->assertSame($buyer->id, $ticket->requester_id);
        $this->assertSame($supplier->id, $ticket->supplier_id);
        $this->assertSame(TicketStatus::WaitingSupplier, $ticket->status);
        $this->assertDatabaseHas('support_messages', [
            'support_ticket_id' => $ticket->id,
            'sender_type' => 'chatbot',
        ]);

        $this->actingAs($buyer)
            ->get("/support/tickets/{$ticket->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Support/Tickets/Show')
                ->where('ticket.ticket_number', $ticket->ticket_number));

        $this->actingAs($buyer)
            ->post("/support/tickets/{$ticket->id}/reply", [
                'message' => 'I can share screenshots if needed.',
            ])
            ->assertRedirect();

        $ticket->refresh();

        $this->assertSame(TicketStatus::Open, $ticket->status);
        $this->assertDatabaseHas('support_messages', [
            'support_ticket_id' => $ticket->id,
            'message' => 'I can share screenshots if needed.',
        ]);

        $this->actingAs($admin)
            ->put("/support/tickets/{$ticket->id}/status", [
                'status' => TicketStatus::Resolved->value,
            ])
            ->assertRedirect();

        $ticket->refresh();

        $this->assertSame(TicketStatus::Resolved, $ticket->status);
        $this->assertNotNull($ticket->resolved_at);

        $this->actingAs($admin)
            ->put("/support/tickets/{$ticket->id}/assign", [
                'assigned_to' => $admin->id,
            ])
            ->assertRedirect();

        $ticket->refresh();

        $this->assertSame($admin->id, $ticket->assigned_to);
    }
}
