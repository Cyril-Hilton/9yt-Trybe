<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = ContactMessage::query()->orderBy('created_at', 'desc');

        if ($status === 'unread') {
            $query->where('is_read', false);
        } elseif ($status === 'read') {
            $query->where('is_read', true);
        }

        $messages = $query->paginate(20);

        return view('admin.contact.index', compact('messages', 'status'));
    }

    public function show(ContactMessage $message)
    {
        // Mark as read when viewed
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('admin.contact.show', compact('message'));
    }

    public function markAsRead(ContactMessage $message)
    {
        $message->update(['is_read' => true]);

        return back()->with('success', 'Message marked as read.');
    }

    public function markAsUnread(ContactMessage $message)
    {
        $message->update(['is_read' => false]);

        return back()->with('success', 'Message marked as unread.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.contact.index')
            ->with('success', 'Message deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'message_ids' => ['required', 'array'],
            'message_ids.*' => ['exists:contact_messages,id'],
        ]);

        ContactMessage::whereIn('id', $request->message_ids)->delete();

        return back()->with('success', count($request->message_ids) . ' messages deleted successfully.');
    }

    public function bulkMarkAsRead(Request $request)
    {
        $request->validate([
            'message_ids' => ['required', 'array'],
            'message_ids.*' => ['exists:contact_messages,id'],
        ]);

        ContactMessage::whereIn('id', $request->message_ids)->update(['is_read' => true]);

        return back()->with('success', count($request->message_ids) . ' messages marked as read.');
    }
}
