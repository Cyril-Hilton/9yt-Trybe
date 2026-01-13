@extends('layouts.admin')

@section('title', 'Edit SMS Plan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Edit SMS Plan
                    </h1>
                    <p class="mt-2 text-gray-600">Update SMS plan details</p>
                </div>
                <a href="{{ route('admin.sms.plans.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Plans
                </a>
            </div>
        </div>

        <!-- Warning Card -->
        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-6 mb-8">
            <div class="flex items-start">
                <svg class="w-8 h-8 text-yellow-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-yellow-900 mb-2">Important Note</h3>
                    <ul class="space-y-1 text-sm text-yellow-800">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Changing the price will only affect new purchases</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Existing transactions will not be affected</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Deactivating a plan will hide it from companies</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Plan Details
                </h2>
            </div>

            <form method="POST" action="{{ route('admin.sms.plans.update', $plan) }}" class="p-6 space-y-6" x-data="{
                credits: {{ old('sms_credits', $plan->sms_credits) }},
                price: {{ old('price', $plan->price) }},
                get pricePerCredit() {
                    if (this.credits === 0) return 0;
                    return (this.price / this.credits).toFixed(4);
                }
            }">
                @csrf
                @method('PUT')

                <!-- Plan Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Plan Name *
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $plan->name) }}"
                           placeholder="e.g., Starter Plan, Business Plan, Enterprise"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Description (Optional)
                    </label>
                    <textarea name="description"
                              rows="3"
                              placeholder="Brief description of this plan..."
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('description') border-red-500 @enderror"
                              maxlength="255">{{ old('description', $plan->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Maximum 255 characters</p>
                </div>

                <!-- SMS Credits and Price -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            SMS Credits *
                        </label>
                        <input type="number"
                               name="sms_credits"
                               x-model.number="credits"
                               value="{{ old('sms_credits', $plan->sms_credits) }}"
                               placeholder="100"
                               min="1"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('sms_credits') border-red-500 @enderror"
                               required>
                        @error('sms_credits')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Price (GH₵) *
                        </label>
                        <input type="number"
                               name="price"
                               x-model.number="price"
                               value="{{ old('price', $plan->price) }}"
                               placeholder="10.00"
                               step="0.01"
                               min="0.01"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('price') border-red-500 @enderror"
                               required>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Price per Credit Calculation -->
                <div class="bg-indigo-50 border-2 border-indigo-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-indigo-900">Price per Credit</p>
                            <p class="text-xs text-indigo-700">Automatically calculated</p>
                        </div>
                        <p class="text-2xl font-black text-indigo-600">
                            GH₵ <span x-text="pricePerCredit">0.0000</span>
                        </p>
                    </div>
                </div>

                <!-- Badge -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Badge (Optional)
                    </label>
                    <input type="text"
                           name="badge"
                           value="{{ old('badge', $plan->badge) }}"
                           placeholder="e.g., Most Popular, Best Value, Recommended"
                           maxlength="50"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('badge') border-red-500 @enderror">
                    @error('badge')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Add a badge to highlight this plan (e.g., "Most Popular")</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-sm font-bold text-gray-700">Plan is Active</span>
                    </label>
                    <p class="ml-8 text-xs text-gray-500">Active plans are visible to companies in the SMS wallet</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200">
                    <a href="{{ route('admin.sms.plans.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
