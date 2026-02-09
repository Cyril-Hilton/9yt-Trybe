@extends('layouts.auth')

@section('title', 'Event Attendee Registration')

@section('content')
<div class="max-w-md w-full glass-effect p-6 sm:p-8 rounded-2xl shadow-2xl transition-all duration-300 transform hover:scale-[1.01]">
    <div class="text-center mb-8">
        <div class="inline-block bg-gradient-to-br from-purple-500 to-pink-600 p-3 rounded-2xl shadow-lg mb-4 transform rotate-3 hover:rotate-6 transition-transform">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
        </div>
        <h2 class="text-center text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Create Account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
            Join the Event Attendee Portal
        </p>
        <div class="mt-3 inline-flex items-center justify-center px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs font-medium">
            Join the trybe 🎉
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <form class="space-y-5" action="{{ route('user.register') }}" method="POST"
        x-data="{ showPassword: false, showConfirm: false }">
        @csrf
        @if(request('redirect'))
        <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif

        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        required
                        value="{{ old('name') }}"
                        class="pl-10 appearance-none block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 sm:text-sm transition-all shadow-sm @error('name') border-red-500 @enderror"
                        placeholder="John Doe"
                    >
                </div>
                @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        value="{{ old('email') }}"
                        class="pl-10 appearance-none block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 sm:text-sm transition-all shadow-sm @error('email') border-red-500 @enderror"
                        placeholder="you@example.com"
                    >
                </div>
                @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Phone Number <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        required
                        value="{{ old('phone') }}"
                        class="w-full appearance-none relative block px-3 py-2.5 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 sm:text-sm transition-all shadow-sm @error('phone') border-red-500 @enderror"
                        placeholder="Phone number"
                    >
                    <input type="hidden" name="full_phone" id="full_phone">
                </div>
                @error('phone')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input
                        id="password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        class="pl-10 pr-10 appearance-none block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 sm:text-sm transition-all shadow-sm @error('password') border-red-500 @enderror"
                        placeholder="Min. 8 characters"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-cyan-600 transition-colors focus:outline-none"
                    >
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L4.573 4.574m14.853 14.853L14.12 14.12M21.1 12a9.97 9.97 0 01-1.563 3.029m-5.858-.908a3 3 0 11-4.243-4.243M9.878 9.878l4.242 4.242M9.88 9.88L4.573 4.574m14.853 14.853L14.12 14.12" />
                        </svg>
                    </button>
                </div>
                @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        :type="showConfirm ? 'text' : 'password'"
                        required
                        class="pl-10 pr-10 appearance-none block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 sm:text-sm transition-all shadow-sm"
                        placeholder="Confirm password"
                    >
                    <button
                        type="button"
                        @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-cyan-600 transition-colors focus:outline-none"
                    >
                        <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L4.573 4.574m14.853 14.853L14.12 14.12M21.1 12a9.97 9.97 0 01-1.563 3.029m-5.858-.908a3 3 0 11-4.243-4.243M9.878 9.878l4.242 4.242M9.88 9.88L4.573 4.574m14.853 14.853L14.12 14.12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="w-full py-3 px-4 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-cyan-500/30 transform hover:-translate-y-0.5"
            >
                Create Account
            </button>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700/50">
            <div class="grid grid-cols-1 gap-3 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an attendee account?
                    <a href="{{ route('user.login') }}" class="font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-500 dark:hover:text-purple-300 transition-colors">
                        Sign in
                    </a>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Want to organize events?
                    <a href="{{ route('organization.register') }}" class="font-semibold text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 dark:hover:text-cyan-300 transition-colors">
                        Register as Organizer
                    </a>
                </p>
            </div>
        </div>
    </form>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.querySelector("#phone");
        const fullPhoneInput = document.querySelector("#full_phone");

        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "gh",
            separateDialCode: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js"
        });

        // Update hidden field on change
        phoneInput.addEventListener('change', function() {
            fullPhoneInput.value = iti.getNumber();
        });

        phoneInput.addEventListener('keyup', function() {
            fullPhoneInput.value = iti.getNumber();
        });

        // Final check before submit
        phoneInput.closest('form').addEventListener('submit', function() {
            fullPhoneInput.value = iti.getNumber();
        });
    });
</script>
@endsection
@endsection
