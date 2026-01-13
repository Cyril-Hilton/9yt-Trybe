@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Contact Messages</h1>
                <p class="text-gray-400 text-sm sm:text-base">View and manage contact form submissions</p>
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
        @php
            $total = \App\Models\ContactMessage::count();
            $unread = \App\Models\ContactMessage::unread()->count();
            $read = \App\Models\ContactMessage::where('is_read', true)->count();
        @endphp
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Total Messages</p>
            <p class="text-3xl font-bold text-white">{{ $total }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6">
            <p class="text-blue-100 text-sm mb-1">Unread</p>
            <p class="text-3xl font-bold text-white">{{ $unread }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6">
            <p class="text-green-100 text-sm mb-1">Read</p>
            <p class="text-3xl font-bold text-white">{{ $read }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-xl p-4 mb-6 border border-gray-700">
        <form action="{{ route('admin.contact.index') }}" method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Messages</option>
                <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>Unread Only</option>
                <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Read Only</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>

            @if(request()->has('status') && request('status') !== 'all')
            <a href="{{ route('admin.contact.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Messages Table -->
    <div class="bg-gray-800 rounded-xl overflow-x-auto border border-gray-700">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">From</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Message Preview</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Received</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($messages as $message)
                <tr class="hover:bg-gray-750 transition {{ !$message->is_read ? 'bg-blue-900 bg-opacity-10' : '' }}">
                    <td class="px-6 py-4">
                        @if(!$message->is_read)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-600 text-white">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            New
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-600 text-gray-300">
                            Read
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-white">{{ $message->name }}</div>
                        <div class="text-xs text-gray-400">{{ $message->email }}</div>
                        @if($message->phone)
                        <div class="text-xs text-gray-500">{{ $message->phone }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-white font-medium">{{ Str::limit($message->subject, 40) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-300">{{ Str::limit($message->message, 60) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-400">{{ $message->created_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $message->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.contact.show', $message) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 transition">
                                View
                            </a>
                            @if(!$message->is_read)
                            <form action="{{ route('admin.contact.mark-read', $message) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition">
                                    Mark Read
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.contact.destroy', $message) }}" method="POST" onsubmit="return confirm('Delete this message?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center">
                        <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-400 mb-2">No contact messages</h3>
                        <p class="text-gray-500">Contact messages will appear here when submitted</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($messages->hasPages())
    <div class="mt-8">
        {{ $messages->links() }}
    </div>
    @endif
</div>
@endsection
