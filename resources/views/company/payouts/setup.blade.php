@extends('layouts.company')

@section('title', 'Set Up Payment Details')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Set Up Payment Details</h1>
            <p class="mt-2 text-gray-600">Choose how you want to receive your payout for {{ $payout->event->title }}</p>
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

        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Payout Summary Card -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-700 mb-1">Net Payout Amount</p>
                    <p class="text-4xl font-bold text-green-800">GH₵{{ number_format($payout->net_amount, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Event Performance</p>
                    <p class="text-gray-900 font-semibold">{{ number_format($payout->total_tickets_sold) }} tickets sold</p>
                    <p class="text-gray-600">{{ number_format($payout->total_attendees) }} attendees checked in</p>
                </div>
            </div>
        </div>

        <!-- Existing Accounts -->
        @if($existingAccounts->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Use Existing Account</h2>
            <form action="{{ route('organization.payouts.store-account', $payout) }}" method="POST" id="existingAccountForm">
                @csrf
                <input type="hidden" name="use_existing_account" value="1">
                <input type="hidden" name="existing_account_id" id="existing_account_id">

                @foreach($existingAccounts as $account)
                <label class="flex items-center p-4 mb-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition border border-gray-200">
                    <input type="radio" name="use_existing" value="{{ $account->id }}" class="w-5 h-5 text-blue-600">
                    <div class="ml-4 flex-1">
                        <p class="text-gray-900 font-semibold">
                            @if($account->account_type === 'mobile_money')
                                {{ $account->mobile_money_network }} - {{ $account->mobile_money_number }}
                            @else
                                {{ $account->bank_name }} - {{ $account->account_number }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ $account->account_type === 'bank' ? 'Bank Account' : 'Mobile Money' }}
                            @if($account->account_type === 'bank' && $account->account_name)
                                - {{ $account->account_name }}
                            @elseif($account->account_type === 'mobile_money' && $account->mobile_money_name)
                                - {{ $account->mobile_money_name }}
                            @endif
                        </p>
                    </div>
                    @if($account->is_default)
                    <span class="ml-auto bg-blue-600 text-white text-xs px-2 py-1 rounded">Default</span>
                    @endif
                </label>
                @endforeach

                <button type="submit" class="mt-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition">
                    Use Selected Account
                </button>
            </form>
        </div>

        <div class="text-center text-gray-500 my-6 font-semibold">OR</div>
        @endif

        <!-- New Payment Account Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Add New Payment Account</h2>

            <form action="{{ route('organization.payouts.store-account', $payout) }}" method="POST" x-data="{ accountType: 'mobile_money', network: 'MTN' }">
                @csrf

                <!-- Account Type Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Payment Method</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="account_type" value="mobile_money" x-model="accountType" class="hidden peer">
                            <div class="p-4 border-2 border-gray-300 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                                <p class="font-semibold text-gray-900">📱 Mobile Money</p>
                                <p class="text-sm text-gray-600">MTN, Airtel, Telecel</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="account_type" value="bank" x-model="accountType" class="hidden peer">
                            <div class="p-4 border-2 border-gray-300 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                                <p class="font-semibold text-gray-900">🏦 Bank Transfer</p>
                                <p class="text-sm text-gray-600">All Ghana banks</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Mobile Money Fields -->
                <div x-show="accountType === 'mobile_money'" x-transition>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Network</label>
                        <select name="mobile_money_network" x-model="network" class="w-full px-4 py-3 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="MTN">MTN Mobile Money</option>
                            <option value="Airtel">Airtel Money</option>
                            <option value="Telecel">Telecel Cash</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="mobile_money_number" placeholder="0XXXXXXXXX" class="w-full px-4 py-3 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Account Name</label>
                        <input type="text" name="mobile_money_name" placeholder="Name registered with mobile money" class="w-full px-4 py-3 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Bank Fields -->
                <div x-show="accountType === 'bank'" x-transition>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Name</label>
                        <input type="text" name="bank_name" placeholder="e.g., Access Bank, GCB Bank" class="w-full px-4 py-3 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Account Number</label>
                        <input type="text" name="account_number" placeholder="Account number" class="w-full px-4 py-3 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Account Name</label>
                        <input type="text" name="account_name" placeholder="Name on bank account" class="w-full px-4 py-3 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Branch (Optional)</label>
                        <input type="text" name="branch" placeholder="Branch name" class="w-full px-4 py-3 bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Set as Default -->
                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="set_as_default" class="w-5 h-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-gray-700">Set as default payment account</span>
                    </label>
                </div>

                <!-- Submit -->
                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition">
                        Save & Request Payout
                    </button>
                    <a href="{{ route('organization.payouts.show', $payout) }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.querySelectorAll('input[name="use_existing"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('existing_account_id').value = this.value;
    });
});
</script>
@endsection
