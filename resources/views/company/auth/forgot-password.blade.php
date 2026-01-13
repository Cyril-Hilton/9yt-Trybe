@extends('layouts.auth')

@section('title', 'Organizer Forgot Password')

@section('content')
<div class="max-w-md w-full space-y-8">
    <div>
        <h2 class="text-center text-3xl font-extrabold text-gray-900 dark:text-white">
            Reset organizer password
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
            Enter your email and we'll send you a reset link
        </p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <form class="mt-8 space-y-6" action="{{ route('organization.password.email') }}" method="POST">
        @csrf

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
                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg focus:outline-none focus:ring-cyan-500 focus:border-cyan-500 @error('email') border-red-500 @enderror"
                placeholder="company@example.com"
            >
            @error('email')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <button
                type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors shadow-lg"
            >
                Send Reset Link
            </button>
        </div>

        <div class="text-center">
            <a href="{{ route('organization.login') }}" class="text-sm font-medium text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 dark:hover:text-cyan-300">
                Back to login
            </a>
        </div>
    </form>
</div>
@endsection
