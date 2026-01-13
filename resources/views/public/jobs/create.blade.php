@extends('layouts.app')

@section('title', 'Add Your Portfolio')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-6">Add Your Work Portfolio</h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">This is our way to assist you reach more customers.</p>
        </div>

        <form action="{{ route('jobs.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                    <input type="text" name="first_name" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                    <input type="text" name="last_name" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Middle Name (Optional)</label>
                <input type="text" name="middle_name" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                <input type="text" name="title" required placeholder="e.g., Photographer, Designer, Videographer" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Job Type *</label>
                <select name="job_type" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Select a Job Type</option>
                    <option value="Photography">Photography</option>
                    <option value="Videography">Videography</option>
                    <option value="Design">Design</option>
                    <option value="Event Planning">Event Planning</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Portfolio / Social Media Link *</label>
                <input type="url" name="portfolio_link" required placeholder="https://" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Profile Picture</label>
                <input type="file" name="profile_picture" accept="image/*" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg font-bold hover:from-cyan-700 hover:to-blue-700 transition shadow-lg text-lg">
                    Submit Portfolio
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
