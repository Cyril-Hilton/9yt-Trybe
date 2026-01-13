@extends('layouts.admin')

@section('title', 'Add Job Portfolio')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <a href="{{ route('admin.jobs.index') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold mb-4 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Portfolios
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Add Job Portfolio</h1>
        <p class="text-gray-400">Create a portfolio on behalf of an applicant.</p>
    </div>

    <form method="POST" action="{{ route('admin.jobs.store') }}" enctype="multipart/form-data" class="bg-gray-800 rounded-xl p-6 border border-gray-700 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">First Name *</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('first_name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Middle Name</label>
                <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('middle_name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Last Name *</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('last_name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g., UI/UX Designer"
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('title')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Job Type *</label>
                <input type="text" name="job_type" value="{{ old('job_type') }}" required
                       placeholder="e.g., Full-time, Contract"
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('job_type')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Portfolio Link *</label>
            <input type="url" name="portfolio_link" value="{{ old('portfolio_link') }}" required
                   placeholder="https://"
                   class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            @error('portfolio_link')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Profile Picture</label>
            <input type="file" name="profile_picture" accept="image/*"
                   class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            @error('profile_picture')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Status *</label>
                <select name="status" required
                        class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="approved" {{ old('status', 'approved') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('status')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Rejection Reason</label>
                <input type="text" name="rejection_reason" value="{{ old('rejection_reason') }}"
                       placeholder="Required if status is rejected"
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('rejection_reason')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.jobs.index') }}" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Add Portfolio
            </button>
        </div>
    </form>
</div>
@endsection
