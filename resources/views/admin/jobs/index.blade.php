@extends('layouts.admin')

@section('title', 'Job Portfolio Management')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Job Portfolio Management</h1>
                <p class="text-gray-400">Review and approve job portfolios from applicants</p>
            </div>
            <a href="{{ route('admin.jobs.create') }}"
               class="inline-flex items-center justify-center px-5 py-3 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Portfolio
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @php
            $total = \App\Models\JobPortfolio::count();
            $pending = \App\Models\JobPortfolio::where('status', 'pending')->count();
            $approved = \App\Models\JobPortfolio::where('status', 'approved')->count();
            $rejected = \App\Models\JobPortfolio::where('status', 'rejected')->count();
        @endphp
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Total Portfolios</p>
            <p class="text-3xl font-bold text-white">{{ $total }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-xl p-6">
            <p class="text-yellow-100 text-sm mb-1">Pending Approval</p>
            <p class="text-3xl font-bold text-white">{{ $pending }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6">
            <p class="text-green-100 text-sm mb-1">Approved</p>
            <p class="text-3xl font-bold text-white">{{ $approved }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-6">
            <p class="text-red-100 text-sm mb-1">Rejected</p>
            <p class="text-3xl font-bold text-white">{{ $rejected }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-xl p-4 mb-6 border border-gray-700">
        <form action="{{ route('admin.jobs.index') }}" method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>

            @if(request()->has('status') && request('status') !== 'all')
            <a href="{{ route('admin.jobs.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Portfolios Table -->
    <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Applicant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Job Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Portfolio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Submitted</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($portfolios as $portfolio)
                <tr class="hover:bg-gray-750 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($portfolio->profile_picture)
                            <img src="{{ asset('storage/' . $portfolio->profile_picture) }}" alt="{{ $portfolio->first_name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                            @else
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 mr-3 flex items-center justify-center text-white font-bold">
                                {{ substr($portfolio->first_name, 0, 1) }}{{ substr($portfolio->last_name, 0, 1) }}
                            </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-white">{{ $portfolio->first_name }} {{ $portfolio->middle_name }} {{ $portfolio->last_name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-white">{{ Str::limit($portfolio->title, 30) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-900 text-indigo-200">
                            {{ $portfolio->job_type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ $portfolio->portfolio_link }}" target="_blank"
                           class="text-indigo-400 hover:text-indigo-300 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            View Portfolio
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($portfolio->status === 'approved') bg-green-900 text-green-200
                            @elseif($portfolio->status === 'pending') bg-yellow-900 text-yellow-200
                            @elseif($portfolio->status === 'rejected') bg-red-900 text-red-200
                            @endif">
                            {{ ucfirst($portfolio->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                        {{ $portfolio->created_at->format('M j, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-3">
                            @if($portfolio->status === 'pending')
                            <button onclick="approvePortfolio({{ $portfolio->id }})"
                                    class="text-green-400 hover:text-green-300">Approve</button>

                            <button onclick="openRejectModal({{ $portfolio->id }}, {{ json_encode($portfolio->first_name . ' ' . $portfolio->last_name) }})"
                                    class="text-yellow-400 hover:text-yellow-300">Reject</button>
                            @endif

                            @if($portfolio->status === 'rejected' && $portfolio->rejection_reason)
                            <button onclick="showRejectionReason({{ json_encode($portfolio->rejection_reason) }})"
                                    class="text-gray-400 hover:text-gray-300">View Reason</button>
                            @endif

                            <button onclick="deletePortfolio({{ $portfolio->id }}, {{ json_encode($portfolio->first_name . ' ' . $portfolio->last_name) }})"
                                    class="text-red-400 hover:text-red-300">Delete</button>
                        </div>

                        <!-- Hidden forms -->
                        <form id="approve-form-{{ $portfolio->id }}" action="{{ route('admin.jobs.approve', $portfolio) }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                        <form id="delete-form-{{ $portfolio->id }}" action="{{ route('admin.jobs.destroy', $portfolio) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-400">No portfolios found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($portfolios->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $portfolios->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4">Reject Portfolio</h3>
        <p class="text-gray-400 mb-4">Are you sure you want to reject <span id="portfolioName" class="font-semibold text-white"></span>'s portfolio?</p>

        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Rejection Reason *</label>
                <textarea name="rejection_reason" required rows="3"
                          class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                          placeholder="Please provide a reason for rejection..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Reject Portfolio
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div id="reasonModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4">Rejection Reason</h3>
        <p class="text-gray-300 mb-4" id="rejectionReasonText"></p>
        <div class="flex justify-end">
            <button onclick="closeReasonModal()"
                    class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function approvePortfolio(id) {
    if (confirm('Are you sure you want to approve this portfolio?')) {
        document.getElementById('approve-form-' + id).submit();
    }
}

function openRejectModal(id, name) {
    document.getElementById('portfolioName').textContent = name;
    document.getElementById('rejectForm').action = '/admin/jobs/' + id + '/reject';
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

function deletePortfolio(id, name) {
    if (confirm('Are you sure you want to delete ' + name + '\'s portfolio? This action cannot be undone.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}

function showRejectionReason(reason) {
    document.getElementById('rejectionReasonText').textContent = reason;
    document.getElementById('reasonModal').style.display = 'flex';
}

function closeReasonModal() {
    document.getElementById('reasonModal').style.display = 'none';
}

// Close modals when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

document.getElementById('reasonModal').addEventListener('click', function(e) {
    if (e.target === this) closeReasonModal();
});
</script>
@endsection
