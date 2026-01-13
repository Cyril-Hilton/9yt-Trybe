<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - 9yt !Trybe</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}">
    <!-- CRITICAL: Set dark mode BEFORE any rendering to prevent flash -->
    <script>
        (function() {
            var darkMode = localStorage.getItem('darkMode');
            if (darkMode === 'true') {
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
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#0f172a',
                            card: '#1e293b',
                            border: '#334155'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }

        /* iOS 26-style Liquid Glass Effect - Light Mode */
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(24px) saturate(200%) brightness(105%);
            -webkit-backdrop-filter: blur(24px) saturate(200%) brightness(105%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.5);
        }

        /* Dark Mode Glass Effect */
        .dark .glass-effect {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(24px) saturate(200%) brightness(95%);
            -webkit-backdrop-filter: blur(24px) saturate(200%) brightness(95%);
            border: 1px solid rgba(148, 163, 184, 0.15);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.05);
        }

        /* Glass Dropdown - For menus and popovers */
        .glass-dropdown {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px) saturate(200%) brightness(108%);
            -webkit-backdrop-filter: blur(20px) saturate(200%) brightness(108%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 40px 0 rgba(31, 38, 135, 0.18),
                        0 2px 8px 0 rgba(0, 0, 0, 0.08),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.6);
        }

        .dark .glass-dropdown {
            background: rgba(30, 41, 59, 0.9);
            backdrop-filter: blur(20px) saturate(200%) brightness(98%);
            -webkit-backdrop-filter: blur(20px) saturate(200%) brightness(98%);
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 10px 40px 0 rgba(0, 0, 0, 0.6),
                        0 2px 8px 0 rgba(0, 0, 0, 0.3),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.08);
        }

        /* Glass Card - For content cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px) saturate(180%) brightness(106%);
            -webkit-backdrop-filter: blur(16px) saturate(180%) brightness(106%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 24px 0 rgba(31, 38, 135, 0.1),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.4);
        }

        .dark .glass-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px) saturate(180%) brightness(96%);
            -webkit-backdrop-filter: blur(16px) saturate(180%) brightness(96%);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.4),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.06);
        }

        /* TRANSPARENT GLASS HEADER - ULTRA CLEAR */
        .glass-header-transparent {
            background: rgba(255, 255, 255, 0.08) !important;
            backdrop-filter: blur(30px) saturate(180%) brightness(120%) !important;
            -webkit-backdrop-filter: blur(30px) saturate(180%) brightness(120%) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05),
                        inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        }

        .dark .glass-header-transparent {
            background: rgba(15, 23, 42, 0.15) !important;
            backdrop-filter: blur(30px) saturate(180%) brightness(90%) !important;
            -webkit-backdrop-filter: blur(30px) saturate(180%) brightness(90%) !important;
            border-bottom: 1px solid rgba(148, 163, 184, 0.15) !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3),
                        inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/glassmorphism.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-dark-bg transition-colors duration-200">
    @include('components.logo-loader', ['id' => 'page-loader', 'text' => 'Loading Organizer Dashboard...'])

    <script>
        // Hide loader after page loads - wait for Alpine to be fully ready
        window.addEventListener('load', function() {
            setTimeout(() => {
                const loader = document.getElementById('page-loader');
                if (loader) {
                    if (loader.__x && loader.__x.$data) {
                        loader.__x.$data.show = false;
                    } else {
                        loader.style.display = 'none';
                    }
                }
            }, 1000);
        });
    </script>

    <div x-data="{ sidebarOpen: window.innerWidth >= 1024 }" class="min-h-screen">
        <!-- Sidebar with Glass Effect -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed top-0 left-0 z-40 w-64 lg:w-64 md:w-56 h-screen transition-transform duration-300 ease-in-out glass-effect border-r-2 border-white/30 dark:border-gray-700/50 shadow-2xl">
            <div class="h-full px-3 py-4 overflow-y-auto flex flex-col">
                <!-- Logo -->
                <div class="flex items-center justify-center mb-6 p-4 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="text-center">
                        <div class="flex items-center justify-center">
                            <svg class="h-8 w-8 text-slate-700 dark:text-slate-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">9yt !Trybe</h2>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Organizer Portal</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="space-y-1 flex-1">
                    <a href="{{ route('organization.dashboard') }}"
                       class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('organization.dashboard') ? 'bg-white/40 dark:bg-gray-800/60 font-semibold border border-white/30 dark:border-gray-700/40 shadow-lg' : 'bg-white/20 dark:bg-gray-800/30 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <!-- Event Management Section -->
                    <div class="pt-3 mt-3 border-t border-slate-200 dark:border-slate-700">
                        <p class="px-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Event Management</p>

                        <a href="{{ route('organization.events.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('organization.events.*') ? 'bg-white/40 dark:bg-gray-800/60 font-semibold border border-white/30 dark:border-gray-700/40 shadow-lg' : 'bg-white/20 dark:bg-gray-800/30 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium">Events</span>
                        </a>

                        <a href="{{ route('organization.conferences.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('organization.conferences.*') ? 'bg-white/40 dark:bg-gray-800/60 font-semibold border border-white/30 dark:border-gray-700/40 shadow-lg' : 'bg-white/20 dark:bg-gray-800/30 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="font-medium">Conferences</span>
                        </a>

                        <a href="{{ route('organization.surveys.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('organization.surveys.*') ? 'bg-white/40 dark:bg-gray-800/60 font-semibold border border-white/30 dark:border-gray-700/40 shadow-lg' : 'bg-white/20 dark:bg-gray-800/30 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <span class="font-medium">Surveys</span>
                        </a>

                        <a href="{{ route('organization.polls.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('organization.polls.*') ? 'bg-white/40 dark:bg-gray-800/60 font-semibold border border-white/30 dark:border-gray-700/40 shadow-lg' : 'bg-white/20 dark:bg-gray-800/30 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="font-medium">Polls & Voting</span>
                        </a>

                        <a href="{{ route('organization.staff.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('organization.staff.*') ? 'bg-white/40 dark:bg-gray-800/60 font-semibold border border-white/30 dark:border-gray-700/40 shadow-lg' : 'bg-white/20 dark:bg-gray-800/30 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="font-medium">Ticket Attendants</span>
                        </a>
                    </div>

                    <!-- Finance Section -->
                    <div class="pt-3 mt-3 border-t border-slate-200 dark:border-slate-700">
                        <p class="px-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Finance</p>

                        <a href="{{ route('organization.finance.invoices') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('organization.finance.*') ? 'bg-white/40 dark:bg-gray-800/60 font-semibold border border-white/30 dark:border-gray-700/40 shadow-lg' : 'bg-white/20 dark:bg-gray-800/30 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="font-medium">Invoices</span>
                        </a>
                    </div>

                    <!-- Communication Section -->
                    <div class="pt-3 mt-3 border-t border-slate-200 dark:border-slate-700">
                        <p class="px-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Communication</p>

                        <a href="{{ route('organization.sms.dashboard') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('organization.sms.*') ? 'bg-white/40 dark:bg-gray-800/60 font-semibold border border-white/30 dark:border-gray-700/40 shadow-lg' : 'bg-white/20 dark:bg-gray-800/30 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span class="font-medium">Bulk SMS</span>
                        </a>
                    </div>

                    <!-- Organization Section -->
                    <div class="pt-3 mt-3 border-t border-slate-200 dark:border-slate-700">
                        <p class="px-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Organization</p>

                        <a href="{{ route('organization.profile.edit') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('organization.profile.*') ? 'bg-white/40 dark:bg-gray-800/60 font-semibold border border-white/30 dark:border-gray-700/40 shadow-lg' : 'bg-white/20 dark:bg-gray-800/30 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="font-medium">Profile</span>
                        </a>
                    </div>
                </nav>

                <!-- Logout Button -->
                <div class="mt-auto pt-4 border-t border-slate-200 dark:border-slate-700">
                    <form method="POST" action="{{ route('organization.logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center p-3 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 transition-all duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="sidebarOpen ? 'lg:ml-64 md:ml-56 ml-0' : 'ml-0'" class="transition-all duration-300">
            <!-- Top Navigation with LIQUID GLASS EFFECT -->
            <nav class="glass-header-transparent sticky top-0 z-50 shadow-lg transition-all duration-300">
                <div class="px-2 sm:px-4 py-2 sm:py-3">
                    <div class="flex items-center justify-between">
                        <!-- Left Side -->
                        <div class="flex items-center gap-2 sm:gap-3">
                            <button @click="sidebarOpen = !sidebarOpen"
                                    class="p-1.5 sm:p-2 text-white hover:text-cyan-300 rounded-lg hover:bg-white/20 transition-all duration-200 flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <!-- Page Title -->
                            <h1 class="text-sm sm:text-base md:text-lg lg:text-xl font-semibold text-white truncate">@yield('title', 'Dashboard')</h1>
                        </div>

                        <!-- Right Side -->
                        <div class="flex items-center gap-2 sm:gap-3">
                            <!-- Cart Icon - Hidden on small mobile -->
                            <a href="{{ route('shop.cart') }}" class="hidden sm:block p-1.5 sm:p-2 text-white hover:text-cyan-300 rounded-lg hover:bg-white/20 transition-all duration-200 flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </a>

                            <!-- Dark Mode Toggle -->
                            <button @click="darkMode = !darkMode"
                                    class="p-1.5 sm:p-2 text-white hover:text-cyan-300 rounded-lg hover:bg-white/20 transition-all flex-shrink-0">
                                <svg x-show="!darkMode" class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <svg x-show="darkMode" class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </button>

                            <!-- User Profile -->
                            <div class="relative flex-shrink-0" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="flex items-center gap-1 sm:gap-2 p-1 sm:p-1.5 md:p-2 rounded-lg text-white hover:text-cyan-300 hover:bg-white/20 transition-all duration-200">
                                    <div class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold text-xs sm:text-sm">
                                        {{ substr(Auth::guard('company')->user()->name, 0, 2) }}
                                    </div>
                                    <div class="hidden lg:block text-left">
                                        <p class="text-xs sm:text-sm font-semibold text-white truncate max-w-[120px]">{{ Auth::guard('company')->user()->name }}</p>
                                        <p class="text-xs text-cyan-300">Company Admin</p>
                                    </div>
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="open"
                                     @click.away="open = false"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white dark:bg-dark-card border border-slate-200 dark:border-dark-border z-50">
                                    <div class="p-3 border-b border-slate-200 dark:border-slate-700">
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Signed in as</p>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">{{ Auth::guard('company')->user()->email }}</p>
                                    </div>
                                    <div class="p-2">
                                        <a href="{{ route('organization.profile.edit') }}"
                                           class="flex items-center px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Edit Profile
                                        </a>
                                    </div>
                                    <div class="border-t border-slate-200 dark:border-slate-700 p-2">
                                        <form method="POST" action="{{ route('organization.logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="flex items-center w-full px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                </svg>
                                                Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content Area -->
            <main class="p-3 sm:p-4 md:p-6 min-h-[calc(100vh-4rem)]">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
