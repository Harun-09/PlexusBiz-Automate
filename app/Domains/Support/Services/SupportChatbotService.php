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
        private readonly GeminiChatbotService $gemini,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function respond(User $user, array $payload): array
    {
        $message = trim((string) $payload['message']);
        
        $ticket = null;
        $answer = null;
        $confidence = 0.0;
        $source = 'ticket';
        $matchedKeywords = [];
        $escalate = false;
        
        $shouldCreateTicket = $payload['create_ticket'] ?? false;

        $geminiResponse = $this->gemini->generateResponse($message);

        if ($geminiResponse && $geminiResponse['action'] === 'answer') {
            $answer = $geminiResponse['answer'];
            $confidence = 0.95;
            $source = 'faq';
        } elseif ($geminiResponse && $geminiResponse['action'] === 'create_ticket') {
            if ($shouldCreateTicket) {
                $source = 'ticket';
            } else {
                $escalate = true;
                $answer = "I'm sorry, I couldn't find an answer to your question in our system. How would you like to proceed?";
                $source = 'escalation';
            }
        } else {
            // Fallback to legacy FaqMatcher if Gemini fails or is not configured
            $match = $this->faqs->match($message);
            if ($match !== null) {
                $answer = $match->faq->answer;
                $confidence = $match->confidence;
                $source = 'faq';
                $matchedKeywords = $match->matchedKeywords;
            } else {
                if ($shouldCreateTicket) {
                    $source = 'ticket';
                } else {
                    $escalate = true;
                    $answer = "I'm sorry, I couldn't find an answer to your question in our system. How would you like to proceed?";
                    $source = 'escalation';
                }
            }
        }

        if ($shouldCreateTicket) {
            $ticket = $this->tickets->createTicket($user, [
                'subject' => $payload['subject'] ?? Str::limit($message, 120),
                'description' => $message,
                'supplier_id' => $payload['supplier_id'] ?? null,
                'order_id' => $payload['order_id'] ?? null,
                'metadata' => [
                    'source' => 'chatbot',
                    'gemini_fallback' => true,
                ],
            ], SupportChannel::Chatbot);
            
            $answer = $answer ?? 'A support ticket has been created and the team will follow up.';
        }

        return [
            'answer' => $answer,
            'confidence' => $confidence,
            'source' => $source,
            'matched_keywords' => $matchedKeywords,
            'ticket' => $ticket instanceof SupportTicket ? [
                'id' => $ticket->id,
                'number' => $ticket->ticket_number,
                'status' => $ticket->status->value,
            ] : null,
            'escalate' => $escalate,
        ];
    }
}
