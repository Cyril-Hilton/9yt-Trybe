@extends('layouts.company')

@section('title', 'Conference Registrations')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('organization.conferences.show', $conference) }}" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
            ← Back to Conference
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Registrations</h1>
        <p class="mt-2 text-gray-600">{{ $conference->title }}</p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Filters -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <div class="flex space-x-2">
                    <a href="{{ route('organization.conferences.export', [$conference, 'pdf']) }}?type={{ request('type','all') }}" 
                       class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                        PDF
                    </a>

                    <a href="{{ route('organization.conferences.export', [$conference, 'excel']) }}?type={{ request('type','all') }}" 
                       class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                        Excel
                    </a>

                    <a href="{{ route('organization.conferences.export', [$conference, 'csv']) }}?type={{ request('type','all') }}" 
                       class="px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm">
                        CSV
                    </a>
                </div>

                <div class="text-sm text-gray-600">
                    Exports respect the selected type filter.
                </div>
            </div>

            <form action="{{ route('organization.conferences.registrations.index', $conference) }}" method="GET" class="flex gap-4">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Search by name or email..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Search
                </button>
            </form>
        </div>

        <!-- Registrations Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($registrations as $registration)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $registration->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $registration->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $registration->unique_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $registration->attendance_type === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($registration->attendance_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $registration->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($registration->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $registration->created_at->format('M j, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('organization.conferences.registrations.show', [$conference, $registration]) }}" class="text-indigo-600 hover:text-indigo-900">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No registrations found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($registrations->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $registrations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection