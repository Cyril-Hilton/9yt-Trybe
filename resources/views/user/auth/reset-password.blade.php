@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="max-w-md w-full space-y-8">
    <div>
        <h2 class="text-center text-3xl font-extrabold text-gray-900 dark:text-white">
            Set new password
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
            Enter your new password below
        </p>
    </div>

    <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Email Address
                </label>
                <input
                    id="email"
                    type="email"
                    value="{{ $email }}"
                    disabled
                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-lg"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    New Password <span class="text-red-500">*</span>
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg focus:outline-none focus:ring-cyan-500 focus:border-cyan-500 @error('password') border-red-500 @enderror"
                    placeholder="Min. 8 characters"
                >
                @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg focus:outline-none focus:ring-cyan-500 focus:border-cyan-500"
                >
            </div>
        </div>

        <div>
            <button
                type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors shadow-lg"
            >
                Reset Password
            </button>
        </div>
    </form>
</div>
@endsection
