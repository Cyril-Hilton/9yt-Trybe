@extends('layouts.auth')

@section('title', 'Admin Login')

@section('content')
<div class="max-w-md w-full">
    <!-- Logo and Header -->
    <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
            <div class="p-4 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-2xl border-4 border-indigo-400">
                <svg class="h-16 w-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>
        <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2">Super Admin</h2>
        <p class="text-lg text-gray-600 dark:text-gray-400">Management Portal</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border-4 border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
            <h3 class="text-2xl font-bold text-white text-center flex items-center justify-center">
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Sign In to Dashboard
            </h3>
            <p class="text-indigo-100 text-sm text-center mt-2">Enter your credentials to access admin panel</p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mx-8 mt-6 p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 rounded-xl">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mx-8 mt-6 p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-xl">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- Login Method Toggle --}}
        <div x-data="{
            loginMethod: {{ session('otp_sent') ? "'phone'" : "'email'" }},
            otpSent: {{ session('otp_sent') ? 'true' : 'false' }},
            phone: '{{ session('phone') ?? '' }}',
            showPassword: false,
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
        }" x-init="if (otpSent) startTimer()" class="p-8 space-y-6">
            <div class="flex gap-2">
                <button type="button"
                        @click="loginMethod = 'email'"
                        :class="loginMethod === 'email' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                        class="flex-1 px-3 py-2 rounded-lg font-medium text-sm transition-all">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Email
                </button>
                <button type="button"
                        @click="loginMethod = 'phone'"
                        :class="loginMethod === 'phone' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                        class="flex-1 px-3 py-2 rounded-lg font-medium text-sm transition-all">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Phone OTP
                </button>
            </div>

        <!-- Email/Password Login Form -->
        <form x-show="loginMethod === 'email'" method="POST" action="{{ route('admin.login') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                    <svg class="h-4 w-4 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Email Address
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </div>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        required
                        value="{{ old('email') }}"
                        class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 shadow-sm hover:border-indigo-400 transition-all duration-200 text-gray-900 dark:text-white bg-white dark:bg-gray-700 font-medium"
                        placeholder="admin@example.com"
                    >
                </div>
                @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                    <svg class="h-4 w-4 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        id="password"
                        required
                        class="w-full pl-10 pr-12 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 shadow-sm hover:border-indigo-400 transition-all duration-200 text-gray-900 dark:text-white bg-white dark:bg-gray-700 font-medium"
                        placeholder="Enter your password"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-indigo-600 transition-colors"
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
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input
                    type="checkbox"
                    name="remember"
                    id="remember"
                    class="h-5 w-5 rounded-lg border-2 border-gray-400 dark:border-gray-600 text-indigo-600 focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700"
                >
                <label for="remember" class="ml-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Remember me for 30 days
                </label>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full py-4 px-6 border-2 border-transparent rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 font-bold text-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center"
            >
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Sign In to Dashboard
            </button>
        </form>

        {{-- Phone OTP Login Form --}}
        <form x-show="loginMethod === 'phone'"
              x-ref="phoneForm"
              action="{{ route('admin.send-otp') }}"
              method="POST" class="space-y-6"
              @submit="if (otpSent) { $el.action = '{{ route('admin.verify-otp') }}' } else { $el.action = '{{ route('admin.send-otp') }}' }">
            @csrf

            <div x-show="!otpSent">
                <label for="phone-otp" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                    <svg class="h-4 w-4 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Phone Number
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <input
                        type="tel"
                        name="phone"
                        id="phone-otp"
                        x-model="phone"
                        :required="!otpSent"
                        class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 shadow-sm hover:border-indigo-400 transition-all duration-200 text-gray-900 dark:text-white bg-white dark:bg-gray-700 font-medium @error('phone') border-red-500 @enderror"
                        placeholder="0200000000"
                    >
                </div>
                @error('phone')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div x-show="otpSent" x-cloak>
                <input type="hidden" name="phone" :value="phone">

                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 rounded-xl">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('message') ?? 'OTP sent to your phone number!' }}
                    </p>
                </div>

                <label for="otp" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                    <svg class="h-4 w-4 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Enter 6-Digit OTP
                </label>
                <input
                    type="text"
                    name="otp"
                    id="otp"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    :required="otpSent"
                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 shadow-sm hover:border-indigo-400 transition-all duration-200 text-gray-900 dark:text-white bg-white dark:bg-gray-700 font-medium text-center text-2xl tracking-widest @error('otp') border-red-500 @enderror"
                    placeholder="000000"
                >
                @error('otp')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full py-4 px-6 border-2 border-transparent rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 font-bold text-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center"
            >
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span x-show="!otpSent">Send OTP</span>
                <span x-show="otpSent" x-cloak>Verify & Sign In</span>
            </button>

            <div x-show="otpSent" x-cloak class="text-center space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Didn't receive the code? 
                    <span x-show="countdown > 0" class="font-medium text-indigo-600 dark:text-indigo-400">
                        Resend in <span x-text="countdown"></span>s
                    </span>
                    <button type="button" 
                            x-show="countdown === 0"
                            @click="otpSent = false; $nextTick(() => $refs.phoneForm.submit())"
                            class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 transition-colors">
                        Resend OTP
                    </button>
                </p>
                
                <button type="button" 
                        @click="otpSent = false; phone = '';"
                        class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    Change phone number
                </button>
            </div>
        </form>
        </div>

        <!-- Footer -->
        <div class="px-8 py-4 bg-gray-50 dark:bg-gray-900 border-t-2 border-gray-200 dark:border-gray-700 text-center">
            <p class="text-xs text-gray-600 dark:text-gray-400">
                <svg class="h-4 w-4 inline mr-1 text-gray-500 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Secure admin authentication protected by encryption
            </p>
        </div>
    </div>
</div>
@endsection
