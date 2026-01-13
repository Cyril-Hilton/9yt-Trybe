@extends('layouts.company')

@section('title', 'Payment Accounts')

@section('content')
<div class="py-12" x-data="{ showAddModal: false, accountType: 'bank', editingAccount: null }">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payment Accounts</h1>
                <p class="mt-2 text-gray-600">Manage where you receive your payouts</p>
            </div>
            <button @click="showAddModal = true; accountType = 'bank'"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition shadow-lg">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Account
                </span>
            </button>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <!-- Accounts Grid -->
        @if($accounts->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Payment Accounts</h3>
            <p class="text-gray-600 mb-6">Add a bank account or mobile money number to receive payouts</p>
            <button @click="showAddModal = true"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Your First Account
            </button>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($accounts as $account)
            <div class="bg-white rounded-lg shadow-md p-6 relative {{ $account->is_default ? 'border-2 border-indigo-500' : '' }}">
                @if($account->is_default)
                <div class="absolute top-4 right-4">
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-bold rounded-full">DEFAULT</span>
                </div>
                @endif

                <!-- Account Icon -->
                <div class="flex items-start gap-4 mb-4">
                    <div class="p-3 {{ $account->account_type === 'bank' ? 'bg-blue-100' : 'bg-green-100' }} rounded-lg">
                        @if($account->account_type === 'bank')
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                        @else
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">
                            {{ $account->account_type === 'bank' ? 'Bank Account' : 'Mobile Money' }}
                        </h3>
                        @if($account->account_type === 'bank')
                        <p class="text-sm text-gray-600">{{ $account->bank_name }}</p>
                        @else
                        <p class="text-sm text-gray-600">{{ $account->mobile_money_network }}</p>
                        @endif
                    </div>
                </div>

                <!-- Account Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    @if($account->account_type === 'bank')
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Account Name:</span>
                            <span class="font-semibold text-gray-900">{{ $account->account_name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Account Number:</span>
                            <span class="font-mono font-semibold text-gray-900">{{ $account->account_number }}</span>
                        </div>
                        @if($account->branch)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Branch:</span>
                            <span class="text-gray-900">{{ $account->branch }}</span>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Name:</span>
                            <span class="font-semibold text-gray-900">{{ $account->mobile_money_name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Number:</span>
                            <span class="font-mono font-semibold text-gray-900">{{ $account->mobile_money_number }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    @if(!$account->is_default)
                    <form action="{{ route('organization.finance.bank-accounts.set-default', $account) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 border-2 border-indigo-300 text-indigo-700 rounded-lg font-semibold hover:bg-indigo-50 transition text-sm">
                            Set as Default
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('organization.finance.bank-accounts.delete', $account) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this payment account?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 border-2 border-red-300 text-red-700 rounded-lg font-semibold hover:bg-red-50 transition text-sm">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-900">
                    <p class="font-semibold mb-2">Payment Account Information</p>
                    <ul class="space-y-1 list-disc list-inside">
                        <li>Your default account will receive all payouts automatically</li>
                        <li>You can add multiple accounts and switch between them</li>
                        <li>Mobile Money: MTN, Vodafone, and AirtelTigo are supported</li>
                        <li>Ensure account details are accurate to avoid payment issues</li>
                        <li>Cannot delete accounts with pending payouts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div x-show="showAddModal"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="showAddModal = false">
        <div class="bg-white rounded-xl max-w-2xl w-full p-8 max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Add Payment Account</h2>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('organization.finance.bank-accounts.store') }}" method="POST">
                @csrf

                <!-- Account Type Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Account Type</label>
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" @click="accountType = 'bank'"
                                :class="accountType === 'bank' ? 'bg-indigo-50 border-indigo-500' : 'bg-white border-gray-300'"
                                class="p-4 border-2 rounded-lg text-left hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                </svg>
                                <div>
                                    <div class="font-semibold text-gray-900">Bank Account</div>
                                    <div class="text-xs text-gray-600">Traditional banking</div>
                                </div>
                            </div>
                        </button>
                        <button type="button" @click="accountType = 'mobile_money'"
                                :class="accountType === 'mobile_money' ? 'bg-green-50 border-green-500' : 'bg-white border-gray-300'"
                                class="p-4 border-2 rounded-lg text-left hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <div class="font-semibold text-gray-900">Mobile Money</div>
                                    <div class="text-xs text-gray-600">MTN, Vodafone, AirtelTigo</div>
                                </div>
                            </div>
                        </button>
                    </div>
                    <input type="hidden" name="account_type" :value="accountType">
                </div>

                <!-- Bank Account Fields -->
                <div x-show="accountType === 'bank'" x-cloak class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name <span class="text-red-500">*</span></label>
                        <select name="bank_name" :required="accountType === 'bank'"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="">Select Bank</option>
                            <option value="Access Bank">Access Bank</option>
                            <option value="ADB Bank">ADB Bank</option>
                            <option value="Absa Bank">Absa Bank</option>
                            <option value="Bank of Africa">Bank of Africa</option>
                            <option value="Consolidated Bank">Consolidated Bank</option>
                            <option value="Ecobank">Ecobank</option>
                            <option value="FBNBank">FBNBank</option>
                            <option value="Fidelity Bank">Fidelity Bank</option>
                            <option value="First Atlantic Bank">First Atlantic Bank</option>
                            <option value="First National Bank">First National Bank</option>
                            <option value="GCB Bank">GCB Bank</option>
                            <option value="Guaranty Trust Bank">Guaranty Trust Bank (GTBank)</option>
                            <option value="National Investment Bank">National Investment Bank (NIB)</option>
                            <option value="OmniBSIC Bank">OmniBSIC Bank</option>
                            <option value="Prudential Bank">Prudential Bank</option>
                            <option value="Republic Bank">Republic Bank</option>
                            <option value="Société Générale Ghana">Société Générale Ghana</option>
                            <option value="Stanbic Bank">Stanbic Bank</option>
                            <option value="Standard Chartered Bank">Standard Chartered Bank</option>
                            <option value="UBA Ghana">UBA Ghana</option>
                            <option value="Universal Merchant Bank">Universal Merchant Bank</option>
                            <option value="Zenith Bank">Zenith Bank</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Name <span class="text-red-500">*</span></label>
                        <input type="text" name="account_name" :required="accountType === 'bank'"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Number <span class="text-red-500">*</span></label>
                        <input type="text" name="account_number" :required="accountType === 'bank'"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="1234567890">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Branch (Optional)</label>
                        <input type="text" name="branch"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="Accra Main Branch">
                    </div>
                </div>

                <!-- Mobile Money Fields -->
                <div x-show="accountType === 'mobile_money'" x-cloak class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Network <span class="text-red-500">*</span></label>
                        <select name="mobile_money_network" :required="accountType === 'mobile_money'"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="">Select Network</option>
                            <option value="MTN">MTN Mobile Money</option>
                            <option value="Vodafone">Vodafone Cash</option>
                            <option value="AirtelTigo">AirtelTigo Money</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Name <span class="text-red-500">*</span></label>
                        <input type="text" name="mobile_money_name" :required="accountType === 'mobile_money'"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number <span class="text-red-500">*</span></label>
                        <input type="text" name="mobile_money_number" :required="accountType === 'mobile_money'"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="0241234567">
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" @click="showAddModal = false"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition shadow-lg">
                        Add Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
