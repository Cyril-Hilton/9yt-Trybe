{{-- Cookie Consent Banner Component --}}
<div x-data="{
        show: false,
        hasConsent: localStorage.getItem('cookie_consent') !== null,

        init() {
            if (!this.hasConsent) {
                setTimeout(() => { this.show = true; }, 1000);
            }
        },

        acceptAll() {
            localStorage.setItem('cookie_consent', JSON.stringify({
                essential: true,
                analytics: true,
                marketing: true,
                functional: true,
                timestamp: new Date().toISOString()
            }));
            this.show = false;
        },

        acceptEssential() {
            localStorage.setItem('cookie_consent', JSON.stringify({
                essential: true,
                analytics: false,
                marketing: false,
                functional: false,
                timestamp: new Date().toISOString()
            }));
            this.show = false;
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform translate-y-full opacity-0"
    x-transition:enter-end="transform translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="transform translate-y-0 opacity-100"
    x-transition:leave-end="transform translate-y-full opacity-0"
    x-cloak
    class="cookie-banner show safe-area-bottom"
    role="dialog"
    aria-labelledby="cookie-title"
    aria-describedby="cookie-description">

    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            {{-- Cookie Icon --}}
            <div class="hidden sm:flex flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            {{-- Content --}}
            <div class="flex-1">
                <h3 id="cookie-title" class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-1">
                    We value your privacy
                </h3>
                <p id="cookie-description" class="text-sm text-gray-600 dark:text-gray-400">
                    We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic.
                    By clicking "Accept All", you consent to our use of cookies.
                    <a href="{{ route('legal.cookies') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline font-medium">
                        Learn more
                    </a>
                </p>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto flex-shrink-0">
                <button @click="acceptEssential()"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-all touch-target">
                    Essential Only
                </button>
                <button @click="acceptAll()"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 rounded-lg shadow-lg hover:shadow-xl transition-all touch-target">
                    Accept All
                </button>
            </div>
        </div>
    </div>
</div>
