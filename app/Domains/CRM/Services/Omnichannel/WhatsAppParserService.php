<?php

namespace App\Domains\CRM\Services\Omnichannel;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class WhatsAppParserService
{
    /**
     * Parse incoming WhatsApp webhook (Mocked logic for Twilio/Meta API)
     */
    public function parseIncomingMessage(array $payload): void
    {
        // REAL API Logic would authenticate signature and parse Twilio payload
        /*
        $from = $payload['From'] ?? '';
        $body = $payload['Body'] ?? '';
        */
        
        // MOCKED LOGIC
        $from = $payload['From'] ?? 'whatsapp:+8801700000000';
        $body = strtolower($payload['Body'] ?? 'order 10 units of product x');

        Log::info("Received WhatsApp message from {$from}: {$body}");

        if (str_contains($body, 'order')) {
            $this->handleConversationalOrder($from, $body);
        } else {
            $this->sendReply($from, "Hello! To place an order, type 'Order [quantity] of [product]'.");
        }
    }

    protected function handleConversationalOrder(string $from, string $body): void
    {
        // Advanced NLP could be here (e.g. Gemini/Dialogflow)
        // Mock order creation based on text
        Log::info("Mock: Creating order for {$from} based on text: {$body}");
        $this->sendReply($from, "We have received your order request. Our agent will confirm it shortly.");
    }

    protected function sendReply(string $to, string $message): void
    {
        // REAL API LOGIC
        /*
        $twilio = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));
        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'body' => $message
        ]);
        */

        // MOCKED LOGIC
        Log::info("Mock: Sent WhatsApp reply to {$to}: {$message}");
    }
}
