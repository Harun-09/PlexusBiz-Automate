<?php

namespace App\Domains\CRM\Services\Support;

class ChatbotService
{
    /**
     * Get a response from the mock AI Chatbot
     */
    public function getResponse(string $message, ?int $userId = null): string
    {
        // REAL API LOGIC
        /*
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful B2B marketplace assistant.'],
                ['role' => 'user', 'content' => $message],
            ],
        ]);
        return $response->choices[0]->message->content;
        */

        // MOCK LOGIC
        $message = strtolower($message);

        if (str_contains($message, 'order status')) {
            return "Please provide your order number and I will check the status for you.";
        }

        if (str_contains($message, 'refund')) {
            return "To request a refund, please navigate to your Orders page and select 'Dispute' for the relevant item.";
        }

        if (str_contains($message, 'hello') || str_contains($message, 'hi')) {
            return "Hello! Welcome to PlexusBiz. How can I help you with your B2B purchasing today?";
        }

        return "Thank you for your message. An agent will review your inquiry shortly. In the meantime, you can explore our catalog.";
    }
}
