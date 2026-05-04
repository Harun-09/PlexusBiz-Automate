<?php

namespace Tests\Feature\Support;

use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Support\Services\SupportTicketService;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Models\User;
use Database\Seeders\ECommerceSeeder;
use Database\Seeders\RbacSeeder;
use Database\Seeders\SupportSeeder;
use Database\Seeders\WorkflowSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SupportAutomationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_creation_adds_auto_reply_supplier_notification_and_workflow_snapshot(): void
    {
        $this->seed([
            RbacSeeder::class,
            ECommerceSeeder::class,
            SupportSeeder::class,
            WorkflowSeeder::class,
        ]);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $supplier = Supplier::query()->firstOrFail();

        $ticket = app(SupportTicketService::class)->createTicket($buyer, [
            'subject' => 'Shipping ETA needed',
            'description' => 'Please help me check the shipping tracking ETA for this order.',
            'supplier_id' => $supplier->id,
        ], SupportChannel::Web);

        $this->assertSame(TicketStatus::WaitingSupplier, $ticket->status);
        $this->assertDatabaseHas('support_messages', [
            'support_ticket_id' => $ticket->id,
            'sender_type' => 'chatbot',
        ]);
        $this->assertDatabaseHas('supplier_notifications', [
            'supplier_id' => $supplier->id,
            'support_ticket_id' => $ticket->id,
            'type' => 'support.ticket.created',
        ]);

        $log = WorkflowLog::where('trigger_event', WorkflowTriggerEvent::TicketCreated->value)->firstOrFail();

        $this->assertSame(WorkflowLogStatus::Success, $log->status);
        $this->assertSame($ticket->ticket_number, $log->payload['ticket']['number']);
        $this->assertSame('buyer@plexus.test', $log->payload['requester']['email']);
        $this->assertNotEmpty($log->payload['supplier_notifications']);
    }

    public function test_chatbot_endpoint_matches_faq_and_can_open_ticket(): void
    {
        $this->seed([
            RbacSeeder::class,
            ECommerceSeeder::class,
            SupportSeeder::class,
            WorkflowSeeder::class,
        ]);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $supplier = Supplier::query()->firstOrFail();

        Sanctum::actingAs($buyer);

        $response = $this->postJson('/api/support/chatbot/message', [
            'message' => 'Can you check the shipping tracking eta for my order?',
            'create_ticket' => true,
            'supplier_id' => $supplier->id,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.source', 'faq')
            ->assertJsonPath('data.ticket.status', TicketStatus::WaitingSupplier->value);

        $ticket = SupportTicket::query()->findOrFail($response->json('data.ticket.id'));

        $this->assertSame($buyer->id, $ticket->requester_id);
        $this->assertSame(SupportChannel::Chatbot, $ticket->channel);
        $this->assertDatabaseHas('support_messages', [
            'support_ticket_id' => $ticket->id,
            'sender_type' => 'chatbot',
        ]);
    }
}
