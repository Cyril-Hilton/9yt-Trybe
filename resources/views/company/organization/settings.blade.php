@extends('layouts.company')

@section('title', 'Organization Settings')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('organization.dashboard') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-semibold mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
            <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Organization Settings
            </h1>
            <p class="mt-2 text-gray-600">Update your organization profile and review key stats</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6">
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Events</p>
                <p class="text-3xl font-black text-gray-900">{{ number_format($stats['total_events']) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-xl border-2 border-green-100 p-6">
                <p class="text-sm font-semibold text-gray-500 mb-1">Approved Events</p>
                <p class="text-3xl font-black text-green-600">{{ number_format($stats['approved_events']) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-xl border-2 border-yellow-100 p-6">
                <p class="text-sm font-semibold text-gray-500 mb-1">Pending Events</p>
                <p class="text-3xl font-black text-yellow-600">{{ number_format($stats['pending_events']) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-xl border-2 border-purple-100 p-6">
                <p class="text-sm font-semibold text-gray-500 mb-1">Followers</p>
                <p class="text-3xl font-black text-purple-600">{{ number_format($stats['total_followers']) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6 lg:col-span-2">
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Revenue</p>
                <p class="text-3xl font-black text-indigo-600">GHS {{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Fee Summary
                </h2>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    @foreach($feeSummary as $label => $value)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">{{ str_replace('_', ' ', $label) }}</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <form action="{{ route('organization.organization.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Organization Details
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Organization Logo</label>
                        <div class="flex items-center space-x-6">
                            @if(auth()->guard('company')->user()->logo)
                                <div class="relative">
                                    <img src="{{ Storage::url(auth()->guard('company')->user()->logo) }}" alt="Organization Logo" class="w-24 h-24 rounded-xl object-cover border-2 border-gray-200">
                                </div>
                            @else
                                <div class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center border-2 border-indigo-200">
                                    <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-xl file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100 file:transition-all file:duration-200">
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF</p>
                                @error('logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Organization Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', auth()->guard('company')->user()->name) }}" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', auth()->guard('company')->user()->email) }}" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->guard('company')->user()->phone) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-bold text-gray-700 mb-2">Website</label>
                        <input type="url" name="website" id="website" value="{{ old('website', auth()->guard('company')->user()->website) }}" placeholder="https://example.com"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('website') border-red-500 @enderror">
                        @error('website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="4" placeholder="Tell us about your organization..."
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', auth()->guard('company')->user()->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6">
                <a href="{{ route('organization.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
