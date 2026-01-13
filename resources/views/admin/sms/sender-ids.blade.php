@extends('layouts.admin')

@section('title', 'Sender ID Requests')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Sender ID Requests
                    </h1>
                    <p class="mt-2 text-gray-600">Review and approve sender ID requests from companies</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Total Requests</p>
                        <p class="text-3xl font-black text-gray-900">{{ $senderIds->total() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border-2 border-yellow-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Pending Review</p>
                        <p class="text-3xl font-black text-yellow-600">{{ $pendingCount }}</p>
                    </div>
                    <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border-2 border-green-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Approved</p>
                        <p class="text-3xl font-black text-green-600">{{ $approvedCount }}</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border-2 border-red-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Rejected</p>
                        <p class="text-3xl font-black text-red-600">{{ $rejectedCount }}</p>
                    </div>
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden mb-8">
            <div class="flex border-b border-gray-200">
                <a href="{{ route('admin.sms.sender-ids') }}" class="px-6 py-4 text-sm font-bold {{ !request('status') ? 'text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }} transition-all duration-200">
                    All Requests
                </a>
                <a href="{{ route('admin.sms.sender-ids', ['status' => 'pending']) }}" class="px-6 py-4 text-sm font-bold {{ request('status') === 'pending' ? 'text-yellow-600 border-b-2 border-yellow-600 bg-yellow-50' : 'text-gray-600 hover:text-yellow-600 hover:bg-gray-50' }} transition-all duration-200">
                    Pending ({{ $pendingCount }})
                </a>
                <a href="{{ route('admin.sms.sender-ids', ['status' => 'approved']) }}" class="px-6 py-4 text-sm font-bold {{ request('status') === 'approved' ? 'text-green-600 border-b-2 border-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-gray-50' }} transition-all duration-200">
                    Approved ({{ $approvedCount }})
                </a>
                <a href="{{ route('admin.sms.sender-ids', ['status' => 'rejected']) }}" class="px-6 py-4 text-sm font-bold {{ request('status') === 'rejected' ? 'text-red-600 border-b-2 border-red-600 bg-red-50' : 'text-gray-600 hover:text-red-600 hover:bg-gray-50' }} transition-all duration-200">
                    Rejected ({{ $rejectedCount }})
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-800 font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($senderIds->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-200 p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No Sender IDs Found</h3>
                <p class="text-gray-600">
                    @if(request('status'))
                        No {{ request('status') }} sender ID requests at this time.
                    @else
                        No sender ID requests have been submitted yet.
                    @endif
                </p>
            </div>
        @else
            <!-- Sender IDs List -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Sender ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Company</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Purpose</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Requested</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($senderIds as $senderId)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-lg font-black text-gray-900 font-mono">{{ $senderId->sender_id }}</p>
                                                @if($senderId->is_default)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                        Default
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($senderId->owner)
                                            @if($senderId->owner_type === 'App\\Models\\Company')
                                                <p class="text-sm font-bold text-gray-900">{{ $senderId->owner->company_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $senderId->owner->company_email }}</p>
                                            @else
                                                <p class="text-sm font-bold text-gray-900">{{ $senderId->owner->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $senderId->owner->email }}</p>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-500">No owner</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-700 max-w-xs">{{ Str::limit($senderId->purpose, 100) }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($senderId->status === 'approved')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Approved
                                            </span>
                                        @elseif($senderId->status === 'pending')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Pending
                                            </span>
                                        @elseif($senderId->status === 'rejected')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Rejected
                                            </span>
                                        @endif

                                        @if($senderId->status === 'approved' && $senderId->approved_at)
                                            <p class="text-xs text-gray-500 mt-1">{{ $senderId->approved_at->diffForHumans() }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-700">{{ $senderId->created_at->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $senderId->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($senderId->status === 'pending')
                                            <div class="flex items-center space-x-2">
                                                <!-- Approve Button -->
                                                <form method="POST" action="{{ route('admin.sms.sender-ids.approve', $senderId) }}">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition-all duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>

                                                <!-- Reject Button with Modal -->
                                                <button onclick="showRejectModal({{ $senderId->id }}, '{{ $senderId->sender_id }}')" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all duration-200">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Reject
                                                </button>
                                            </div>
                                        @elseif($senderId->status === 'approved')
                                            <span class="text-xs text-gray-500">No actions available</span>
                                        @elseif($senderId->status === 'rejected')
                                            @if($senderId->rejection_reason)
                                                <button onclick="showRejectionReason({{ json_encode($senderId->rejection_reason) }})" class="text-xs text-blue-600 hover:text-blue-800 font-semibold">
                                                    View Reason
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $senderIds->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-red-600 to-red-700 p-6 rounded-t-2xl">
            <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Reject Sender ID
            </h3>
        </div>
        <form id="rejectForm" method="POST" class="p-6">
            @csrf

            <p class="text-gray-700 mb-4">You are about to reject the sender ID: <strong id="rejectSenderId" class="font-mono text-lg"></strong></p>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Rejection Reason *
                </label>
                <textarea name="rejection_reason"
                          rows="4"
                          placeholder="Explain why this sender ID is being rejected..."
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent"
                          required></textarea>
                <p class="mt-1 text-xs text-gray-500">This will be visible to the company</p>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <button type="button" onclick="hideRejectModal()" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all duration-200">
                    Reject Sender ID
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div id="reasonModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-red-600 to-red-700 p-6 rounded-t-2xl">
            <h3 class="text-xl font-bold text-white">Rejection Reason</h3>
        </div>
        <div class="p-6">
            <p id="reasonText" class="text-gray-700"></p>
            <div class="flex items-center justify-end mt-6">
                <button type="button" onclick="hideReasonModal()" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectModal(id, senderId) {
    document.getElementById('rejectForm').action = '/admin/sms/sender-ids/' + id + '/reject';
    document.getElementById('rejectSenderId').textContent = senderId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectForm').reset();
}

function showRejectionReason(reason) {
    document.getElementById('reasonText').textContent = reason;
    document.getElementById('reasonModal').classList.remove('hidden');
}

function hideReasonModal() {
    document.getElementById('reasonModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) hideRejectModal();
});

document.getElementById('reasonModal').addEventListener('click', function(e) {
    if (e.target === this) hideReasonModal();
});
</script>
@endsection
