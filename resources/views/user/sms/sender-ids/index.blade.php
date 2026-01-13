@extends('layouts.app')

@section('title', 'Sender IDs')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('user.sms.dashboard') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold mb-4 inline-block">
                    ← Back to Dashboard
                </a>
                <h1 class="text-4xl font-bold gradient-text mb-2">🔖 Sender IDs</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage your SMS sender identities</p>
            </div>
            <a href="{{ route('user.sms.sender-ids.create') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition">
                + Request New Sender ID
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg">
                <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg">
                <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Info Card -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-6 rounded-lg mb-8">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-2">What is a Sender ID?</h4>
                    <p class="text-sm text-blue-700 dark:text-blue-400">A Sender ID is the name that appears as the sender when recipients receive your SMS. It can be up to 15 characters (letters, numbers, spaces, and special characters allowed). All Sender IDs must be approved by an admin before you can use them.</p>
                </div>
            </div>
        </div>

        @if($senderIds->count() > 0)
        <!-- Sender IDs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($senderIds as $senderId)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-2
                @if($senderId->status === 'approved' && $senderId->is_default) border-green-500
                @elseif($senderId->status === 'approved') border-gray-200 dark:border-gray-700
                @elseif($senderId->status === 'rejected') border-red-300 dark:border-red-700
                @else border-yellow-300 dark:border-yellow-700
                @endif">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-mono">{{ $senderId->sender_id }}</h3>
                            @if($senderId->is_default)
                                <span class="ml-3 px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-xs font-bold rounded-full">
                                    ⭐ DEFAULT
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $senderId->purpose }}</p>

                        <!-- Status Badge -->
                        <div class="flex items-center">
                            @if($senderId->status === 'approved')
                                <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-xs font-semibold rounded-full">
                                    ✓ Approved
                                </span>
                            @elseif($senderId->status === 'rejected')
                                <span class="px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 text-xs font-semibold rounded-full">
                                    ✗ Rejected
                                </span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 text-xs font-semibold rounded-full">
                                    ⏳ Pending Review
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl py-2 z-50 border border-gray-200 dark:border-gray-700">
                            @if(!$senderId->is_default)
                                <a href="{{ route('user.sms.sender-ids.edit', $senderId->id) }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                            @endif
                            @if($senderId->status === 'approved' && !$senderId->is_default)
                                <form method="POST" action="{{ route('user.sms.sender-ids.set-default', $senderId->id) }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                        Set as Default
                                    </button>
                                </form>
                            @endif
                            @if($senderId->status !== 'approved' || !$senderId->is_default)
                                <form method="POST" action="{{ route('user.sms.sender-ids.destroy', $senderId->id) }}" onsubmit="return confirm('Are you sure you want to delete this Sender ID?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                        <span>Created: {{ $senderId->created_at->format('M d, Y') }}</span>
                        @if($senderId->reviewed_by_admin_id && $senderId->reviewedByAdmin)
                            <span>Reviewed by: {{ $senderId->reviewedByAdmin->name }}</span>
                        @endif
                    </div>
                    @if($senderId->status === 'rejected' && $senderId->rejection_reason)
                        <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-xs font-semibold text-red-800 dark:text-red-300 mb-1">Rejection Reason:</p>
                            <p class="text-xs text-red-700 dark:text-red-400">{{ $senderId->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination Links -->
        <div class="mt-8">
            {{ $senderIds->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Sender IDs Yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Request your first Sender ID to start sending SMS messages</p>
            <a href="{{ route('user.sms.sender-ids.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition">
                Request Sender ID
            </a>
        </div>
        @endif
    </div>
</div>

<style>
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
[x-cloak] { display: none !important; }
</style>
@endsection
