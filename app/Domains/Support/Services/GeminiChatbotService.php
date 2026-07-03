<?php

namespace App\Domains\Support\Services;

use App\Domains\Support\Enums\SupportFaqStatus;
use App\Domains\Support\Models\SupportFaq;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatbotService
{
    public function generateResponse(string $message): ?array
    {
        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            Log::warning('Gemini API key is missing.');
            return null;
        }

        $faqs = SupportFaq::query()
            ->where('status', SupportFaqStatus::Active->value)
            ->orderBy('priority')
            ->get();

        $faqContext = $faqs->map(fn($faq) => "Q: {$faq->question}\nA: {$faq->answer}")->join("\n\n");

        $systemInstruction = "You are a helpful customer support chatbot for PlexusBiz Automate.
Use the following FAQ knowledge base to answer the user's question.
If the user's question cannot be answered using the FAQs, or if they explicitly ask to talk to a human or create a ticket, respond exactly with the phrase: 'ACTION_CREATE_TICKET'.
Otherwise, provide a friendly and concise answer based ONLY on the FAQs.

FAQ Knowledge Base:
" . $faqContext;

        try {
            $response = Http::timeout(15)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey, [
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $message]]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $text = trim($text);

                if (empty($text)) {
                    return null;
                }

                if (str_contains($text, 'ACTION_CREATE_TICKET')) {
                    return ['action' => 'create_ticket'];
                }

                return [
                    'action' => 'answer',
                    'answer' => $text,
                ];
            }

            Log::error('Gemini API Error', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Gemini API Exception', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
