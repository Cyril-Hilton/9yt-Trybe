<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') !== 'false' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - 9yt !Trybe</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}">
    <!-- CRITICAL: Set dark mode BEFORE any rendering to prevent flash -->
    <script>
        (function() {
            var darkMode = localStorage.getItem('darkMode');
            if (darkMode !== 'false') {
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
    <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    @include('components.logo-loader', ['id' => 'page-loader', 'text' => 'Loading Admin Dashboard...'])

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

    <div x-data="{ sidebarOpen: window.innerWidth >= 1024, profileDropdown: false }" class="min-h-screen">
        <!-- Sidebar with Glass Effect -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed top-0 left-0 z-40 w-64 lg:w-64 md:w-56 h-screen transition-transform duration-300 ease-in-out glass-effect border-r-2 border-white/30 dark:border-gray-700/50 shadow-2xl">
            <div class="h-full px-3 py-4 overflow-y-auto">
                <!-- Logo -->
                <div class="flex items-center justify-center mb-6 p-4 bg-gray-800 dark:bg-gray-900 rounded-xl border-2 border-indigo-500 dark:border-indigo-600 shadow-lg">
                    <div class="text-center">
                        <div class="flex items-center justify-center">
                            <svg class="h-10 w-10 text-indigo-400 dark:text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <h2 class="text-xl font-bold text-white">Super Admin</h2>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Management Panel</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.companies.index') }}"
                       class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.companies.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="font-medium">Companies</span>
                    </a>

                    <a href="{{ route('admin.admins.index') }}"
                       class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.admins.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="font-medium">Admin Users</span>
                    </a>

                    <!-- Event Management Section -->
                    <div class="pt-4 mt-4 border-t border-gray-700 dark:border-gray-800">
                        <p class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-600 uppercase tracking-wider mb-2">Event Management</p>

                        <a href="{{ route('admin.events.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.events.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium">Events</span>
                        </a>

                        <a href="{{ route('admin.polls.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.polls.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="font-medium">Polls & Voting</span>
                        </a>

                        <a href="{{ route('admin.surveys.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.surveys.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="font-medium">Surveys & Forms</span>
                        </a>

                        <a href="{{ route('admin.settings.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="font-medium">Platform Settings</span>
                        </a>
                    </div>

                    <!-- Content Management Section -->
                    <div class="pt-4 mt-4 border-t border-gray-700 dark:border-gray-800">
                        <p class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-600 uppercase tracking-wider mb-2">Content Management</p>

                        <a href="{{ route('admin.shop.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.shop.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span class="font-medium">Shop Products</span>
                        </a>

                        <a href="{{ route('admin.jobs.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.jobs.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium">Job Portfolios</span>
                        </a>

                        <a href="{{ route('admin.team.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.team.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="font-medium">Team Applications</span>
                        </a>
                    </div>

                    <!-- Gallery & Magazine Section -->
                    <div class="pt-4 mt-4 border-t border-gray-700 dark:border-gray-800">
                        <p class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-600 uppercase tracking-wider mb-2">Media & Content</p>

                        <a href="{{ route('admin.gallery.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.gallery.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium">Gallery</span>
                        </a>

                        <a href="{{ route('admin.magazine.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.magazine.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="font-medium">Magazine</span>
                        </a>

                        <a href="{{ route('admin.chat.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.chat.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span class="font-medium">Chat Messages</span>
                            @php $unreadChatCount = \App\Models\ChatMessage::unread()->count(); @endphp
                            @if($unreadChatCount > 0)
                            <span class="ml-auto px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">{{ $unreadChatCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.contact.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.contact.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium">Contact Messages</span>
                            @php $unreadContactCount = \App\Models\ContactMessage::unread()->count(); @endphp
                            @if($unreadContactCount > 0)
                            <span class="ml-auto px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">{{ $unreadContactCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.shop-orders.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.shop-orders.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <span class="font-medium">Shop Orders</span>
                        </a>
                    </div>

                    <!-- SMS Management Section -->
                    <div class="pt-4 mt-4 border-t border-gray-700 dark:border-gray-800">
                        <p class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-600 uppercase tracking-wider mb-2">SMS Management</p>

                        <a href="{{ route('admin.sms.dashboard') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.dashboard') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <span class="font-medium">SMS Dashboard</span>
                        </a>

                        <a href="{{ route('admin.sms.send-single') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.send-single*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium">Send Single SMS</span>
                        </a>

                        <a href="{{ route('admin.sms.send-bulk') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.send-bulk*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="font-medium">Send Bulk SMS</span>
                        </a>

                        <a href="{{ route('admin.sms.campaigns.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.campaigns.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span class="font-medium">SMS Campaigns</span>
                        </a>

                        <a href="{{ route('admin.sms.plans.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.plans.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span class="font-medium">SMS Plans</span>
                        </a>

                        <a href="{{ route('admin.sms.sender-ids') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.sender-ids') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">Sender ID Requests</span>
                        </a>

                        <a href="{{ route('admin.sms.add-credits') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.add-credits') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span class="font-medium">Add Credits</span>
                        </a>

                        <a href="{{ route('admin.sms-contacts.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms-contacts.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="font-medium">Contacts Database</span>
                        </a>
                    </div>

                    <!-- Events & Tickets Section -->
                    <div class="pt-4 mt-4 border-t border-gray-700 dark:border-gray-800">
                        <p class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-600 uppercase tracking-wider mb-2">Events & Tickets</p>

                        <a href="{{ route('admin.complementary-tickets.index') }}"
                           class="flex items-center p-3 text-gray-900 dark:text-white rounded-xl transition-all duration-200 {{ request()->routeIs('admin.complementary-tickets.*') ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg border border-white/30 dark:border-gray-700/40' : 'bg-white/25 dark:bg-gray-800/35 hover:bg-white/35 dark:hover:bg-gray-800/50 border border-white/20 dark:border-gray-700/30' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            <span class="font-medium">Complementary Tickets</span>
                        </a>
                    </div>

                    <!-- Logout Button -->
                    <div class="pt-4 mt-4 border-t border-gray-700 dark:border-gray-800 pb-4">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center p-3 text-gray-900 dark:text-gray-300 rounded-xl hover:bg-red-600 hover:text-white transition-all duration-200 bg-white/25 dark:bg-gray-800/35 border border-white/20 dark:border-gray-700/30">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="sidebarOpen ? 'lg:ml-64 md:ml-56 ml-0' : 'ml-0'" class="transition-all duration-300">
            <!-- Top Navigation with LIQUID GLASS EFFECT -->
            <nav class="glass-header-transparent sticky top-0 z-50 shadow-lg transition-all duration-300">
                <div class="px-2 sm:px-4 py-3 sm:py-4">
                    <div class="flex items-center justify-between">
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="p-1.5 sm:p-2 text-white hover:text-cyan-300 rounded-lg hover:bg-white/20 transition-all duration-200 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div class="flex items-center gap-2 sm:gap-3">
                            <!-- Dark Mode Toggle -->
                            <button @click="darkMode = !darkMode"
                                    class="p-1.5 sm:p-2 rounded-lg text-white hover:text-cyan-300 hover:bg-white/20 transition-all flex-shrink-0">
                                <svg x-show="!darkMode" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <svg x-show="darkMode" x-cloak class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </button>

                            <!-- Admin Profile -->
                            <div class="relative flex-shrink-0" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="flex items-center gap-1 sm:gap-2 p-1.5 sm:p-2 rounded-xl text-white hover:text-cyan-300 hover:bg-white/20 transition-all duration-200">
                                    <div class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-xs sm:text-sm">
                                        {{ substr(auth()->guard('admin')->user()->name, 0, 1) }}
                                    </div>
                                    <div class="hidden md:block text-left">
                                        <p class="text-xs sm:text-sm font-semibold text-white truncate max-w-[120px]">{{ auth()->guard('admin')->user()->name }}</p>
                                        <p class="text-xs text-cyan-300">{{ ucfirst(auth()->guard('admin')->user()->role) }}</p>
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
                                     class="absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 z-50">
                                    <div class="p-3">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Logged in as</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->guard('admin')->user()->email }}</p>
                                    </div>
                                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full text-left px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-b-xl transition-colors duration-200 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content Area -->
            <main class="p-3 sm:p-4 md:p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-xl shadow-md">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-500 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl shadow-md">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-red-500 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
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
</body>
</html>
