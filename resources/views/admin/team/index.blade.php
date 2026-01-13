@extends('layouts.admin')

@section('title', 'Team Application Management')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Team Application Management</h1>
                <p class="text-gray-400">Review and approve team member applications</p>
            </div>
            <a href="{{ route('admin.team.create') }}"
               class="inline-flex items-center justify-center px-5 py-3 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Team Member
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @php
            $total = \App\Models\TeamMember::count();
            $pending = \App\Models\TeamMember::where('status', 'pending')->count();
            $approved = \App\Models\TeamMember::where('status', 'approved')->count();
            $rejected = \App\Models\TeamMember::where('status', 'rejected')->count();
        @endphp
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Total Applications</p>
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
        <form action="{{ route('admin.team.index') }}" method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <select name="role" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="">All Roles</option>
                <option value="Volunteer" {{ request('role') === 'Volunteer' ? 'selected' : '' }}>Volunteer</option>
                <option value="Staff" {{ request('role') === 'Staff' ? 'selected' : '' }}>Staff</option>
                <option value="Intern" {{ request('role') === 'Intern' ? 'selected' : '' }}>Intern</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>

            @if(request()->hasAny(['status', 'role']) && (request('status') !== 'all' || request('role')))
            <a href="{{ route('admin.team.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Applications Table -->
    <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Applicant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Submitted</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($members as $member)
                <tr class="hover:bg-gray-750 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 mr-3 flex items-center justify-center text-white font-bold">
                                {{ substr($member->full_name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-white">{{ $member->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $member->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-white">{{ Str::limit($member->title, 30) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($member->role === 'Staff') bg-purple-900 text-purple-200
                            @elseif($member->role === 'Volunteer') bg-blue-900 text-blue-200
                            @elseif($member->role === 'Intern') bg-teal-900 text-teal-200
                            @endif">
                            {{ $member->role }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-300">{{ $member->contact_number }}</div>
                        @if($member->socials)
                        <div class="text-xs text-gray-400">{{ $member->socials }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($member->status === 'approved') bg-green-900 text-green-200
                            @elseif($member->status === 'pending') bg-yellow-900 text-yellow-200
                            @elseif($member->status === 'rejected') bg-red-900 text-red-200
                            @endif">
                            {{ ucfirst($member->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                        {{ $member->created_at->format('M j, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-3">
                            <button onclick='viewDetails({{ json_encode([
                                "id" => $member->id,
                                "full_name" => $member->full_name,
                                "title" => $member->title,
                                "role" => $member->role,
                                "job_description" => $member->job_description,
                                "portfolio_link" => $member->portfolio_link ?? "",
                                "contact_number" => $member->contact_number,
                                "socials" => $member->socials ?? "",
                                "email" => $member->email
                            ]) }})'
                                    class="text-indigo-400 hover:text-indigo-300">View</button>

                            @if($member->status === 'pending')
                            <button onclick="approveMember({{ $member->id }})"
                                    class="text-green-400 hover:text-green-300">Approve</button>

                            <button onclick="openRejectModal({{ $member->id }}, {{ json_encode($member->full_name) }})"
                                    class="text-yellow-400 hover:text-yellow-300">Reject</button>
                            @endif

                            @if($member->status === 'rejected' && $member->rejection_reason)
                            <button onclick="showRejectionReason({{ json_encode($member->rejection_reason) }})"
                                    class="text-gray-400 hover:text-gray-300">View Reason</button>
                            @endif

                            <button onclick="deleteMember({{ $member->id }}, {{ json_encode($member->full_name) }})"
                                    class="text-red-400 hover:text-red-300">Delete</button>
                        </div>

                        <!-- Hidden forms -->
                        <form id="approve-form-{{ $member->id }}" action="{{ route('admin.team.approve', $member) }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                        <form id="delete-form-{{ $member->id }}" action="{{ route('admin.team.destroy', $member) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-gray-400">No applications found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($members->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $members->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-gray-800 rounded-xl p-6 max-w-2xl w-full mx-4 border border-gray-700 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-white mb-4">Application Details</h3>
        <div class="space-y-4">
            <div>
                <label class="text-sm font-semibold text-gray-400">Full Name</label>
                <p class="text-white" id="detailName"></p>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-400">Title</label>
                <p class="text-white" id="detailTitle"></p>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-400">Role</label>
                <p class="text-white" id="detailRole"></p>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-400">Job Description</label>
                <p class="text-white whitespace-pre-wrap" id="detailDescription"></p>
            </div>
            <div id="portfolioSection">
                <label class="text-sm font-semibold text-gray-400">Portfolio Link</label>
                <a id="detailPortfolio" href="#" target="_blank" class="text-indigo-400 hover:text-indigo-300 block"></a>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-400">Contact Number</label>
                <p class="text-white" id="detailContact"></p>
            </div>
            <div id="socialsSection">
                <label class="text-sm font-semibold text-gray-400">Socials</label>
                <p class="text-white" id="detailSocials"></p>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-400">Email</label>
                <p class="text-white" id="detailEmail"></p>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <button onclick="closeDetailsModal()"
                    class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4">Reject Application</h3>
        <p class="text-gray-400 mb-4">Are you sure you want to reject <span id="memberName" class="font-semibold text-white"></span>'s application?</p>

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
                    Reject Application
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
function viewDetails(member) {
    document.getElementById('detailName').textContent = member.full_name;
    document.getElementById('detailTitle').textContent = member.title;
    document.getElementById('detailRole').textContent = member.role;
    document.getElementById('detailDescription').textContent = member.job_description;
    document.getElementById('detailContact').textContent = member.contact_number;
    document.getElementById('detailEmail').textContent = member.email;

    if (member.portfolio_link) {
        document.getElementById('detailPortfolio').href = member.portfolio_link;
        document.getElementById('detailPortfolio').textContent = member.portfolio_link;
        document.getElementById('portfolioSection').style.display = 'block';
    } else {
        document.getElementById('portfolioSection').style.display = 'none';
    }

    if (member.socials) {
        document.getElementById('detailSocials').textContent = member.socials;
        document.getElementById('socialsSection').style.display = 'block';
    } else {
        document.getElementById('socialsSection').style.display = 'none';
    }

    document.getElementById('detailsModal').style.display = 'flex';
}

function closeDetailsModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

function approveMember(id) {
    if (confirm('Are you sure you want to approve this application?')) {
        document.getElementById('approve-form-' + id).submit();
    }
}

function openRejectModal(id, name) {
    document.getElementById('memberName').textContent = name;
    document.getElementById('rejectForm').action = '/admin/team/' + id + '/reject';
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

function deleteMember(id, name) {
    if (confirm('Are you sure you want to delete ' + name + '\'s application? This action cannot be undone.')) {
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
document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailsModal();
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

document.getElementById('reasonModal').addEventListener('click', function(e) {
    if (e.target === this) closeReasonModal();
});
</script>
@endsection
