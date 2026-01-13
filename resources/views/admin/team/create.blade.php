@extends('layouts.admin')

@section('title', 'Add Team Member')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <a href="{{ route('admin.team.index') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold mb-4 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Team Applications
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Add Team Member</h1>
        <p class="text-gray-400">Create a team member application on behalf of someone.</p>
    </div>

    <form method="POST" action="{{ route('admin.team.store') }}" class="bg-gray-800 rounded-xl p-6 border border-gray-700 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('full_name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g., Event Coordinator"
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('title')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Role *</label>
                <input type="text" name="role" value="{{ old('role') }}" required
                       placeholder="e.g., Volunteer, Staff, Intern"
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('role')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Contact Number *</label>
                <input type="text" name="contact_number" value="{{ old('contact_number') }}" required
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('contact_number')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('email')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Socials</label>
                <input type="text" name="socials" value="{{ old('socials') }}"
                       placeholder="@handle or profile link"
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('socials')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Portfolio Link</label>
            <input type="url" name="portfolio_link" value="{{ old('portfolio_link') }}"
                   placeholder="https://"
                   class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            @error('portfolio_link')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Job Description *</label>
            <textarea name="job_description" rows="4" required
                      class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('job_description') }}</textarea>
            @error('job_description')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
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
            <a href="{{ route('admin.team.index') }}" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Add Team Member
            </button>
        </div>
    </form>
</div>
@endsection
