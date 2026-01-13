@extends($layout ?? 'layouts.company')

@php
    $pollRoutePrefix = $pollRoutePrefix ?? 'organization.polls';
    $isAdmin = $isAdmin ?? false;
@endphp

@section('title', 'Create New Poll')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route($pollRoutePrefix . '.index') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline mb-4 inline-block">
            ← Back to Polls
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Poll</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Set up your pageant, voting poll, or survey</p>
    </div>

    <!-- Form -->
    <form action="{{ route($pollRoutePrefix . '.store') }}" method="POST" enctype="multipart/form-data" class="glass-card rounded-2xl p-8">
        @csrf

        @if($isAdmin)
        <!-- Organizer -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Organizer (Optional)</label>
            <select name="company_id" id="company_id"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <option value="">Global (No Company)</option>
                @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
                @endforeach
            </select>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leave blank to create a global poll.</p>
        </div>
        @endif

        <!-- Title -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Poll Title *</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            @error('title')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Description</label>
            <textarea name="description" rows="4"
                      class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">{{ old('description') }}</textarea>
            @error('description')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Event Association -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Associate with Event (Optional)</label>
            <select name="event_id" id="event_id" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <option value="">None</option>
                @foreach($events as $event)
                <option value="{{ $event->id }}" data-company="{{ $event->company_id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                    {{ $event->title }} - {{ $event->start_date->format('M d, Y') }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Banner Image -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Banner Image</label>
            <input type="file" name="banner_image" accept="image/*"
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Recommended: 1200x600px</p>
        </div>

        <!-- Poll Type -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Poll Type *</label>
            <select name="poll_type" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <option value="pageant" {{ old('poll_type') == 'pageant' ? 'selected' : '' }}>Pageant</option>
                <option value="voting" {{ old('poll_type') == 'voting' ? 'selected' : '' }}>Voting Poll</option>
                <option value="survey" {{ old('poll_type') == 'survey' ? 'selected' : '' }}>Survey</option>
            </select>
        </div>

        <!-- Voting Type -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Voting Type *</label>
            <select name="voting_type" id="voting_type" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <option value="free" {{ old('voting_type') == 'free' ? 'selected' : '' }}>Free</option>
                <option value="paid" {{ old('voting_type') == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>

        <!-- Vote Price (shown only if paid) -->
        <div class="mb-6" id="vote_price_section" style="display: none;">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Vote Price (GH₵) *</label>
            <input type="number" name="vote_price" value="{{ old('vote_price', '1.00') }}" step="0.01" min="0"
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
        </div>

        <!-- Grid for checkboxes and settings -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Allow Multiple Votes -->
            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                <input type="checkbox" name="allow_multiple_votes" id="allow_multiple_votes" value="1" {{ old('allow_multiple_votes') ? 'checked' : '' }}
                       class="w-5 h-5 text-cyan-600 rounded focus:ring-cyan-500">
                <label for="allow_multiple_votes" class="ml-3 text-sm font-medium text-gray-900 dark:text-white">Allow Multiple Votes</label>
            </div>

            <!-- Show Results -->
            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                <input type="checkbox" name="show_results" id="show_results" value="1" {{ old('show_results', true) ? 'checked' : '' }}
                       class="w-5 h-5 text-cyan-600 rounded focus:ring-cyan-500">
                <label for="show_results" class="ml-3 text-sm font-medium text-gray-900 dark:text-white">Show Results Publicly</label>
            </div>

            <!-- Require Login -->
            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                <input type="checkbox" name="require_login" id="require_login" value="1" {{ old('require_login') ? 'checked' : '' }}
                       class="w-5 h-5 text-cyan-600 rounded focus:ring-cyan-500">
                <label for="require_login" class="ml-3 text-sm font-medium text-gray-900 dark:text-white">Require Login to Vote</label>
            </div>
        </div>

        <!-- Max Votes Per User -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Max Votes Per User (Optional)</label>
            <input type="number" name="max_votes_per_user" value="{{ old('max_votes_per_user') }}" min="1"
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leave empty for unlimited votes</p>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Start Date (Optional)</label>
                <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">End Date (Optional)</label>
                <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white rounded-xl font-semibold shadow-lg transition-all hover:scale-105">
                Create Poll
            </button>
            <a href="{{ route($pollRoutePrefix . '.index') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const votingType = document.getElementById('voting_type');
    const votePriceSection = document.getElementById('vote_price_section');
    const companySelect = document.getElementById('company_id');
    const eventSelect = document.getElementById('event_id');

    function toggleVotePrice() {
        if (votingType.value === 'paid') {
            votePriceSection.style.display = 'block';
        } else {
            votePriceSection.style.display = 'none';
        }
    }

    function filterEvents() {
        if (!companySelect || !eventSelect) {
            return;
        }

        const companyId = companySelect.value;
        Array.from(eventSelect.options).forEach((option) => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            const eventCompany = option.getAttribute('data-company');
            option.hidden = !companyId || eventCompany !== companyId;
        });

        eventSelect.disabled = !companyId;
        if (eventSelect.selectedOptions.length && eventSelect.selectedOptions[0].hidden) {
            eventSelect.value = '';
        }
    }

    votingType.addEventListener('change', toggleVotePrice);
    toggleVotePrice(); // Initial check

    if (companySelect) {
        companySelect.addEventListener('change', filterEvents);
        filterEvents();
    }
});
</script>
@endsection
