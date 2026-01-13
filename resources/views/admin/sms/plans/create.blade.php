@extends('layouts.admin')

@section('title', 'Create SMS Plan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Create SMS Plan
                    </h1>
                    <p class="mt-2 text-gray-600">Add a new SMS credit package</p>
                </div>
                <a href="{{ route('admin.sms.plans.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Plans
                </a>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6 mb-8">
            <div class="flex items-start">
                <svg class="w-8 h-8 text-blue-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">SMS Plan Guidelines</h3>
                    <ul class="space-y-1 text-sm text-blue-800">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Choose a clear and descriptive name for the plan</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Price is in Ghana Cedis (GH₵)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Add badges like "Most Popular" or "Best Value" to highlight plans</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>New plans are active by default</span>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Plan Details
                </h2>
            </div>

            <form method="POST" action="{{ route('admin.sms.plans.store') }}" class="p-6 space-y-6" x-data="{
                credits: {{ old('sms_credits', 0) }},
                price: {{ old('price', 0) }},
                get pricePerCredit() {
                    if (this.credits === 0) return 0;
                    return (this.price / this.credits).toFixed(4);
                }
            }">
                @csrf

                <!-- Plan Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Plan Name *
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
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
                              maxlength="255">{{ old('description') }}</textarea>
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
                               value="{{ old('sms_credits') }}"
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
                               value="{{ old('price') }}"
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
                           value="{{ old('badge') }}"
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
                               {{ old('is_active', true) ? 'checked' : '' }}
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
                        Create Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
