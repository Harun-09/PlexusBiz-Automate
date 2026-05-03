<?php

namespace App\Http\Controllers\Api;

use App\Domains\Support\Services\SupportChatbotService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\ChatbotMessageRequest;
use Illuminate\Http\JsonResponse;

class SupportChatbotController extends Controller
{
    public function __invoke(ChatbotMessageRequest $request, SupportChatbotService $chatbot): JsonResponse
    {
        return response()->json([
            'data' => $chatbot->respond($request->user(), $request->validated()),
        ]);
    }
}
