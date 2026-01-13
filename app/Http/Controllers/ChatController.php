<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Store a new chat message
     */
    public function store(Request $request)
    {
        $isAuthenticated = auth()->check() || auth('company')->check();

        // Different validation rules based on authentication status
        $rules = [
            'message' => 'required|string|max:1000',
        ];

        // Guest users must provide name and email
        if (!$isAuthenticated) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|max:255';
        }

        $request->validate($rules);

        try {
            // Get name and email for the message
            $name = $request->name;
            $email = $request->email;

            if (auth()->check()) {
                $name = $name ?: auth()->user()->name;
                $email = $email ?: auth()->user()->email;
            } elseif (auth('company')->check()) {
                $name = $name ?: auth('company')->user()->name;
                $email = $email ?: auth('company')->user()->email;
            }

            $chatMessage = ChatMessage::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'company_id' => auth('company')->check() ? auth('company')->id() : null,
                'session_id' => !$isAuthenticated ? session()->getId() : null,
                'name' => $name,
                'email' => $email,
                'message' => $request->message,
            ]);

            // Send email notification to admin (wrapped in try-catch to prevent failure)
            try {
                ChatMessage::notifyAdmin($chatMessage);
            } catch (\Exception $e) {
                \Log::warning('Failed to send admin notification for chat: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Message sent! We will respond shortly.',
                'chat' => $chatMessage->load('user', 'company'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Chat message failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.',
            ], 500);
        }
    }

    /**
     * Get user's chat history
     */
    public function history(Request $request)
    {
        $query = ChatMessage::with(['repliedByAdmin']);

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } elseif (auth('company')->check()) {
            $query->where('company_id', auth('company')->id());
        } else {
            // Guest users - use session ID
            $query->where('session_id', session()->getId());
        }

        $messages = $query->latest()->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * Mark message as read by user
     */
    public function markRead($id)
    {
        $chatMessage = ChatMessage::findOrFail($id);

        // Verify ownership
        if (auth()->check() && $chatMessage->user_id !== auth()->id()) {
            abort(403);
        } elseif (auth('company')->check() && $chatMessage->company_id !== auth('company')->id()) {
            abort(403);
        } elseif (!auth()->check() && !auth('company')->check() && $chatMessage->session_id !== session()->getId()) {
            abort(403);
        }

        $chatMessage->markAsRead();

        return response()->json([
            'success' => true,
        ]);
    }
}
