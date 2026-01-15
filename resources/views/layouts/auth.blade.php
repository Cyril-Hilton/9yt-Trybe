<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') !== 'false' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication') - 9yt !Trybe</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}">
    <!-- CRITICAL: Set dark mode BEFORE any rendering to prevent flash -->
    <script>
        (function() {
            var darkMode = localStorage.getItem('darkMode');
            if (darkMode === null || darkMode === 'true') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }

        /* iOS 26-style Liquid Glass Effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(24px) saturate(200%) brightness(105%);
            -webkit-backdrop-filter: blur(24px) saturate(200%) brightness(105%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12), inset 0 1px 0 0 rgba(255, 255, 255, 0.5);
        }

        .dark .glass-effect {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(24px) saturate(200%) brightness(95%);
            -webkit-backdrop-filter: blur(24px) saturate(200%) brightness(95%);
            border: 1px solid rgba(148, 163, 184, 0.15);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5), inset 0 1px 0 0 rgba(255, 255, 255, 0.05);
        }

        /* Logo hover - no shadow to preserve transparency */
        .logo-hover {
            transition: transform 0.3s ease;
        }
        .logo-hover:hover {
            transform: translateY(-2px) scale(1.02);
        }

        /* intl-tel-input overrides for dark mode */
        .dark .iti__country-list {
            background-color: #1f2937 !important;
            color: white !important;
        }
        .dark .iti__country:hover {
            background-color: #374151 !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Glassmorphism Logo Loader -->
    @include('components.logo-loader', ['id' => 'page-loader', 'text' => 'Loading the !Trybe Community...'])

    <script>
        // Hide loader after page loads - wait for Alpine to be fully ready
        window.addEventListener('load', function() {
            // Give Alpine time to fully initialize
            setTimeout(() => {
                const loader = document.getElementById('page-loader');
                if (loader) {
                    // Try Alpine component first
                    if (loader.__x && loader.__x.$data) {
                        loader.__x.$data.show = false;
                    } else {
                        // Fallback: hide manually
                        loader.style.display = 'none';
                    }
                }
            }, 1000); // Slightly longer delay to ensure Alpine is ready
        });
    </script>

    <!-- Minimal Navigation Header with Glass Effect -->
    <nav class="glass-effect border-b-2 border-white/30 dark:border-gray-700/50 shadow-2xl transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center logo-hover">
                        <img x-show="!darkMode" src="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}" alt="9yt !Trybe Logo" class="h-10 sm:h-12 md:h-14 w-auto">
                        <img x-show="darkMode" x-cloak src="{{ asset('ui/logo/9yt-trybe-logo-dark.png') }}" alt="9yt !Trybe Logo" class="h-10 sm:h-12 md:h-14 w-auto">
                    </a>
                </div>

                <!-- Right Section: Back to Home + Dark Mode Toggle -->
                <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                    <!-- Back to Home Button -->
                    <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Home
                    </a>

                    <!-- Mobile: Icon Only -->
                    <a href="{{ route('home') }}" class="sm:hidden p-1.5 sm:p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>

                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="p-1.5 sm:p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition flex-shrink-0">
                        <svg x-show="!darkMode" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- Flash Messages -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    @yield('scripts')
</body>
</html>
