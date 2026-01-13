@extends('layouts.admin')

@section('title', 'Contact Message - ' . $message->subject)

@section('content')
<div class="p-8 max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Contact Message</h1>
                <p class="text-gray-400">{{ $message->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <a href="{{ route('admin.contact.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                ← Back to Messages
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-600 bg-opacity-20 border border-green-600 rounded-lg">
        <p class="text-green-400 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Message Content -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 mb-6">
        <!-- Status Badge -->
        <div class="mb-6 flex items-center justify-between">
            @if(!$message->is_read)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-white">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                Unread
            </span>
            @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-600 text-white">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                Read
            </span>
            @endif

            <div class="flex gap-2">
                @if($message->is_read)
                <form action="{{ route('admin.contact.mark-unread', $message) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition text-sm">
                        Mark as Unread
                    </button>
                </form>
                @else
                <form action="{{ route('admin.contact.mark-read', $message) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                        Mark as Read
                    </button>
                </form>
                @endif

                <form action="{{ route('admin.contact.destroy', $message) }}" method="POST" onsubmit="return confirm('Delete this message? This action cannot be undone.');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Subject -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-white mb-2">{{ $message->subject }}</h2>
        </div>

        <!-- Sender Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-900 rounded-lg">
            <div>
                <p class="text-gray-400 text-sm mb-1">From</p>
                <p class="text-white font-semibold">{{ $message->name }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">Email</p>
                <a href="mailto:{{ $message->email }}" class="text-indigo-400 hover:text-indigo-300 font-semibold">
                    {{ $message->email }}
                </a>
            </div>
            @if($message->phone)
            <div>
                <p class="text-gray-400 text-sm mb-1">Phone</p>
                <a href="tel:{{ $message->phone }}" class="text-indigo-400 hover:text-indigo-300 font-semibold">
                    {{ $message->phone }}
                </a>
            </div>
            @endif
            <div>
                <p class="text-gray-400 text-sm mb-1">Received</p>
                <p class="text-white font-semibold">{{ $message->created_at->format('M d, Y h:i A') }}</p>
                <p class="text-gray-500 text-sm">{{ $message->created_at->diffForHumans() }}</p>
            </div>
        </div>

        <!-- Message Body -->
        <div>
            <p class="text-gray-400 text-sm mb-2">Message</p>
            <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                <p class="text-white whitespace-pre-wrap leading-relaxed">{{ $message->message }}</p>
            </div>
        </div>
    </div>

    <!-- Reply via Email -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-bold text-white mb-4">Reply to this message</h3>
        <p class="text-gray-400 mb-4">Click the button below to compose a reply in your default email client:</p>
        <a href="mailto:{{ $message->email }}?subject=Re: {{ urlencode($message->subject) }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Reply via Email
        </a>
    </div>
</div>
@endsection
