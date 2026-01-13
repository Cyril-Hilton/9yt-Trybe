@extends('layouts.admin')

@section('title', 'SMS Contacts Database')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2 gradient-text">SMS Contacts Database</h1>
                <p class="text-gray-400">Manage your contact database for SMS campaigns</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <x-tooltip text="Add a single contact manually">
                    <a href="{{ route('admin.sms-contacts.create') }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 hover-lift transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-indigo-500/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Single Contact
                    </a>
                </x-tooltip>
                <x-tooltip text="Upload multiple contacts via Excel file">
                    <a href="{{ route('admin.sms-contacts.bulk-upload') }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 hover-lift transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-green-500/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Bulk Upload Excel
                    </a>
                </x-tooltip>
            </div>
        </div>
    </div>

    <!-- Stats with Enhanced Glass Effect -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card rounded-xl p-6 hover-lift bg-gradient-to-br from-indigo-600/90 to-indigo-700/90 border-indigo-400/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm mb-1 font-medium">Total Contacts</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($contacts->total()) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="glass-card rounded-xl p-6 hover-lift bg-gradient-to-br from-purple-600/90 to-purple-700/90 border-purple-400/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm mb-1 font-medium">Total Groups</p>
                    <p class="text-3xl font-bold text-white">{{ $groupsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="glass-card rounded-xl p-6 hover-lift bg-gradient-to-br from-pink-600/90 to-pink-700/90 border-pink-400/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-pink-100 text-sm mb-1 font-medium">Revenue Potential</p>
                    <p class="text-3xl font-bold text-white">GH₵{{ number_format($contacts->total() * 1, 2) }}</p>
                    <p class="text-pink-100 text-xs mt-1">@ GH₵1 per contact</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter with Glass Effect -->
    <div class="glass-card rounded-xl p-6 mb-6 border border-gray-700/50 hover-scale">
        <form action="{{ route('admin.sms-contacts.index') }}" method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, or email..."
                   class="flex-1 px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">

            <select name="group" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="">All Groups</option>
                @foreach($groups as $group)
                    <option value="{{ $group }}" {{ request('group') === $group ? 'selected' : '' }}>{{ $group }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 hover-lift transition-all duration-300 shadow-lg hover:shadow-indigo-500/50 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filter
            </button>

            @if(request()->hasAny(['search', 'group']))
            <a href="{{ route('admin.sms-contacts.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 hover-lift transition-all duration-300 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Contacts Table with Glass Effect -->
    <div class="glass-card rounded-xl overflow-hidden border border-gray-700/50">
        @if($contacts->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Phone Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Group</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Added</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach($contacts as $contact)
                    <tr class="hover:bg-gray-700/30 transition-all duration-200 group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-indigo-500/20 rounded-full flex items-center justify-center text-indigo-400 font-semibold text-sm">
                                    {{ strtoupper(substr($contact->name ?? 'N', 0, 1)) }}
                                </div>
                                <div class="text-sm font-medium text-white">{{ $contact->name ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <div class="text-sm text-white font-mono">{{ $contact->phone_number }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <div class="text-sm text-gray-300">{{ $contact->email ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($contact->group)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                    {{ $contact->group }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500">No group</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-300">{{ $contact->notes ? Str::limit($contact->notes, 30) : '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-400">{{ $contact->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <x-tooltip text="Delete this contact">
                                <form action="{{ route('admin.sms-contacts.destroy', $contact->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this contact?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-medium transition-all duration-200 hover:scale-110 inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </x-tooltip>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($contacts->hasPages())
        <div class="px-6 py-4 border-t border-gray-700 bg-gray-900">
            {{ $contacts->links() }}
        </div>
        @endif
        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-400">No contacts found</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'group']))
                    No contacts match your search criteria.
                @else
                    Get started by adding your first contact.
                @endif
            </p>
            <div class="mt-6 flex justify-center gap-3">
                <a href="{{ route('admin.sms-contacts.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Add Single Contact
                </a>
                <a href="{{ route('admin.sms-contacts.bulk-upload') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Bulk Upload
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
