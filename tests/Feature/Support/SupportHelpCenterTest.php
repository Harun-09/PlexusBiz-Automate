<?php

namespace Tests\Feature\Support;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Domains\Support\Mail\SupportEscalationMail;
use App\Domains\CRM\Models\Customer;
use Database\Seeders\RbacSeeder;
use Tests\TestCase;

class SupportHelpCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_help_center_page_requires_auth(): void
    {
        $this->get('/support/help-center')
            ->assertRedirect('/login');
    }

    public function test_help_center_renders_for_authenticated_buyer(): void
    {
        $this->seed(RbacSeeder::class);
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');

        $this->actingAs($buyer)
            ->get('/support/help-center')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Support/HelpCenter')
                ->has('faqs')
                ->has('recentTickets')
                ->has('recentOrders')
            );
    }

    public function test_help_center_message_returns_chatbot_response(): void
    {
        $this->seed(RbacSeeder::class);
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');

        $this->actingAs($buyer)
            ->postJson('/support/help-center/message', [
                'message' => 'Hello support!'
            ])
            ->assertOk()
            ->assertJsonStructure([
                'answer',
                'confidence',
                'source',
                'matched_keywords',
                'ticket',
                'escalate'
            ]);
    }

    public function test_help_center_email_escalation_dispatches_email_and_logs_interaction(): void
    {
        Mail::fake();

        $this->seed(RbacSeeder::class);
        
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        
        // Seed CRM Customer for buyer
        $customer = Customer::factory()->create(['user_id' => $buyer->id]);

        $this->actingAs($buyer)
            ->postJson('/support/help-center/email-escalate', [
                'subject' => 'URGENT: Order issue',
                'description' => 'I did not receive my shipment yet.',
            ])
            ->assertOk();

        Mail::assertSent(SupportEscalationMail::class, function ($mail) use ($buyer) {
            return $mail->user->id === $buyer->id &&
                   $mail->subjectLine === 'URGENT: Order issue' &&
                   $mail->description === 'I did not receive my shipment yet.';
        });

        $this->assertDatabaseHas('interactions', [
            'customer_id' => $customer->id,
            'user_id' => $buyer->id,
            'type' => 'email',
            'direction' => 'inbound',
        ]);
    }
}
