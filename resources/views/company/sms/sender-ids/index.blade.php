@extends('layouts.company')

@section('title', 'Sender IDs')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Sender IDs
                    </h1>
                    <p class="mt-2 text-gray-600">Manage your SMS sender IDs for brand recognition</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('organization.sms.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('organization.sms.sender-ids.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Request Sender ID
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6 mb-8">
            <div class="flex items-start">
                <svg class="w-8 h-8 text-blue-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">What is a Sender ID?</h3>
                    <p class="text-sm text-blue-800 mb-3">
                        A Sender ID is the name that appears as the sender when your recipients receive an SMS.
                        Instead of showing a phone number, it displays your brand name (e.g., "MyCompany" or "ShopName").
                    </p>
                    <ul class="space-y-1 text-sm text-blue-800">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Maximum 15 characters (letters and numbers only)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Must be approved by admin before use</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Helps build trust and brand recognition with your audience</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sender IDs List -->
        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Your Sender IDs ({{ $senderIds->count() }})
                </h2>
            </div>

            <div class="p-6">
                @if($senderIds->count() > 0)
                    <div class="space-y-4">
                        @foreach($senderIds as $senderId)
                            <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-indigo-300 hover:shadow-lg transition-all duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <h3 class="text-2xl font-black text-gray-900">{{ $senderId->sender_id }}</h3>

                                            <!-- Status Badge -->
                                            @if($senderId->status === 'approved')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                    ✓ Approved
                                                </span>
                                            @elseif($senderId->status === 'pending')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                                    ⏳ Pending Review
                                                </span>
                                            @elseif($senderId->status === 'rejected')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                    ✗ Rejected
                                                </span>
                                            @endif

                                            <!-- Default Badge -->
                                            @if($senderId->is_default)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                        Default
                                                    </span>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="space-y-2">
                                            <div class="flex items-start text-sm">
                                                <span class="font-semibold text-gray-700 w-32">Purpose:</span>
                                                <span class="text-gray-600 flex-1">{{ $senderId->purpose }}</span>
                                            </div>

                                            <div class="flex items-start text-sm">
                                                <span class="font-semibold text-gray-700 w-32">Requested:</span>
                                                <span class="text-gray-600">{{ $senderId->created_at->format('M d, Y \a\t h:i A') }}</span>
                                            </div>

                                            @if($senderId->reviewed_at)
                                                <div class="flex items-start text-sm">
                                                    <span class="font-semibold text-gray-700 w-32">Reviewed:</span>
                                                    <span class="text-gray-600">{{ $senderId->reviewed_at->format('M d, Y \a\t h:i A') }}</span>
                                                </div>
                                            @endif

                                            @if($senderId->status === 'approved' && $senderId->reviewedByAdmin)
                                                <div class="flex items-start text-sm">
                                                    <span class="font-semibold text-gray-700 w-32">Approved by:</span>
                                                    <span class="text-gray-600">{{ $senderId->reviewedByAdmin->name ?? 'N/A' }}</span>
                                                </div>
                                            @endif

                                            @if($senderId->status === 'rejected')
                                                <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                                    <p class="text-sm font-semibold text-red-900 mb-1">Rejection Reason:</p>
                                                    <p class="text-sm text-red-700">{{ $senderId->rejection_reason }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="ml-4 flex flex-col space-y-2">
                                        <!-- Set as Default (only for approved, non-default IDs) -->
                                        @if($senderId->status === 'approved' && !$senderId->is_default)
                                            <form method="POST" action="{{ route('organization.sms.sender-ids.set-default', $senderId->id) }}">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                                    Set as Default
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Edit (only for pending or rejected) -->
                                        @if(in_array($senderId->status, ['pending', 'rejected']))
                                            <a href="{{ route('organization.sms.sender-ids.edit', $senderId->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-colors duration-200 text-center">
                                                Edit
                                            </a>
                                        @endif

                                        <!-- Delete (can delete any sender ID except default) -->
                                        @if(!$senderId->is_default)
                                            <form method="POST" action="{{ route('organization.sms.sender-ids.destroy', $senderId->id) }}" onsubmit="return confirm('Are you sure you want to delete this sender ID{{ $senderId->status === 'approved' ? '? This action cannot be undone' : ' request' }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-lg hover:bg-red-700 transition-colors duration-200">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-8">
                        {{ $senderIds->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <p class="text-gray-600 font-semibold mb-2">No sender IDs yet</p>
                        <p class="text-sm text-gray-500 mb-4">Request your first sender ID to enhance your brand identity</p>
                        <a href="{{ route('organization.sms.sender-ids.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Request Sender ID
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
