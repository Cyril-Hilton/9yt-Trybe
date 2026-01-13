@extends('layouts.admin')

@section('title', 'Company Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.companies.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Companies
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Company Info -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-16 w-16 rounded-full bg-white flex items-center justify-center text-indigo-600 font-bold text-2xl mr-4">
                                {{ substr($company->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">{{ $company->name }}</h3>
                                <p class="text-indigo-100 text-sm mt-1">Joined {{ $company->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @if($company->is_suspended)
                            <span class="px-4 py-2 bg-red-100 text-red-800 rounded-xl font-bold border-2 border-red-200">
                                Suspended
                            </span>
                        @else
                            <span class="px-4 py-2 bg-green-100 text-green-800 rounded-xl font-bold border-2 border-green-200">
                                Active
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-8 space-y-4">
                    <div>
                        <p class="text-sm font-bold text-gray-600">Email</p>
                        <p class="text-lg text-gray-900">{{ $company->email }}</p>
                    </div>

                    @if($company->phone)
                        <div>
                            <p class="text-sm font-bold text-gray-600">Phone</p>
                            <p class="text-lg text-gray-900">{{ $company->phone }}</p>
                        </div>
                    @endif

                    @if($company->website)
                        <div>
                            <p class="text-sm font-bold text-gray-600">Website</p>
                            <a href="{{ $company->website }}" target="_blank" class="text-lg text-indigo-600 hover:text-indigo-800">
                                {{ $company->website }}
                            </a>
                        </div>
                    @endif

                    @if($company->description)
                        <div>
                            <p class="text-sm font-bold text-gray-600">Description</p>
                            <p class="text-gray-900">{{ $company->description }}</p>
                        </div>
                    @endif

                    @if($company->is_suspended && $company->suspension_reason)
                        <div class="p-4 bg-red-50 rounded-xl border-2 border-red-200">
                            <p class="text-sm font-bold text-red-800 mb-1">Suspension Reason</p>
                            <p class="text-sm text-red-700">{{ $company->suspension_reason }}</p>
                            <p class="text-xs text-red-600 mt-2">Suspended on {{ $company->suspended_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>

                <div class="px-8 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                    <a href="{{ route('admin.companies.edit', $company) }}"
                       class="px-6 py-3 rounded-xl border-2 border-indigo-600 text-indigo-600 font-bold hover:bg-indigo-50">
                        Edit Company
                    </a>

                    @if($company->is_suspended)
                        <form method="POST" action="{{ route('admin.companies.unsuspend', $company) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-6 py-3 rounded-xl text-white bg-green-600 hover:bg-green-700 font-bold"
                                    onclick="return confirm('Unsuspend this company?')">
                                Unsuspend
                            </button>
                        </form>
                    @else
                        <button onclick="showSuspendModal({{ $company->id }}, '{{ $company->name }}')"
                                class="px-6 py-3 rounded-xl text-white bg-red-600 hover:bg-red-700 font-bold">
                            Suspend Company
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white">
                <p class="text-blue-100 text-sm font-medium">Conferences</p>
                <p class="text-4xl font-bold mt-2">{{ $stats['total_conferences'] }}</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <p class="text-purple-100 text-sm font-medium">Surveys</p>
                <p class="text-4xl font-bold mt-2">{{ $stats['total_surveys'] }}</p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 text-white">
                <p class="text-green-100 text-sm font-medium">Registrations</p>
                <p class="text-4xl font-bold mt-2">{{ $stats['total_registrations'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Suspend Modal (same as in index) -->
<div id="suspendModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 border-4 border-red-200">
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
