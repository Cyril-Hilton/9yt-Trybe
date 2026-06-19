<nav class="sticky top-0 z-50 animated-gradient-bg glass-header-transparent shadow-lg" aria-label="Primary navigation">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex h-16 sm:h-20 items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex-shrink-0 logo-hover" aria-label="9yt !Trybe home">
                <img x-show="!darkMode" src="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}" alt="9yt !Trybe" class="h-11 sm:h-14 w-auto">
                <img x-show="darkMode" x-cloak src="{{ asset('ui/logo/9yt-trybe-logo-dark.png') }}" alt="9yt !Trybe" class="h-11 sm:h-14 w-auto">
            </a>

            <div class="hidden lg:flex flex-1 items-center justify-center gap-1 xl:gap-3">
                <a href="{{ route('events.index') }}" class="px-3 py-2 rounded-lg text-sm xl:text-base font-semibold whitespace-nowrap">Events</a>
                <a href="{{ route('shop.index') }}" class="px-3 py-2 rounded-lg text-sm xl:text-base font-semibold whitespace-nowrap">Shop</a>
                <a href="{{ route('gallery.index') }}" class="px-3 py-2 rounded-lg text-sm xl:text-base font-semibold whitespace-nowrap">Gallery</a>
                <a href="{{ route('blog.index') }}" class="px-3 py-2 rounded-lg text-sm xl:text-base font-semibold whitespace-nowrap">Blog</a>
                <a href="{{ route('jobs.index') }}" class="px-3 py-2 rounded-lg text-sm xl:text-base font-semibold whitespace-nowrap">Jobs</a>
                <a href="{{ route('team.index') }}" class="px-3 py-2 rounded-lg text-sm xl:text-base font-semibold whitespace-nowrap">Team</a>
                <a href="{{ route('about') }}" class="px-3 py-2 rounded-lg text-sm xl:text-base font-semibold whitespace-nowrap">About</a>
                <a href="{{ route('contact') }}" class="px-3 py-2 rounded-lg text-sm xl:text-base font-semibold whitespace-nowrap">Contact</a>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <x-global-search />

                <a href="{{ route('shop.cart') }}" class="hidden sm:inline-flex p-2 rounded-lg" aria-label="Shopping cart">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </a>

                @if(Auth::guard('company')->check())
                    <a href="{{ route('organization.dashboard') }}" class="glass-btn hidden md:inline-flex px-4 py-2 rounded-lg font-semibold whitespace-nowrap">Dashboard</a>
                @elseif(Auth::check())
                    <a href="{{ route('user.dashboard') }}" class="glass-btn hidden md:inline-flex px-4 py-2 rounded-lg font-semibold whitespace-nowrap">Dashboard</a>
                @else
                    <a href="{{ route('user.login') }}" class="hidden md:inline-flex px-3 py-2 rounded-lg font-semibold whitespace-nowrap">Log In</a>
                    <a href="{{ route('user.register') }}" class="glass-btn hidden md:inline-flex px-4 py-2 rounded-lg font-semibold whitespace-nowrap">Sign Up</a>
                @endif

                <button @click="toggleDarkMode()" class="glass-btn p-2 rounded-lg" :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg" aria-label="Toggle mobile menu" :aria-expanded="mobileMenuOpen">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-cloak @click.away="mobileMenuOpen = false" x-transition
             class="lg:hidden absolute left-2 right-2 top-full mt-2 rounded-2xl glass-dropdown shadow-2xl border border-white/30 dark:border-gray-700/50 max-h-[calc(100vh-6rem)] overflow-y-auto">
            <div class="p-3 grid gap-1">
                <a href="{{ route('events.index') }}" class="px-4 py-3 rounded-xl font-semibold">Events</a>
                <a href="{{ route('shop.index') }}" class="px-4 py-3 rounded-xl font-semibold">Shop</a>
                <a href="{{ route('gallery.index') }}" class="px-4 py-3 rounded-xl font-semibold">Gallery</a>
                <a href="{{ route('blog.index') }}" class="px-4 py-3 rounded-xl font-semibold">Blog</a>
                <a href="{{ route('jobs.index') }}" class="px-4 py-3 rounded-xl font-semibold">Jobs</a>
                <a href="{{ route('team.index') }}" class="px-4 py-3 rounded-xl font-semibold">Team</a>
                <a href="{{ route('about') }}" class="px-4 py-3 rounded-xl font-semibold">About</a>
                <a href="{{ route('contact') }}" class="px-4 py-3 rounded-xl font-semibold">Contact</a>
                <a href="{{ route('shop.cart') }}" class="px-4 py-3 rounded-xl font-semibold">Cart</a>

                <div class="border-t border-white/30 dark:border-gray-700/50 mt-2 pt-2">
                    @if(Auth::guard('company')->check())
                        <a href="{{ route('organization.dashboard') }}" class="block px-4 py-3 rounded-xl font-semibold">Organization dashboard</a>
                    @elseif(Auth::check())
                        <a href="{{ route('user.dashboard') }}" class="block px-4 py-3 rounded-xl font-semibold">My dashboard</a>
                        <a href="{{ route('user.tickets') }}" class="block px-4 py-3 rounded-xl font-semibold">My tickets</a>
                    @else
                        <a href="{{ route('user.login') }}" class="block px-4 py-3 rounded-xl font-semibold">Log In</a>
                        <a href="{{ route('user.register') }}" class="glass-btn block px-4 py-3 rounded-xl font-bold text-center">Sign Up</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</nav>
