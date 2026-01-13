@extends('layouts.company')

@section('title', 'Conferences')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Conferences</h1>
                <p class="mt-2 text-gray-600">Manage your conference registration forms</p>
            </div>
            <a href="{{ route('organization.conferences.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium">
                Create New Conference
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="{{ route('organization.conferences.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search conferences..." 
                    value="{{ request('search') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
            <div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                Filter
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('organization.conferences.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Conferences Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($conferences as $conference)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex-1">
                        <a href="{{ route('organization.conferences.show', $conference) }}" class="hover:text-indigo-600">
                            {{ $conference->title }}
                        </a>
                    </h3>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($conference->status === 'active') bg-green-100 text-green-800
                        @elseif($conference->status === 'inactive') bg-gray-100 text-gray-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($conference->status) }}
                    </span>
                </div>

                @if($conference->description)
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $conference->description }}</p>
                @endif

                <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $conference->start_date->format('M d, Y') }}
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        {{ $conference->registrations_count }} registrations
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="bg-blue-50 rounded p-2 text-center">
                        <p class="text-xs text-blue-600 font-medium">Online</p>
                        <p class="text-lg font-bold text-blue-700">{{ $conference->online_registrations_count }}</p>
                    </div>
                    <div class="bg-red-50 rounded p-2 text-center">
                        <p class="text-xs text-red-600 font-medium">In-Person</p>
                        <p class="text-lg font-bold text-red-700">{{ $conference->in_person_registrations_count }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('organization.conferences.show', $conference) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                        View Details →
                    </a>
                    <div class="flex space-x-2">
                        <a href="{{ route('organization.conferences.edit', $conference) }}" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No conferences found</h3>
            <p class="mt-2 text-gray-600">Get started by creating a new conference.</p>
            <a href="{{ route('organization.conferences.create') }}" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Create Conference
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($conferences->hasPages())
    <div class="mt-6">
        {{ $conferences->links() }}
    </div>
    @endif
</div>
@endsection