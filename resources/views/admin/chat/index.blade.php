@extends('layouts.admin')

@section('title', 'Chat Messages')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Chat Messages</h1>
                <p class="text-gray-400 text-sm sm:text-base">View and respond to customer chat messages</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-600 bg-opacity-20 border border-green-600 rounded-lg">
        <p class="text-green-400 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Total Messages</p>
            <p class="text-3xl font-bold text-white">{{ $messages->total() }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6">
            <p class="text-blue-100 text-sm mb-1">Unread</p>
            <p class="text-3xl font-bold text-white">{{ $unreadCount }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-600 to-amber-700 rounded-xl p-6">
            <p class="text-amber-100 text-sm mb-1">Pending Reply</p>
            <p class="text-3xl font-bold text-white">{{ $pendingCount }}</p>
        </div>
    </div>

    <!-- Messages List -->
    <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700">
        @if($messages->count() > 0)
        <div class="divide-y divide-gray-700">
            @foreach($messages as $message)
            <div x-data="{ showReply: false, reply: '' }"
                 class="p-6 hover:bg-gray-750 transition {{ !$message->is_read ? 'border-l-4 border-blue-500 bg-blue-900/10' : '' }}">

                <!-- Message Header -->
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($message->sender_name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">{{ $message->sender_name }}</h3>
                                <p class="text-gray-400 text-sm">{{ $message->sender_email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($message->status === 'pending')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-amber-600/20 text-amber-400 border border-amber-600/30">
                            Pending
                        </span>
                        @elseif($message->status === 'replied')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-600/20 text-green-400 border border-green-600/30">
                            Replied
                        </span>
                        @elseif($message->status === 'closed')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-600/20 text-gray-400 border border-gray-600/30">
                            Closed
                        </span>
                        @endif

                        @if(!$message->is_read)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-600/20 text-blue-400 border border-blue-600/30">
                            New
                        </span>
                        @endif

                        <span class="text-gray-500 text-sm">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Message Content -->
                <div class="bg-gray-900 rounded-lg p-4 mb-4">
                    <p class="text-gray-300 whitespace-pre-wrap">{{ $message->message }}</p>
                </div>

                <!-- Admin Reply (if exists) -->
                @if($message->admin_reply)
                <div class="bg-gradient-to-br from-indigo-900/30 to-purple-900/30 rounded-lg p-4 mb-4 border border-indigo-500/30">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        <span class="text-indigo-400 text-sm font-medium">Admin Reply</span>
                        <span class="text-gray-500 text-xs">{{ $message->replied_at?->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-300 whitespace-pre-wrap">{{ $message->admin_reply }}</p>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-3">
                    @if($message->status === 'pending')
                    <button @click="showReply = !showReply"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Reply
                    </button>
                    @endif

                    @if(!$message->is_read)
                    <form action="{{ route('admin.chat.mark-read', $message->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-gray-700 text-white text-sm rounded-lg hover:bg-gray-600 transition">
                            Mark as Read
                        </button>
                    </form>
                    @endif

                    @if($message->status !== 'closed')
                    <form action="{{ route('admin.chat.close', $message->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-gray-700 text-white text-sm rounded-lg hover:bg-gray-600 transition">
                            Close
                        </button>
                    </form>
                    @endif
                </div>

                <!-- Reply Form -->
                <div x-show="showReply" x-transition class="mt-4">
                    <form action="{{ route('admin.chat.reply', $message->id) }}" method="POST"
                          @submit.prevent="
                              fetch($el.action, {
                                  method: 'POST',
                                  headers: {
                                      'Content-Type': 'application/json',
                                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                  },
                                  body: JSON.stringify({ reply: reply })
                              })
                              .then(r => r.json())
                              .then(data => {
                                  if(data.success) {
                                      window.location.reload();
                                  } else {
                                      alert('Failed to send reply');
                                  }
                              })
                          ">
                        <textarea x-model="reply"
                                  rows="3"
                                  class="w-full px-4 py-3 bg-gray-900 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none mb-3"
                                  placeholder="Type your reply..."></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showReply = false"
                                    class="px-4 py-2 bg-gray-700 text-white text-sm rounded-lg hover:bg-gray-600 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm rounded-lg hover:from-indigo-700 hover:to-purple-700 transition">
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-20 text-center">
            <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-400 mb-2">No chat messages yet</h3>
            <p class="text-gray-500">Customer chat messages will appear here</p>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($messages->hasPages())
    <div class="mt-8">
        {{ $messages->links() }}
    </div>
    @endif
</div>
@endsection
