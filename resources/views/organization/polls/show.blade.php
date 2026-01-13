@extends($layout ?? 'layouts.company')

@php
    $pollRoutePrefix = $pollRoutePrefix ?? 'organization.polls';
    $isAdmin = $isAdmin ?? false;
@endphp

@section('title', $poll->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <a href="{{ route($pollRoutePrefix . '.index') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline mb-4 inline-block">
                ← Back to Polls
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $poll->title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ ucfirst($poll->poll_type) }} • {{ ucfirst($poll->voting_type) }}</p>
        </div>
        <div class="flex gap-2">
            @if($poll->status === 'draft')
            <form action="{{ route($pollRoutePrefix . '.publish', $poll) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition">
                    🚀 Publish Poll
                </button>
            </form>
            @elseif($poll->status === 'active')
            <form action="{{ route($pollRoutePrefix . '.close', $poll) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition">
                    🔒 Close Poll
                </button>
            </form>
            @endif
            <a href="{{ route($pollRoutePrefix . '.edit', $poll) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                ✏️ Edit
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-xl">
        {{ session('error') }}
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="glass-card rounded-xl p-6 text-center">
            <div class="text-3xl font-bold text-cyan-600 dark:text-cyan-400">{{ $analytics['contestants_count'] }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Contestants</div>
        </div>
        <div class="glass-card rounded-xl p-6 text-center">
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($analytics['total_votes']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total Votes</div>
        </div>
        <div class="glass-card rounded-xl p-6 text-center">
            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $analytics['views_count'] }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Views</div>
        </div>
        <div class="glass-card rounded-xl p-6 text-center">
            <div class="text-3xl font-bold text-green-600 dark:text-green-400">GH₵{{ number_format($analytics['total_revenue'], 2) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Revenue</div>
        </div>
        <div class="glass-card rounded-xl p-6 text-center">
            <div class="text-3xl font-bold text-pink-600 dark:text-pink-400">{{ number_format($analytics['unique_voters']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Unique Voters</div>
        </div>
    </div>

    <!-- Poll Public URL -->
    @if($poll->status === 'active')
    <div class="glass-card rounded-xl p-6 mb-8">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Public Voting URL</h3>
        <div class="flex gap-2">
            <input type="text" value="{{ $poll->public_url }}" readonly
                   class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-900 dark:text-white">
            <button onclick="copyToClipboard('{{ $poll->public_url }}')" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-semibold transition">
                📋 Copy
            </button>
        </div>
    </div>
    @endif

    <!-- Contestants Section -->
    <div class="glass-card rounded-2xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Contestants</h2>
            <button onclick="document.getElementById('addContestantModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white rounded-lg font-semibold transition">
                + Add Contestant
            </button>
        </div>

        @if($poll->contestants->isEmpty())
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🏆</div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Contestants Yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Add contestants to start collecting votes</p>
            <button onclick="document.getElementById('addContestantModal').classList.remove('hidden')"
                    class="px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white rounded-xl font-semibold transition">
                Add First Contestant
            </button>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($poll->contestants as $contestant)
            <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg">
                <div class="h-64 bg-gray-200 dark:bg-gray-700">
                    <img src="{{ $contestant->photo_url }}" alt="{{ $contestant->name }}" class="w-full h-full object-cover">
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-3 py-1 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 rounded-full text-sm font-bold">
                            #{{ $contestant->contestant_number }}
                        </span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($contestant->total_votes) }} votes
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $contestant->name }}</h3>
                    @if($contestant->bio)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">{{ $contestant->bio }}</p>
                    @endif
                    <form action="{{ route($pollRoutePrefix . '.contestants.remove', [$poll, $contestant]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this contestant?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition font-semibold">
                            Remove
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Add Contestant Modal -->
<div id="addContestantModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Add Contestant</h3>
            <button onclick="document.getElementById('addContestantModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route($pollRoutePrefix . '.contestants.add', $poll) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Contestant Number *</label>
                    <input type="text" name="contestant_number" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Name *</label>
                    <input type="text" name="name" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Bio</label>
                    <textarea name="bio" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Photo *</label>
                    <input type="file" name="photo" accept="image/*" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Video URL (Optional)</label>
                    <input type="url" name="video_url"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white rounded-xl font-semibold transition">
                        Add Contestant
                    </button>
                    <button type="button" onclick="document.getElementById('addContestantModal').classList.add('hidden')"
                            class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('URL copied to clipboard!');
    });
}
</script>
@endsection
