@extends('layouts.company')

@section('title', 'Add Attendant')

@section('content')
<div class="p-4 max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('organization.staff.index') }}" class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-800 dark:hover:text-cyan-300 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Attendants
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Attendant</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Create a new attendant for ticket scanning</p>
    </div>

    <div class="glass-card">
        <form action="{{ route('organization.staff.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number *</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           placeholder="+1234567890"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                    @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assign Events (Optional)</label>
                    <select name="event_ids[]" multiple size="5"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                        @foreach(auth()->guard('company')->user()->events as $event)
                        <option value="{{ $event->id }}"
                                {{ in_array($event->id, old('event_ids', [])) ? 'selected' : '' }}>
                            {{ $event->title }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple. Leave empty for all events.</p>
                    @error('event_ids')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-cyan-50/50 dark:bg-cyan-900/20 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-cyan-800 dark:text-cyan-300">
                        <p class="font-medium mb-1">Login Details</p>
                        <p>Attendants log in with their phone number and a one-time code.</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('organization.staff.index') }}" class="glass-btn-secondary">Cancel</a>
                <button type="submit" class="glass-btn-primary">Create Attendant</button>
            </div>
        </form>
    </div>
</div>
@endsection
