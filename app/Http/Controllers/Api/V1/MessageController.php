<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Notifications\Models\Message;
use App\Domains\Notifications\Services\MessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        private readonly MessageService $messageService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->input('type', 'inbox');

        if ($type === 'sent') {
            $messages = $this->messageService->getSent($user->id, $request->input('per_page', 15));
        } else {
            $messages = $this->messageService->getInbox($user->id, $request->input('per_page', 15));
        }

        return response()->json([
            'data' => $messages->items(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    public function show(Request $request, Message $message): JsonResponse
    {
        $user = $request->user();

        // Check if user is sender or receiver
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Mark as read if receiver is viewing
        if ($message->receiver_id === $user->id && ! $message->read_at) {
            $message->markAsRead();
        }

        return response()->json([
            'data' => $message->load(['sender', 'receiver']),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $this->messageService->getUnreadCount($request->user()->id);

        return response()->json(['unread_count' => $count]);
    }

    public function markAsRead(Request $request, Message $message): JsonResponse
    {
        $user = $request->user();

        if ($message->receiver_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->markAsRead();

        return response()->json([
            'message' => 'Message marked as read',
            'data' => $message,
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $count = $this->messageService->markAllAsRead($request->user()->id);

        return response()->json([
            'message' => 'All notifications marked as read',
            'updated_count' => $count,
        ]);
    }

    public function recent(Request $request): JsonResponse
    {
        $messages = $this->messageService->getRecentForUser(
            $request->user()->id,
            $request->input('limit', 5)
        );

        return response()->json(['data' => $messages]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
        ]);

        $message = $this->messageService->sendToUser(
            receiver: \App\Models\User::find($validated['receiver_id']),
            subject: $validated['subject'] ?? 'No Subject',
            body: $validated['body'],
            sender: $request->user(),
        );

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message,
        ], 201);
    }
}