<script>
        // Global form loading state component
        document.addEventListener("alpine:init", () => {
            Alpine.data("formLoading", () => ({
                loading: false,
                submit(callback) {
                    this.loading = true;
                    if (callback) callback();
                }
            }));
        });
    </script>
    <script src="{{ asset('js/alpine-components.js') }}"></script>

    {{-- Toast Notifications --}}
    @include('components.toast-notifications')

    <!-- Floating Chatbot Widget -->
    <div x-data="{
        chatOpen: false,
        messages: [],
        newMessage: '',
        loading: false,
        guestName: '',
        guestEmail: '',
        showGuestForm: false,
        isAuthenticated: true,

        init() {
            this.loadChatHistory();
        },

        async loadChatHistory() {
            try {
                const response = await fetch('/chat/history');
                const data = await response.json();
                this.messages = data.messages;
            } catch (error) {
                console.error('Failed to load chat history:', error);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            this.loading = true;

            const formData = new FormData();
            formData.append('message', this.newMessage);

            try {
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.messages.unshift(data.chat);
                    this.newMessage = '';
                } else {
                    alert(data.message || 'Failed to send message');
                }
            } catch (error) {
                console.error('Failed to send message:', error);
                alert('Failed to send message');
            } finally {
                this.loading = false;
            }
        }
    }"
    class="fixed bottom-6 right-6 z-50">

        <!-- Chat Button -->
        <button @click="chatOpen = !chatOpen"
                class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                :class="{ 'scale-0': chatOpen }">
            <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </button>

        <!-- Chat Panel -->
        <div x-show="chatOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 transform translate-y-4 scale-95"
             @click.away="chatOpen = false"
             x-cloak
             class="absolute bottom-0 right-0 w-[calc(100vw-2rem)] sm:w-96 h-[min(500px,80vh)] sm:h-[min(600px,85vh)] glass-dropdown rounded-2xl shadow-2xl flex flex-col overflow-hidden">

            <!-- Chat Header -->
            <div class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-base sm:text-lg truncate">Chat with Us</h3>
                        <p class="text-xs text-white/80 truncate">We typically reply in a few minutes</p>
                    </div>
                </div>
                <button @click="chatOpen = false" class="text-white/80 hover:text-white transition flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Chat Messages -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900">
                <!-- Welcome Message -->
                <div x-show="messages.length === 0" class="text-center py-8">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-r from-cyan-600 to-blue-600 flex items-center justify-center text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hi there!</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 px-4">How can we help you today? Send us a message and we'll get back to you shortly!</p>
                </div>

                <!-- Messages List -->
                <template x-for="msg in messages" :key="msg.id">
                    <div>
                        <!-- User Message -->
                        <div class="flex justify-end mb-2">
                            <div class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-2xl rounded-tr-sm px-4 py-2 max-w-[80%] shadow-md">
                                <p class="text-sm" x-text="msg.message"></p>
                                <p class="text-xs text-white/70 mt-1" x-text="new Date(msg.created_at).toLocaleString()"></p>
                            </div>
                        </div>

                        <!-- Admin Reply -->
                        <div x-show="msg.admin_reply" class="flex justify-start">
                            <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-2xl rounded-tl-sm px-4 py-2 max-w-[80%] shadow-md">
                                <p class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 mb-1">Admin Reply:</p>
                                <p class="text-sm" x-text="msg.admin_reply"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="msg.replied_at ? new Date(msg.replied_at).toLocaleString() : ''"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Chat Input -->
            <form @submit.prevent="sendMessage" class="p-4 bg-white dark:bg-gray-800 border-t border-white/20 dark:border-gray-700/50 flex-shrink-0">
                <div class="flex gap-2">
                    <textarea x-model="newMessage"
                              placeholder="Type your message..."
                              rows="2"
                              @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                              class="flex-1 px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 resize-none text-sm"></textarea>
                    <button type="submit"
                            :disabled="loading || !newMessage.trim()"
                            :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:scale-110'"
                            class="self-end px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-xl font-medium transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!loading" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <svg x-show="loading" class="w-5 h-5 sm:w-6 sm:h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Press Enter to send, Shift+Enter for new line</p>
            </form>
        </div>
    </div>
</body>
</html>
