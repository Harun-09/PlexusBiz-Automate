<?php

namespace Tests\Feature\Api;

use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Services\SupportTicketService;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_list_active_products_with_pagination_and_filters(): void
    {
        $this->seed(DatabaseSeeder::class);

        Sanctum::actingAs(User::where('email', 'buyer@plexus.test')->firstOrFail());

        $this->getJson('/api/v1/products?search=pump&status=active&per_page=5')
            ->assertOk()
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('data.0.sku', 'PX-PUMP-100')
            ->assertJsonPath('data.0.status', 'active');
    }

    public function test_customer_api_is_policy_protected(): void
    {
        $this->seed(DatabaseSeeder::class);

        Sanctum::actingAs(User::where('email', 'buyer@plexus.test')->firstOrFail());
        $this->getJson('/api/v1/customers')->assertForbidden();

        Sanctum::actingAs(User::where('email', 'admin@plexus.test')->firstOrFail());
        $this->getJson('/api/v1/customers?search=plexus&per_page=10')
            ->assertOk()
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('data.0.email', 'buyer@plexus.test');
    }

    public function test_marketing_can_read_campaign_social_and_workflow_resources(): void
    {
        $this->seed(DatabaseSeeder::class);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $supplier = Supplier::query()->firstOrFail();

        app(SupportTicketService::class)->createTicket($buyer, [
            'subject' => 'Shipping tracking',
            'description' => 'Please update shipping tracking for my order.',
            'supplier_id' => $supplier->id,
        ], SupportChannel::Web);

        Sanctum::actingAs(User::where('email', 'marketing@plexus.test')->firstOrFail());

        $this->getJson('/api/v1/campaigns?search=welcome')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'priority-wholesale-welcome');

        $this->getJson('/api/v1/social-posts?status=scheduled')
            ->assertOk()
            ->assertJsonPath('data.0.status', 'scheduled');

        $this->getJson('/api/v1/workflow-logs?status=success')
            ->assertOk()
            ->assertJsonPath('data.0.trigger_event', 'ticket.created');
    }

    public function test_support_ticket_api_is_scoped_by_ticket_ownership(): void
    {
        $this->seed(DatabaseSeeder::class);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $supplier = Supplier::query()->firstOrFail();
        $ticket = app(SupportTicketService::class)->createTicket($buyer, [
            'subject' => 'Shipping ETA needed',
            'description' => 'Need shipping tracking ETA.',
            'supplier_id' => $supplier->id,
        ], SupportChannel::Chatbot);

        Sanctum::actingAs($buyer);
        $response = $this->getJson('/api/v1/support-tickets?status='.TicketStatus::WaitingSupplier->value)
            ->assertOk();

        $this->assertContains($ticket->ticket_number, collect($response->json('data'))->pluck('ticket_number')->all());

        Sanctum::actingAs(User::where('email', 'supplier@plexus.test')->firstOrFail());
        $this->getJson('/api/v1/support-tickets/'.$ticket->id)
            ->assertOk()
            ->assertJsonPath('data.ticket_number', $ticket->ticket_number);

        Sanctum::actingAs(User::where('email', 'marketing@plexus.test')->firstOrFail());
        $this->getJson('/api/v1/support-tickets')->assertForbidden();
    }
}
