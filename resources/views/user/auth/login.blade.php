@extends('layouts.auth')

@section('title', 'Event Attendee Login')

@section('content')
<div class="max-w-md w-full space-y-8">
    <div class="text-center">
        <div class="inline-block bg-gradient-to-br from-purple-500 to-pink-600 p-3 rounded-2xl shadow-lg mb-4">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
        </div>
        <h2 class="text-center text-3xl font-extrabold text-gray-900 dark:text-white">
            Event Attendee Portal
        </h2>
        <p class="mt-2 text-center text-base text-gray-600 dark:text-gray-400">
            Sign in to your Attendee account
        </p>
        <p class="mt-1 text-center text-sm font-medium text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-400 dark:to-pink-400">
            Join the trybe 🎉
        </p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    {{-- Login Method Toggle --}}
    <div x-data="{
        loginMethod: {{ session('otp_sent') ? "'phone'" : "'email'" }},
        otpSent: {{ session('otp_sent') ? 'true' : 'false' }},
        phone: '{{ session('phone') ?? '' }}',
        countryCode: '{{ old('country_code', '+233') }}'
    }" class="space-y-6">
        {{-- Toggle Buttons --}}
        <div class="flex gap-2">
            <button type="button"
                    @click="loginMethod = 'email'"
                    :class="loginMethod === 'email' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="flex-1 px-4 py-2 rounded-lg font-medium transition-all">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email Login
            </button>
            <button type="button"
                    @click="loginMethod = 'phone'"
                    :class="loginMethod === 'phone' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="flex-1 px-4 py-2 rounded-lg font-medium transition-all">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Phone OTP
            </button>
        </div>

        {{-- Email/Password Login Form --}}
        <form x-show="loginMethod === 'email'" class="space-y-6" action="{{ route('user.login') }}" method="POST">
        @csrf
        @if(request('redirect'))
        <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif

        <div class="rounded-md shadow-sm space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Email Address
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    value="{{ old('email') }}"
                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg focus:outline-none focus:ring-cyan-500 focus:border-cyan-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror"
                    placeholder="you@example.com"
                >
                @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Password
                </label>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        class="appearance-none relative block w-full px-3 py-2 pr-12 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg focus:outline-none focus:ring-cyan-500 focus:border-cyan-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror"
                        placeholder="********"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 px-3 text-sm text-gray-600 dark:text-gray-300 hover:text-cyan-600"
                    >
                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
                @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800"
                >
                <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                    Remember me
                </label>
            </div>

            <div class="text-sm">
                <a href="{{ route('password.request') }}" class="font-medium text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 dark:hover:text-cyan-300">
                    Forgot password?
                </a>
            </div>
        </div>

        <div>
            <button
                type="submit"
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors shadow-lg"
            >
                Sign in
            </button>
        </div>

        <div class="text-center space-y-3 mt-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Don't have an attendee account?
                <a href="{{ route('user.register') }}" class="font-medium text-purple-600 dark:text-purple-400 hover:text-purple-500 dark:hover:text-purple-300">
                    Create account
                </a>
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Want to organize events?
                <a href="{{ route('organization.login') }}" class="font-medium text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 dark:hover:text-cyan-300">
                    Sign in as Organizer
                </a>
            </p>
            <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.login') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Super Admin Access
                </a>
            </div>
        </div>
    </form>

    {{-- Phone OTP Login Form --}}
    <form x-show="loginMethod === 'phone'" class="space-y-6"
          action="{{ route('user.send-otp') }}"
          method="POST"
          @submit="if (otpSent) { $el.action = '{{ route('user.verify-otp') }}' } else { $el.action = '{{ route('user.send-otp') }}' }">
        @csrf

        <div x-show="!otpSent" class="rounded-md shadow-sm space-y-4">
            <div>
                <label for="phone-otp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Phone Number
                </label>
                <div class="flex gap-2">
                    <select
                        id="country_code"
                        name="country_code"
                        x-model="countryCode"
                        class="w-28 px-2 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-cyan-500 focus:border-cyan-500"
                    >
                        <option value="+233">+233 GH</option>
                        <option value="+234">+234 NG</option>
                        <option value="+27">+27 ZA</option>
                        <option value="+44">+44 UK</option>
                        <option value="+1">+1 US</option>
                    </select>
                    <input
                        id="phone-otp"
                        name="phone"
                        type="tel"
                        x-model="phone"
                        :required="!otpSent"
                        class="flex-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg focus:outline-none focus:ring-cyan-500 focus:border-cyan-500 focus:z-10 sm:text-sm @error('phone') border-red-500 @enderror"
                        placeholder="Phone number"
                        @blur="if ($el.value && $el.value.charAt(0) !== '+') { $el.value = `${countryCode}${$el.value.replace(/^0+/, '')}` }"
                    >
                </div>
                @error('phone')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div x-show="otpSent" x-cloak class="rounded-md shadow-sm space-y-4">
            <input type="hidden" name="phone" :value="phone">

            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg text-sm">
                {{ session('message') ?? 'OTP sent to your phone number!' }}
            </div>

            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Enter 6-Digit OTP
                </label>
                <input
                    id="otp"
                    name="otp"
                    type="text"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    :required="otpSent"
                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg focus:outline-none focus:ring-cyan-500 focus:border-cyan-500 focus:z-10 sm:text-sm text-center text-2xl tracking-widest @error('otp') border-red-500 @enderror"
                    placeholder="000000"
                >
                @error('otp')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <button
                type="submit"
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors shadow-lg"
            >
                <span x-show="!otpSent">Send OTP</span>
                <span x-show="otpSent" x-cloak>Verify & Sign In</span>
            </button>
        </div>

        <div x-show="otpSent" x-cloak class="text-center">
            <a href="{{ route('user.login') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400">
                Didn't receive code? Try again
            </a>
        </div>
    </form>
    </div>
</div>
@endsection
