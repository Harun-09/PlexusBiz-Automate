<?php

namespace Tests\Feature\CRM;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Models\Interaction;
use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\Notifications\Models\Message;
use App\Domains\Notifications\Services\MessageService;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_linked_messages_create_message_interactions(): void
    {
        $this->seed(RbacSeeder::class);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $customer = app(CustomerProfileService::class)->ensureForUser($buyer, [
            'company_name' => 'Buyer Messaging Ltd',
            'contact_name' => 'Buyer Messaging Contact',
        ]);

        $messages = app(MessageService::class);

        $buyerMessage = $messages->sendToUser(
            receiver: $admin,
            subject: 'Need shipping update',
            body: 'Can you share the delivery ETA?',
            sender: $buyer,
        );

        $adminMessage = $messages->sendToUser(
            receiver: $buyer,
            subject: 'Shipping update',
            body: 'Your order is on the way.',
            sender: $admin,
        );

        $customerMessage = $messages->sendToCustomer(
            customerId: $customer->id,
            subject: 'Invoice ready',
            body: 'Download the invoice from your purchase history.',
            sender: $admin,
        );

        $this->assertSame($customer->id, $buyerMessage->customer_id);
        $this->assertSame($customer->id, $adminMessage->customer_id);
        $this->assertSame($customer->id, $customerMessage->customer_id);
        $this->assertSame(3, Message::count());

        $query = Interaction::where('customer_id', $customer->id)
            ->where('type', InteractionType::Message->value);

        $this->assertSame(3, $query->count());
        $this->assertSame(1, (clone $query)->where('direction', 'inbound')->count());
        $this->assertSame(2, (clone $query)->where('direction', 'outbound')->count());

        $latest = $query->latest('id')->firstOrFail();

        $this->assertSame(InteractionType::Message, $latest->type);
        $this->assertStringContainsString('Invoice ready', $latest->summary);
        $this->assertInstanceOf(Message::class, $latest->related);
        $this->assertSame($customerMessage->id, $latest->related->id);
    }
}
