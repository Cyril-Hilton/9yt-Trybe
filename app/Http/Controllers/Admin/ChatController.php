<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display admin chat dashboard
     */
    public function index()
    {
        $pendingCount = ChatMessage::pending()->count();
        $unreadCount = ChatMessage::unread()->count();

        $messages = ChatMessage::with(['user', 'company', 'repliedByAdmin'])
            ->latest()
            ->paginate(50);

        return view('admin.chat.index', compact('messages', 'pendingCount', 'unreadCount'));
    }

    /**
     * Get all chat messages (AJAX)
     */
    public function getMessages(Request $request)
    {
        $query = ChatMessage::with(['user', 'company', 'repliedByAdmin']);

        // Filter by status
        if ($request->status) {
            if ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'replied') {
                $query->replied();
            } elseif ($request->status === 'unread') {
                $query->unread();
            }
        }

        $messages = $query->latest()->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * Reply to a chat message
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:2000',
        ]);

        $chatMessage = ChatMessage::findOrFail($id);
        $chatMessage->reply(auth()->id(), $request->reply);

        $payload = [
            'success' => true,
            'message' => 'Reply sent successfully!',
            'chat' => $chatMessage->fresh(['user', 'company', 'repliedByAdmin']),
        ];

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json($payload);
        }

        return back()->with('success', $payload['message']);
    }

    /**
     * Mark message as read
     */
    public function markRead(Request $request, $id)
    {
        $chatMessage = ChatMessage::findOrFail($id);
        $chatMessage->markAsRead();

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
            ]);
        }

        return back()->with('success', 'Message marked as read.');
    }

    /**
     * Close a conversation
     */
    public function close(Request $request, $id)
    {
        $chatMessage = ChatMessage::findOrFail($id);
        $chatMessage->update([
            'status' => 'closed',
            'is_read' => true,
        ]);

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Conversation closed.',
            ]);
        }

        return back()->with('success', 'Conversation closed.');
    }

    /**
     * Get unread count (for notifications)
     */
    public function unreadCount()
    {
        $count = ChatMessage::unread()->count();

        return response()->json([
            'count' => $count,
        ]);
    }
}
