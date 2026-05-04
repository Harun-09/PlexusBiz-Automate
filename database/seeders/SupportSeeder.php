<?php

namespace Database\Seeders;

use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Enums\SupportMessageSenderType;
use App\Domains\Support\Enums\SupportMessageVisibility;
use App\Domains\Support\Enums\SupportFaqStatus;
use App\Domains\Support\Enums\TicketPriority;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportFaq;
use App\Domains\Support\Models\SupportMessage;
use App\Domains\Support\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SupportSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFaqs();
        $this->seedTickets();
    }

    private function seedFaqs(): void
    {
        SupportFaq::updateOrCreate(
            ['question' => 'How can I check order shipping status?'],
            [
                'answer' => 'Your order shipping status is available from the Orders workspace. If the supplier has not updated tracking yet, a support ticket will notify the supplier.',
                'keywords_json' => ['shipping', 'shipment', 'tracking', 'delivery', 'eta', 'order status'],
                'status' => SupportFaqStatus::Active,
                'priority' => 10,
            ],
        );

        SupportFaq::updateOrCreate(
            ['question' => 'How do I request a supplier quote?'],
            [
                'answer' => 'Open the marketplace product and submit an RFQ with quantity, target price, and delivery notes. The supplier will respond from their order workspace.',
                'keywords_json' => ['rfq', 'quote', 'bulk price', 'supplier quote', 'request quote'],
                'status' => SupportFaqStatus::Active,
                'priority' => 20,
            ],
        );
    }

    private function seedTickets(): void
    {
        $buyer = User::where('email', 'buyer@plexus.test')->first();
        $supplierUser = User::where('email', 'supplier@plexus.test')->first();
        $admin = User::where('email', 'admin@plexus.test')->first();

        if (! $buyer || ! $supplierUser || ! $admin) {
            return;
        }

        $supplier = Supplier::firstOrCreate(
            ['user_id' => $supplierUser->id],
            [
                'company_name' => 'Plexus Supply Co',
                'slug' => Str::slug('Plexus Supply Co'),
                'contact_email' => 'supplier@plexus.test',
                'status' => SupplierStatus::Approved,
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ],
        );

        $tickets = [
            [
                'number' => 'TKT-'.now()->year.'-001',
                'subject' => 'Order delivery delay inquiry',
                'description' => 'Customer asked for a shipping update on an overdue order.',
                'priority' => TicketPriority::Normal,
                'status' => TicketStatus::Resolved,
                'last_message_at' => now()->subHours(8),
                'resolved_at' => now()->subHours(3),
                'message' => 'The supplier confirmed the shipment will arrive tomorrow morning.',
            ],
            [
                'number' => 'TKT-'.now()->year.'-002',
                'subject' => 'Product quality question',
                'description' => 'Buyer reported a question about product specifications before confirming the order.',
                'priority' => TicketPriority::High,
                'status' => TicketStatus::WaitingSupplier,
                'last_message_at' => now()->subHours(2),
                'resolved_at' => null,
                'message' => 'The supplier has been notified and is preparing a response.',
            ],
        ];

        foreach ($tickets as $ticketData) {
            $ticket = SupportTicket::updateOrCreate(
                ['ticket_number' => $ticketData['number']],
                [
                    'requester_id' => $buyer->id,
                    'supplier_id' => $supplier->id,
                    'channel' => SupportChannel::Web,
                    'subject' => $ticketData['subject'],
                    'description' => $ticketData['description'],
                    'priority' => $ticketData['priority'],
                    'status' => $ticketData['status'],
                    'assigned_to' => $admin->id,
                    'last_message_at' => $ticketData['last_message_at'],
                    'resolved_at' => $ticketData['resolved_at'],
                ],
            );

            SupportMessage::updateOrCreate(
                [
                    'support_ticket_id' => $ticket->id,
                    'sender_id' => $buyer->id,
                    'message' => 'I need help with this issue. Please assist.',
                ],
                [
                    'sender_type' => SupportMessageSenderType::Buyer,
                    'visibility' => SupportMessageVisibility::Public,
                    'payload_json' => ['source' => 'support_seeder'],
                ],
            );

            SupportMessage::updateOrCreate(
                [
                    'support_ticket_id' => $ticket->id,
                    'sender_id' => $admin->id,
                    'message' => $ticketData['message'],
                ],
                [
                    'sender_type' => SupportMessageSenderType::Agent,
                    'visibility' => SupportMessageVisibility::Public,
                    'payload_json' => ['source' => 'support_seeder'],
                ],
            );
        }
    }
}
