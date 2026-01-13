{{-- Toast Notification System --}}
<div x-data="notifications()"
     @notify.window="add($event.detail.message, $event.detail.type || 'info', $event.detail.duration || 5000)"
     class="fixed top-4 right-4 z-[9999] space-y-3 max-w-md">

    <template x-for="item in items" :key="item.id">
        <div x-show="item.show"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="glass-dropdown rounded-xl p-4 shadow-2xl border-l-4 cursor-pointer hover-lift"
             :class="{
                 'border-green-500': item.type === 'success',
                 'border-red-500': item.type === 'error',
                 'border-yellow-500': item.type === 'warning',
                 'border-blue-500': item.type === 'info'
             }"
             @click="remove(item.id)">

            <div class="flex items-start gap-3">
                {{-- Icon --}}
                <div class="flex-shrink-0">
                    <template x-if="item.type === 'success'">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <template x-if="item.type === 'error'">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <template x-if="item.type === 'warning'">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </template>
                    <template x-if="item.type === 'info'">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                </div>

                {{-- Message --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.message"></p>
                </div>

                {{-- Close Button --}}
                <button @click.stop="remove(item.id)"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        aria-label="Close notification">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>

{{-- Session-based Flash Messages --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: {
                message: '{{ session('success') }}',
                type: 'success',
                duration: 5000
            }
        }));
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: {
                message: '{{ session('error') }}',
                type: 'error',
                duration: 7000
            }
        }));
    });
</script>
@endif

@if(session('warning'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: {
                message: '{{ session('warning') }}',
                type: 'warning',
                duration: 6000
            }
        }));
    });
</script>
@endif

@if(session('info'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: {
                message: '{{ session('info') }}',
                type: 'info',
                duration: 5000
            }
        }));
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @foreach($errors->all() as $error)
        window.dispatchEvent(new CustomEvent('notify', {
            detail: {
                message: '{{ $error }}',
                type: 'error',
                duration: 8000
            }
        }));
        @endforeach
    });
</script>
@endif
