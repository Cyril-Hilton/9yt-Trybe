<div x-data="{ searchOpen: false, query: '', suggestions: [], loading: false, error: '' }" class="relative">
    <button @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())"
            class="text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 transition"
            aria-label="Search">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </button>

    <!-- Search Dropdown with Live Suggestions -->
    <div x-show="searchOpen" @click.away="searchOpen = false" x-cloak
         class="fixed md:absolute left-4 right-4 md:left-auto md:right-0 mt-3 md:w-96 glass-dropdown rounded-2xl p-4 z-[9999] shadow-2xl"
         style="top: 70px;">
        <form action="{{ route('search') }}" method="GET">
            <div class="relative">
                <input
                    x-ref="searchInput"
                    type="text"
                    name="q"
                    x-model="query"
                    @input.debounce.300ms="
                        error = '';
                        if(query.length > 0) {
                            loading = true;
                            const controller = new AbortController();
                            const timeoutId = setTimeout(() => controller.abort(), 5000);
                            fetch('/search/quick?q=' + encodeURIComponent(query), { signal: controller.signal })
                                .then(r => r.json())
                                .then(data => { suggestions = data.suggestions || []; })
                                .catch((err) => {
                                    suggestions = [];
                                    error = err && err.name === 'AbortError'
                                        ? 'Search timed out. Please try again.'
                                        : 'Search is temporarily unavailable.';
                                })
                                .finally(() => { clearTimeout(timeoutId); loading = false; });
                        } else {
                            suggestions = [];
                        }
                    "
                    placeholder="Search events, organizers, categories..."
                    class="w-full px-4 py-3 pl-10 rounded-xl bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all"
                    autocomplete="off"
                >
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <svg x-show="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-cyan-500 animate-spin"
                     fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Live Suggestions -->
            <div x-show="suggestions.length > 0" class="mt-3 max-h-96 overflow-y-auto">
                <template x-for="item in suggestions" :key="item.id + '-' + item.type">
                    <a :href="item.url" class="block p-3 rounded-xl hover:bg-white/30 dark:hover:bg-gray-800/30 transition-all mb-1">
                        <div class="flex items-center gap-3">
                            <template x-if="item.image">
                                <img :src="item.image" class="w-12 h-12 rounded-lg object-cover flex-shrink-0" loading="lazy" />
                            </template>
                            <template x-if="!item.image">
                                <div class="w-12 h-12 rounded-lg flex-shrink-0 flex items-center justify-center"
                                     :class="{
                                          'bg-cyan-100 dark:bg-cyan-900/30': item.type === 'event',
                                          'bg-blue-100 dark:bg-blue-900/30': item.type === 'organizer',
                                          'bg-purple-100 dark:bg-purple-900/30': item.type === 'category' || item.type === 'poll',
                                          'bg-pink-100 dark:bg-pink-900/30': item.type === 'contestant',
                                          'bg-green-100 dark:bg-green-900/30': item.type === 'product',
                                          'bg-amber-100 dark:bg-amber-900/30': item.type === 'survey',
                                          'bg-indigo-100 dark:bg-indigo-900/30': item.type === 'conference',
                                          'bg-gray-100 dark:bg-gray-900/30': item.type === 'action'
                                      }">
                                    <svg class="w-6 h-6" :class="{
                                        'text-cyan-600': item.type === 'event',
                                        'text-blue-600': item.type === 'organizer',
                                        'text-purple-600': item.type === 'category' || item.type === 'poll',
                                        'text-pink-600': item.type === 'contestant',
                                        'text-green-600': item.type === 'product',
                                        'text-amber-600': item.type === 'survey',
                                        'text-indigo-600': item.type === 'conference',
                                        'text-gray-600': item.type === 'action'
                                    }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-sm text-gray-900 dark:text-white truncate" x-text="item.title"></div>
                                <div class="text-xs text-gray-600 dark:text-gray-400" x-text="item.subtitle"></div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-lg flex-shrink-0 capitalize"
                                  :class="{
                                      'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300': item.type === 'event',
                                      'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': item.type === 'organizer',
                                      'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300': item.type === 'category' || item.type === 'poll',
                                      'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300': item.type === 'contestant',
                                      'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300': item.type === 'product',
                                      'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300': item.type === 'survey',
                                      'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300': item.type === 'conference',
                                      'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300': item.type === 'action'
                                  }"
                                  x-text="item.type"></span>
                        </div>
                    </a>
                </template>
            </div>

            <div x-show="error" class="mt-3 text-center text-sm text-red-500 py-4" x-text="error"></div>

            <div x-show="query && suggestions.length === 0 && !loading && !error"
                 class="mt-3 text-center text-sm text-gray-500 dark:text-gray-400 py-4">
                No results found. Press Enter to see all results.
            </div>

            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                Start typing to see suggestions • Press Enter to search
            </div>
        </form>
    </div>
</div>
