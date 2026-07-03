<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'is_helpful' => ['required', 'boolean'],
            'context' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'context' => $validated['context'] ?? null,
            'is_helpful' => $validated['is_helpful'],
            'message' => $validated['message'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }
}
