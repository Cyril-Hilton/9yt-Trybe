@extends('layouts.admin')

@section('title', 'Add SMS Credits')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div>
                <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Add SMS Credits
                </h1>
                <p class="mt-2 text-gray-600">Manually credit SMS units to a company's wallet</p>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6 mb-8">
            <div class="flex items-start">
                <svg class="w-8 h-8 text-blue-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Manual Credit Addition</h3>
                    <ul class="space-y-1 text-sm text-blue-800">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Use this to add promotional or complimentary credits</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>No payment is required for manually added credits</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>A transaction record will be created for tracking purposes</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>The company will be notified of the credit addition</span>
                        </li>
                    </ul>
                </div>
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

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Credit Details
                </h2>
            </div>

            <form method="POST" action="{{ route('admin.sms.add-credits.store') }}" class="p-6 space-y-6" x-data="addCreditsForm()">
                @csrf

                <!-- Company Selection -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Select Company *
                    </label>
                    <select name="company_id"
                            x-model="selectedCompanyId"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('company_id') border-red-500 @enderror"
                            required>
                        <option value="">-- Select a company --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }} ({{ $company->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Balance Display -->
                <div x-show="selectedCompanyId" class="bg-gray-50 border-2 border-gray-200 rounded-xl p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1">Current Balance</p>
                            <p class="text-2xl font-black text-gray-900" x-text="currentBalance.toLocaleString()">0</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1">Credits to Add</p>
                            <p class="text-2xl font-black text-indigo-600" x-text="'+' + (credits || 0).toLocaleString()">+0</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1">New Balance</p>
                            <p class="text-2xl font-black text-green-600" x-text="newBalance.toLocaleString()">0</p>
                        </div>
                    </div>
                </div>

                <!-- SMS Credits -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        SMS Credits to Add *
                    </label>
                    <input type="number"
                           name="sms_credits"
                           x-model.number="credits"
                           value="{{ old('sms_credits') }}"
                           placeholder="100"
                           min="1"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('sms_credits') border-red-500 @enderror"
                           required>
                    @error('sms_credits')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Number of SMS credits to add to the company's wallet</p>
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Reason / Notes *
                    </label>
                    <textarea name="reason"
                              rows="4"
                              placeholder="e.g., Promotional credits for new customer, Compensation for service issue, Testing purposes"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('reason') border-red-500 @enderror"
                              required
                              maxlength="500">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Explain why these credits are being added (for internal records)</p>
                </div>

                <!-- Notification Option -->
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                               name="notify_company"
                               value="1"
                               {{ old('notify_company', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-sm font-bold text-gray-700">Notify Company</span>
                    </label>
                    <p class="ml-8 text-xs text-gray-500">Send an email notification to the company about the credit addition</p>
                </div>

                <!-- Confirmation -->
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1 text-sm text-yellow-800">
                            <p class="font-bold mb-1">Please Confirm</p>
                            <p>Make sure you have verified the company and the number of credits before submitting. This action cannot be undone.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end pt-6 border-t-2 border-gray-200">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Credits
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent Credit Additions -->
        @if($recentCredits->isNotEmpty())
            <div class="mt-8 bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Recent Credit Additions
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Credits Added</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Added By</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentCredits as $credit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        @if($credit->company)
                                            <p class="text-sm font-bold text-gray-900">{{ $credit->company->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $credit->company->email }}</p>
                                        @else
                                            <p class="text-sm text-gray-500">No organizer</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-lg font-black text-indigo-600">+{{ number_format($credit->sms_credits) }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-700">{{ $credit->notes ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-700">Admin</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-700">{{ $credit->created_at->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $credit->created_at->diffForHumans() }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function addCreditsForm() {
    return {
        selectedCompanyId: '{{ old('company_id', '') }}',
        companies: @json($companies),
        credits: {{ old('sms_credits', 0) }},

        get selectedCompany() {
            if (!this.selectedCompanyId) return null;
            return this.companies.find(c => c.id == this.selectedCompanyId);
        },

        get currentBalance() {
            return this.selectedCompany ? this.selectedCompany.sms_balance : 0;
        },

        get newBalance() {
            return parseInt(this.currentBalance) + parseInt(this.credits || 0);
        }
    }
}
</script>
@endsection
