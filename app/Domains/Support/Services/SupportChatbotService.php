<?php

namespace App\Domains\Support\Services;

use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Str;

class SupportChatbotService
{
    public function __construct(
        private readonly FaqMatcher $faqs,
        private readonly SupportTicketService $tickets,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function respond(User $user, array $payload): array
    {
        $message = trim((string) $payload['message']);
        $match = $this->faqs->match($message);
        $ticket = null;

        if (($payload['create_ticket'] ?? false) || $match === null) {
            $ticket = $this->tickets->createTicket($user, [
                'subject' => $payload['subject'] ?? Str::limit($message, 120),
                'description' => $message,
                'supplier_id' => $payload['supplier_id'] ?? null,
                'order_id' => $payload['order_id'] ?? null,
                'metadata' => [
                    'source' => 'chatbot',
                    'matched_faq_id' => $match?->faq->id,
                ],
            ], SupportChannel::Chatbot);
        }

        return [
            'answer' => $match?->faq->answer ?? 'A support ticket has been created and the team will follow up.',
            'confidence' => $match?->confidence ?? 0.0,
            'source' => $match ? 'faq' : 'ticket',
            'matched_keywords' => $match?->matchedKeywords ?? [],
            'ticket' => $ticket instanceof SupportTicket ? [
                'id' => $ticket->id,
                'number' => $ticket->ticket_number,
                'status' => $ticket->status->value,
            ] : null,
        ];
    }
}
