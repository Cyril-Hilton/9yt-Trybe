@extends('layouts.app')

@section('title', 'Checkout - 9yt !Trybe Shop')
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-blue-50 dark:from-gray-900 dark:via-slate-900/20 dark:to-slate-800/20 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold gradient-text mb-2">Checkout</h1>
            <p class="text-gray-600 dark:text-gray-400">Complete your order</p>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 rounded-lg">
            <ul class="list-disc list-inside text-red-800 dark:text-red-200">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('shop.checkout.process') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            <!-- Left Column - Forms -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Information -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Customer Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required
                                   class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" required
                                   class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number *</label>
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required
                                   class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Shipping Address</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Street Address *</label>
                            <input type="text" id="shipping_address" name="shipping_address" value="{{ old('shipping_address') }}" required
                                   placeholder="Start typing your address..."
                                   class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Start typing to see location suggestions</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City *</label>
                                <input type="text" id="city" name="city" value="{{ old('city') }}" required
                                       class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Region</label>
                                <input type="text" id="region" name="region" value="{{ old('region') }}"
                                       class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Payment Method</h2>

                    <input type="hidden" name="payment_method" value="paystack">

                    <div class="flex items-center p-4 border-2 border-cyan-500 dark:border-cyan-400 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 dark:text-white">Pay with Card or Mobile Money</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Secure payment via Paystack (Cards, MTN, Vodafone, AirtelTigo)</div>
                        </div>
                        <svg class="w-12 h-8" viewBox="0 0 48 48" fill="none">
                            <rect width="48" height="48" rx="8" fill="#00C3F7"/>
                            <path d="M24 10L34 20L24 30L14 20L24 10Z" fill="white"/>
                        </svg>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Order Notes (Optional)</h2>
                    <textarea name="notes" rows="3" placeholder="Special instructions for your order..."
                              class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Order Summary</h2>

                    <!-- Cart Items -->
                    <div class="space-y-3 mb-6">
                        @foreach($cartItems as $item)
                        <div class="flex items-center space-x-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                            @if($item->product->image_url)
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 rounded-lg object-cover">
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Qty: {{ $item->quantity }}</p>
                            </div>
                            <p class="font-semibold text-gray-900 dark:text-white">GH₵{{ number_format($item->subtotal, 2) }}</p>
                        </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-gray-700 dark:text-gray-300">
                            <span>Subtotal</span>
                            <span class="font-semibold">GH₵{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700 dark:text-gray-300">
                            <span>Shipping</span>
                            <span class="font-semibold">
                                @if($shippingFee > 0)
                                    GH₵{{ number_format($shippingFee, 2) }}
                                @else
                                    <span class="text-green-600 dark:text-green-400">FREE</span>
                                @endif
                            </span>
                        </div>
                        @if($subtotal < 200)
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Add GH₵{{ number_format(200 - $subtotal, 2) }} more for free shipping!
                        </p>
                        @endif
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                                <span>Total</span>
                                <span class="text-cyan-600 dark:text-cyan-400">GH₵{{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-bold rounded-lg hover:from-cyan-700 hover:to-blue-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Place Order
                    </button>

                    <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-4">
                        By placing your order, you agree to our terms and conditions
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<!-- Google Maps Places API -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places" async defer></script>
<script>
let autocomplete;

function initAutocomplete() {
    const addressInput = document.getElementById('shipping_address');
    const cityInput = document.getElementById('city');
    const regionInput = document.getElementById('region');

    if (!addressInput) return;

    // Create autocomplete instance biased to Ghana
    autocomplete = new google.maps.places.Autocomplete(addressInput, {
        types: ['address'],
        componentRestrictions: { country: 'gh' },
        fields: ['address_components', 'formatted_address', 'name']
    });

    // Handle place selection
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();

        if (!place.address_components) return;

        // Extract address components
        let street = '';
        let city = '';
        let region = '';

        place.address_components.forEach(component => {
            const types = component.types;

            if (types.includes('street_number')) {
                street = component.long_name + ' ';
            }
            if (types.includes('route')) {
                street += component.long_name;
            }
            if (types.includes('locality') || types.includes('postal_town')) {
                city = component.long_name;
            }
            if (types.includes('administrative_area_level_1')) {
                region = component.long_name;
            }
            if (types.includes('sublocality') || types.includes('sublocality_level_1')) {
                if (!city) city = component.long_name;
            }
        });

        // Populate fields
        if (street) addressInput.value = street;
        if (city) cityInput.value = city;
        if (region) regionInput.value = region;
    });
}

// Initialize when Google Maps loads
window.initAutocomplete = initAutocomplete;

// Also try to initialize on load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            initAutocomplete();
        }
    }, 1000);
});
</script>
@endsection
