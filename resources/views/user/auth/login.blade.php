@extends('layouts.auth')

@section('title', 'Event Attendee Login')

@section('content')
<div class="max-w-md w-full glass-effect p-6 sm:p-8 rounded-2xl shadow-2xl transition-all duration-300 transform hover:scale-[1.01]">
    <div class="text-center mb-8">
        <div class="inline-block bg-gradient-to-br from-purple-500 to-pink-600 p-3 rounded-2xl shadow-lg mb-4 transform rotate-3 hover:rotate-6 transition-transform">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
        </div>
        <h2 class="text-center text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Welcome Back
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
            Sign in to your Attendee account
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

    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Login Method Toggle --}}
    <div x-data="{
        loginMethod: {{ session('otp_sent') ? "'phone'" : "'email'" }},
        otpSent: {{ session('otp_sent') ? 'true' : 'false' }},
        phone: '{{ session('phone') ?? '' }}',
        countdown: 60,
        timer: null,
        startTimer() {
            this.countdown = 60;
            if (this.timer) clearInterval(this.timer);
            this.timer = setInterval(() => {
                if (this.countdown > 0) this.countdown--;
                else clearInterval(this.timer);
            }, 1000);
        }
    }" x-init="if (otpSent) startTimer()" class="space-y-6">
        
        {{-- Toggle Buttons --}}
        <div class="flex gap-2 p-1 bg-gray-100 dark:bg-gray-800/80 rounded-xl mb-6">
            <button type="button"
                    @click="loginMethod = 'email'"
                    :class="loginMethod === 'email' ? 'bg-white dark:bg-gray-700 text-purple-600 dark:text-purple-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                    class="flex-1 px-4 py-2.5 rounded-lg font-medium transition-all text-sm flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email
            </button>
            <button type="button"
                    @click="loginMethod = 'phone'"
                    :class="loginMethod === 'phone' ? 'bg-white dark:bg-gray-700 text-purple-600 dark:text-purple-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                    class="flex-1 px-4 py-2.5 rounded-lg font-medium transition-all text-sm flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Phone
            </button>
        </div>

        {{-- Email/Password Login Form --}}
        <form x-show="loginMethod === 'email'" class="space-y-5" action="{{ route('user.login') }}" method="POST"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 transform scale-95"
              x-transition:enter-end="opacity-100 transform scale-100">
        @csrf
        @if(request('redirect'))
        <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif

        <div class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Email Address
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
            
            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Password
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
                        placeholder="••••••••"
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
        </div>

        <div class="flex items-center justify-between mt-2">
            <div class="flex items-center">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 transition-colors"
                >
                <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300 select-none">
                    Remember me
                </label>
            </div>

            <div class="text-sm">
                <a href="{{ route('password.request') }}" class="font-medium text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 dark:hover:text-cyan-300 transition-colors">
                    Forgot password?
                </a>
            </div>
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-all shadow-lg hover:shadow-cyan-500/30 transform hover:-translate-y-0.5"
            >
                Sign in
            </button>
        </div>
    </form>

    {{-- Phone OTP Login Form --}}
    <form x-show="loginMethod === 'phone'" class="space-y-6"
          x-ref="phoneForm"
          action="{{ route('user.send-otp') }}"
          method="POST"
          @submit="if (otpSent) { $el.action = '{{ route('user.verify-otp') }}' } else { $el.action = '{{ route('user.send-otp') }}' }"
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 transform scale-95"
          x-transition:enter-end="opacity-100 transform scale-100">
        @csrf

        <div x-show="!otpSent" class="space-y-4">
            <div>
                <label for="phone-otp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Phone Number
                </label>
                <div class="relative">
                    <input
                        id="phone-otp"
                        name="phone"
                        type="tel"
                        x-model="phone"
                        :required="!otpSent"
                        class="w-full appearance-none relative block px-3 py-2.5 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 sm:text-sm transition-all shadow-sm @error('phone') border-red-500 @enderror"
                        placeholder="Phone number"
                    >
                    <input type="hidden" name="full_phone" id="full_phone">
                </div>
                @error('phone')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div x-show="otpSent" x-cloak class="space-y-4">
            <input type="hidden" name="phone" :value="phone">

            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg text-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('message') ?? 'OTP sent to your phone number!' }}
            </div>

            <div>
                <label for="otp" class="block text-center text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Enter the code sent to your phone
                </label>
                <input
                    id="otp"
                    name="otp"
                    type="text"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    :required="otpSent"
                    class="appearance-none relative block w-full px-3 py-3 border border-blue-300 dark:border-blue-600 placeholder-gray-400 dark:placeholder-gray-600 text-gray-900 dark:text-white bg-blue-50/50 dark:bg-gray-800/80 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-center text-3xl tracking-[0.5em] font-mono transition-all shadow-inner @error('otp') border-red-500 @enderror"
                    placeholder="000000"
                    autocomplete="one-time-code"
                >
                @error('otp')
                <p class="mt-1 text-center text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-all shadow-lg hover:shadow-cyan-500/30 transform hover:-translate-y-0.5"
            >
                <span x-show="!otpSent">Send OTP</span>
                <span x-show="otpSent" x-cloak>Verify & Sign In</span>
            </button>
        </div>

        <div x-show="otpSent" x-cloak class="text-center space-y-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Didn't receive the code? 
                <span x-show="countdown > 0" class="font-medium text-purple-600 dark:text-purple-400">
                    Resend in <span x-text="countdown"></span>s
                </span>
                <button type="button" 
                        x-show="countdown === 0"
                        @click="otpSent = false; $nextTick(() => $refs.phoneForm.submit())"
                        class="font-medium text-purple-600 dark:text-purple-400 hover:text-purple-500 dark:hover:text-purple-300 transition-colors underline decoration-2 underline-offset-2">
                    Resend OTP
                </button>
            </p>
            
            <button type="button" 
                    @click="otpSent = false; phone = '';"
                    class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors flex items-center justify-center mx-auto">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Change phone number
            </button>
        </div>
    </form>

    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700/50">
        <div class="grid grid-cols-1 gap-3 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Don't have an attendee account?
                <a href="{{ route('user.register') }}" class="font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-500 dark:hover:text-purple-300 transition-colors">
                    Create account
                </a>
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Want to organize events?
                <a href="{{ route('organization.login') }}" class="font-semibold text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 dark:hover:text-cyan-300 transition-colors">
                    Login as Organizer
                </a>
            </p>
        </div>
        <div class="mt-6 text-center">
            <a href="{{ route('admin.login') }}" class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Super Admin Access
            </a>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.querySelector("#phone-otp");
        const fullPhoneInput = document.querySelector("#full_phone");

        if (phoneInput) {
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: "gh",
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js"
            });

            phoneInput.addEventListener('change', function() {
                fullPhoneInput.value = iti.getNumber();
            });

            phoneInput.addEventListener('keyup', function() {
                fullPhoneInput.value = iti.getNumber();
            });

            phoneInput.closest('form').addEventListener('submit', function() {
                fullPhoneInput.value = iti.getNumber();
            });
        }
    });
</script>
@endsection
@endsection
