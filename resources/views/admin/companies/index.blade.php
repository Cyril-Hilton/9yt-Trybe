@extends('layouts.admin')

@section('title', 'Manage Companies')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Company Management</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Manage all registered companies and their access</p>
            </div>
            <a href="{{ route('admin.companies.create') }}"
               class="inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105 whitespace-nowrap">
                <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Add New Company</span>
                <span class="sm:hidden">Add Company</span>
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border-2 border-indigo-100 dark:border-gray-700 p-6 mb-6 glass-card">
        <form method="GET" class="flex flex-wrap gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[250px]">
                <label class="block text-xs font-bold text-gray-700 mb-2">Search Companies</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by name or email..."
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 shadow-sm">
            </div>

            <!-- Status Filter -->
            <div class="min-w-[180px]">
                <label class="block text-xs font-bold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 shadow-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2 sm:gap-3 w-full sm:w-auto">
                <button type="submit"
                        class="flex-1 sm:flex-initial px-4 sm:px-6 py-2 sm:py-3 rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 font-bold text-sm sm:text-base">
                    Filter
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('admin.companies.index') }}"
                       class="flex-1 sm:flex-initial px-4 sm:px-6 py-2 sm:py-3 rounded-xl shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium text-sm sm:text-base text-center">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Companies Table -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden glass-card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 responsive-table">
                <thead class="bg-gradient-to-r from-indigo-600 to-purple-600">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Company</th>
                        <th class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Contact</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                        <th class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Stats</th>
                        <th class="hidden xl:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Joined</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-4 text-right text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($companies as $company)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap" data-label="Company">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 sm:h-12 sm:w-12">
                                        <div class="h-8 w-8 sm:h-12 sm:w-12 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold text-sm sm:text-lg">
                                            {{ substr($company->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-2 sm:ml-4">
                                        <div class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">{{ $company->name }}</div>
                                        @if($company->website)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">{{ $company->website }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap" data-label="Contact">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $company->email }}</div>
                                @if($company->phone)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $company->phone }}</div>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap" data-label="Status">
                                @if($company->is_suspended)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border-2 border-red-200">
                                        Suspended
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border-2 border-green-200">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" data-label="Stats">
                                <div class="flex items-center space-x-3">
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $company->conferences_count }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        {{ $company->surveys_count }}
                                    </span>
                                </div>
                            </td>
                            <td class="hidden xl:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" data-label="Joined">
                                {{ $company->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-right text-sm font-medium" data-label="Actions">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.companies.show', $company) }}"
                                       class="text-indigo-600 hover:text-indigo-900"
                                       title="View">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.companies.edit', $company) }}"
                                       class="text-blue-600 hover:text-blue-900"
                                       title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    @if($company->is_suspended)
                                        <form method="POST" action="{{ route('admin.companies.unsuspend', $company) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-green-600 hover:text-green-900"
                                                    title="Unsuspend"
                                                    onclick="return confirm('Are you sure you want to unsuspend this company?')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <button onclick="showSuspendModal({{ $company->id }}, '{{ $company->name }}')"
                                                class="text-orange-600 hover:text-orange-900"
                                                title="Suspend">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        </button>
                                    @endif

                                    <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this company? This action cannot be undone.')">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <p class="mt-4 font-semibold">No companies found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($companies->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full mx-4 border-4 border-red-200 dark:border-red-700">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5 rounded-t-xl">
            <h3 class="text-2xl font-bold text-white">Suspend Company</h3>
        </div>
        <form id="suspendForm" method="POST">
            @csrf
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">You are about to suspend <strong id="companyName"></strong>.</p>
                <label class="block text-sm font-bold text-gray-700 mb-2">Reason (Optional)</label>
                <textarea name="reason"
                          rows="3"
                          class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200"
                          placeholder="Enter suspension reason..."></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button type="button"
                        onclick="hideSuspendModal()"
                        class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-medium hover:bg-gray-100">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-3 rounded-xl text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 font-bold">
                    Suspend Company
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showSuspendModal(companyId, companyName) {
    document.getElementById('suspendModal').classList.remove('hidden');
    document.getElementById('companyName').textContent = companyName;
    document.getElementById('suspendForm').action = `/admin/companies/${companyId}/suspend`;
}

function hideSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}
</script>
@endsection
