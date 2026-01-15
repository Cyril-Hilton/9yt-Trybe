<!DOCTYPE html>
<html lang="en" x-data="initApp()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags -->
    <title>@yield('title', '9yt !Trybe - Discover Amazing Events & Entertainment')</title>
    <meta name="title" content="@yield('meta_title', '9yt !Trybe - Discover Amazing Events & Entertainment')">
    <meta name="description" content="@yield('meta_description', 'Discover and book tickets for the best events, concerts, and entertainment experiences. Join the !Trybe community and never miss out on amazing experiences.')">
    <meta name="keywords" content="@yield('meta_keywords', 'events, concerts, tickets, entertainment, book tickets, event discovery, !Trybe, 9yt, conferences, festivals, shows')">
    <meta name="author" content="9yt !Trybe">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', '9yt !Trybe - Discover Amazing Events')">
    <meta property="og:description" content="@yield('og_description', 'Discover and book tickets for the best events, conferences, and entertainment experiences.')">
    <meta property="og:image" content="@yield('og_image', asset('ui/logo/9yt-trybe-logo-light.png'))">
    <meta property="og:site_name" content="9yt !Trybe">
    <meta property="og:locale" content="en_US">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('twitter_title', '9yt !Trybe - Discover Amazing Events')">
    <meta property="twitter:description" content="@yield('twitter_description', 'Discover and book tickets for the best events, conferences, and entertainment experiences.')">
    <meta property="twitter:image" content="@yield('twitter_image', asset('ui/logo/9yt-trybe-logo-light.png'))">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}">

    <!-- Additional SEO Meta Tags -->
    <meta name="theme-color" content="#0891b2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    <!-- CRITICAL: Set dark mode BEFORE any rendering to prevent flash -->
    <script>
        (function() {
            // Check localStorage for dark mode preference
            var darkMode = localStorage.getItem('darkMode');
            // Default to dark mode if not set
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/glassmorphism.css') }}?v={{ time() }}">
    <style>
        [x-cloak] { display: none !important; }

        /* Glassmorphism Loader - using component, no custom styles needed here */

        /* Futuristic Animated Background */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animated-gradient-bg {
            background: linear-gradient(-45deg, #1e40af 0%, #0891b2 25%, #06b6d4 50%, #3b82f6 75%, #1e40af 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        /* Subtle Glow Effect - Midnight theme */
        .neon-glow {
            text-shadow: 0 0 8px rgba(6, 182, 212, 0.3);
        }

        .neon-border {
            box-shadow: 0 0 4px rgba(6, 182, 212, 0.3),
                        0 0 8px rgba(8, 145, 178, 0.2);
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
            background: rgba(0, 0, 0, 0.1) !important;
            backdrop-filter: blur(30px) saturate(180%) brightness(130%) !important;
            -webkit-backdrop-filter: blur(30px) saturate(180%) brightness(130%) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        }

        /* Liquid Glass Morphism - iOS 26 Style */
        /* Light Mode - Crisp frosted white glass */
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(24px) saturate(200%) brightness(105%);
            -webkit-backdrop-filter: blur(24px) saturate(200%) brightness(105%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.5);
        }

        /* Dark Mode - Deep frosted dark glass */
        .dark .glass-effect {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(24px) saturate(200%) brightness(95%);
            -webkit-backdrop-filter: blur(24px) saturate(200%) brightness(95%);
            border: 1px solid rgba(148, 163, 184, 0.15);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.05);
        }

        /* GLASS BUTTONS - Shiny glass effect */
        .glass-btn {
            background: rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(20px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            color: #ffffff !important;
            box-shadow: 0 8px 32px rgba(255, 255, 255, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.5) !important;
        }

        .dark .glass-btn {
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            color: #ffffff !important;
            box-shadow: 0 8px 32px rgba(255, 255, 255, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        }

        .glass-btn:hover {
            background: rgba(255, 255, 255, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            transform: translateY(-2px) scale(1.02) !important;
            box-shadow: 0 12px 40px rgba(255, 255, 255, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.7) !important;
        }

        .dark .glass-btn:hover {
            background: rgba(255, 255, 255, 0.25) !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            box-shadow: 0 12px 40px rgba(255, 255, 255, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.4) !important;
        }

        /* Header text colors - white on gradient background */
        nav.glass-header-transparent a:not(.glass-btn),
        nav.glass-header-transparent button:not(.glass-btn),
        nav.glass-header-transparent span {
            color: #ffffff !important;
        }

        nav.glass-header-transparent a:not(.glass-btn):hover,
        nav.glass-header-transparent button:not(.glass-btn):hover {
            color: #22d3ee !important;
        }

        /* Glass Dropdown - For menus and popovers */
        /* Light Mode - Elevated white glass panel */
        .glass-dropdown {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px) saturate(200%) brightness(108%);
            -webkit-backdrop-filter: blur(20px) saturate(200%) brightness(108%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 40px 0 rgba(31, 38, 135, 0.18),
                        0 2px 8px 0 rgba(0, 0, 0, 0.08),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.6);
        }

        /* Dark Mode - Elevated dark glass panel with MORE transparency for visible blur */
        .dark .glass-dropdown {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(32px) saturate(180%) brightness(90%);
            -webkit-backdrop-filter: blur(32px) saturate(180%) brightness(90%);
            border: 2px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 10px 40px 0 rgba(0, 0, 0, 0.8),
                        0 2px 8px 0 rgba(0, 0, 0, 0.5),
                        inset 0 2px 4px 0 rgba(255, 255, 255, 0.1);
        }

        /* Glass Card - For elevated content */
        /* Light Mode - Subtle frosted card */
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px) saturate(180%) brightness(105%);
            -webkit-backdrop-filter: blur(16px) saturate(180%) brightness(105%);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 6px 28px 0 rgba(31, 38, 135, 0.1),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.4);
        }

        /* Dark Mode - Subtle frosted dark card */
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.75);
            backdrop-filter: blur(16px) saturate(180%) brightness(98%);
            -webkit-backdrop-filter: blur(16px) saturate(180%) brightness(98%);
            border: 1px solid rgba(148, 163, 184, 0.15);
            box-shadow: 0 6px 28px 0 rgba(0, 0, 0, 0.3),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.06);
        }

        /* Premium Glass Card - Enhanced for premium sections */
        .glass-premium {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(24px) saturate(200%);
            -webkit-backdrop-filter: blur(24px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 10px 15px -3px rgba(0, 0, 0, 0.05),
                0 20px 25px -5px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
        }

        .dark .glass-premium {
            background: rgba(17, 24, 39, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.2),
                0 10px 15px -3px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        /* Glass Button - For CTAs */
        .glass-button {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.9) 0%, rgba(139, 92, 246, 0.9) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow:
                0 4px 15px rgba(99, 102, 241, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .glass-button:hover {
            transform: translateY(-2px);
            box-shadow:
                0 8px 25px rgba(99, 102, 241, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        /* Glass Hero Section */
        .glass-hero {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            backdrop-filter: blur(40px) saturate(150%);
            -webkit-backdrop-filter: blur(40px) saturate(150%);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .dark .glass-hero {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.4) 0%, rgba(15, 23, 42, 0.2) 100%);
            border: 1px solid rgba(148, 163, 184, 0.1);
        }

        /* Glass Input Fields */
        .glass-input {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .dark .glass-input {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .dark .glass-input:focus {
            background: rgba(30, 41, 59, 0.9);
            border-color: rgba(99, 102, 241, 0.5);
        }

        /* Glass Badge */
        .glass-badge {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .dark .glass-badge {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* Pulsing Animation - Subtle */
        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 8px rgba(6, 182, 212, 0.3);
            }
            50% {
                box-shadow: 0 0 16px rgba(6, 182, 212, 0.5);
            }
        }

        .pulse-button {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        /* Hover lift effect - Subtle */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.2);
        }

        /* Logo hover - no shadow to preserve transparency */
        .logo-hover {
            transition: transform 0.3s ease;
        }

        .logo-hover:hover {
            transform: translateY(-2px) scale(1.02);
        }

        /* Custom midnight scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #0891b2 0%, #06b6d4 100%);
            border-radius: 10px;
            border: 2px solid #1e293b;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #0e7490 0%, #0891b2 100%);
        }

        .dark::-webkit-scrollbar-track {
            background: linear-gradient(180deg, #0f172a 0%, #020617 100%);
        }

        /* Gradient text - Moonlight theme */
        .gradient-text {
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 50%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Card glow on hover - Subtle */
        .card-glow:hover {
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.25),
                        0 8px 30px rgba(15, 118, 110, 0.15);
            transform: translateY(-5px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* FORCE CORRECT HEADER TEXT COLORS */
        nav a:not(.glass-btn), nav button:not(.glass-btn), nav span.truncate {
            color: #111827 !important; /* Black in light mode */
        }

        .dark nav a:not(.glass-btn), .dark nav button:not(.glass-btn), .dark nav span.truncate {
            color: #ffffff !important; /* White in dark mode */
        }

        /* GLASS HOVER EFFECTS - NO COLORED BACKGROUNDS */
        nav a:not(.glass-btn):hover, nav button:not(.glass-btn):hover {
            background: rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
            transform: scale(1.02) !important;
        }

        .dark nav a:not(.glass-btn):hover, .dark nav button:not(.glass-btn):hover {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1) !important;
        }

        /* GLASS BUTTONS - Sign Up, Login, Create Event, etc */
        .glass-btn {
            background: rgba(6, 182, 212, 0.15) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(6, 182, 212, 0.3) !important;
            color: #111827 !important;
            box-shadow: 0 8px 32px rgba(6, 182, 212, 0.2) !important;
        }

        .dark .glass-btn {
            background: rgba(34, 211, 238, 0.15) !important;
            border: 1px solid rgba(34, 211, 238, 0.3) !important;
            color: #ffffff !important;
            box-shadow: 0 8px 32px rgba(34, 211, 238, 0.2) !important;
        }

        .glass-btn:hover {
            background: rgba(6, 182, 212, 0.25) !important;
            border: 1px solid rgba(6, 182, 212, 0.5) !important;
            transform: translateY(-2px) scale(1.02) !important;
            box-shadow: 0 12px 40px rgba(6, 182, 212, 0.3) !important;
        }

        .dark .glass-btn:hover {
            background: rgba(34, 211, 238, 0.25) !important;
            border: 1px solid rgba(34, 211, 238, 0.5) !important;
            box-shadow: 0 12px 40px rgba(34, 211, 238, 0.3) !important;
        }

        /* ULTRA INTENSE LIQUID GLASS HEADER - iOS 26 STYLE */
        nav.glass-effect {
            background: rgba(255, 255, 255, 0.3) !important;
            backdrop-filter: blur(50px) saturate(300%) brightness(110%) !important;
            -webkit-backdrop-filter: blur(50px) saturate(300%) brightness(110%) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.4) !important;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15),
                        inset 0 1px 0 rgba(255, 255, 255, 0.6) !important;
        }

        .dark nav.glass-effect {
            background: rgba(0, 0, 0, 0.3) !important;
            backdrop-filter: blur(50px) saturate(300%) brightness(105%) !important;
            -webkit-backdrop-filter: blur(50px) saturate(300%) brightness(105%) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.8),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        }

        /* GLASS ALL INPUTS, CARDS, DROPDOWNS - iOS 26 STYLE */
        .glass-dropdown, .glass-card, .glass-premium-card, .glass-input,
        input[type="text"], input[type="email"], input[type="password"],
        input[type="search"], textarea, select {
            backdrop-filter: blur(30px) saturate(250%) !important;
            -webkit-backdrop-filter: blur(30px) saturate(250%) !important;
        }

        .dark .glass-dropdown, .dark .glass-card, .dark .glass-premium-card,
        .dark input[type="text"], .dark input[type="email"], .dark input[type="password"],
        .dark input[type="search"], .dark textarea, .dark select {
            backdrop-filter: blur(40px) saturate(280%) !important;
            -webkit-backdrop-filter: blur(40px) saturate(280%) !important;
        }

        /* RESPONSIVE HEADER */
        @media (max-width: 768px) {
            nav .max-w-7xl {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
                max-width: 100% !important;
                margin: 0 !important;
            }

            nav .h-20 {
                height: 4rem !important;
            }

            nav .flex.items-center {
                gap: 0.5rem !important;
            }

            nav .flex-shrink-0 img {
                height: 2.5rem !important;
            }
        }

        @media (max-width: 640px) {
            nav .max-w-7xl {
                padding-left: 0.25rem !important;
                padding-right: 0.25rem !important;
            }

            nav .glass-btn {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
                font-size: 0.875rem !important;
            }
        }

        @media (max-width: 640px) {
            nav .max-w-7xl {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        .leaflet-container {
            z-index: 1;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-white dark:bg-black transition-colors duration-300">
    <!-- Page Loader with Logo -->
    @include('components.logo-loader', ['id' => 'page-loader', 'text' => 'Loading the !Trybe Community...'])

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

    <!-- Skip to Content Link for Accessibility -->
    <a href="#main-content" class="skip-to-content">Skip to main content</a>

    <!-- Header - Sticky Navigation with Glass Effect -->
    <nav class="sticky top-0 z-50 animated-gradient-bg glass-header-transparent shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="/" class="flex items-center logo-hover">
                        <img x-show="!darkMode" src="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}" alt="9yt !Trybe" class="h-14 w-auto">
                        <img x-show="darkMode" x-cloak src="{{ asset('ui/logo/9yt-trybe-logo-dark.png') }}" alt="9yt !Trybe" class="h-14 w-auto">
                    </a>
                </div>

                <!-- Desktop Navigation - Better Spacing -->
                <div class="hidden lg:flex items-center space-x-3 xl:space-x-4 flex-1 justify-center ml-4 xl:ml-6">
                    <a href="{{ route('events.index') }}" class="relative px-3 py-2 text-sm xl:text-base text-white font-semibold transition-all duration-300 rounded-lg hover:bg-white/20 hover:backdrop-blur-lg whitespace-nowrap">
                        Events
                    </a>
                    <a href="{{ route('shop.index') }}" class="relative px-3 py-2 text-sm xl:text-base text-white font-semibold transition-all duration-300 rounded-lg hover:bg-white/20 hover:backdrop-blur-lg whitespace-nowrap">
                        Shop
                    </a>
                    <a href="{{ route('gallery.index') }}" class="relative px-3 py-2 text-sm xl:text-base text-white font-semibold transition-all duration-300 rounded-lg hover:bg-white/20 hover:backdrop-blur-lg whitespace-nowrap">
                        Gallery
                    </a>
                    <a href="{{ route('user.sms.dashboard') }}" class="relative px-3 py-2 text-sm xl:text-base text-white font-semibold transition-all duration-300 rounded-lg hover:bg-white/20 hover:backdrop-blur-lg whitespace-nowrap">
                        Bulk SMS
                    </a>
                    <a href="{{ route('jobs.index') }}" class="relative px-3 py-2 text-sm xl:text-base text-white font-semibold transition-all duration-300 rounded-lg hover:bg-white/20 hover:backdrop-blur-lg whitespace-nowrap">
                        Jobs
                    </a>
                    <a href="{{ route('team.index') }}" class="relative px-3 py-2 text-sm xl:text-base text-white font-semibold transition-all duration-300 rounded-lg hover:bg-white/20 hover:backdrop-blur-lg whitespace-nowrap">
                        Team
                    </a>
                    <a href="{{ route('about') }}" class="relative px-3 py-2 text-sm xl:text-base text-white font-semibold transition-all duration-300 rounded-lg hover:bg-white/20 hover:backdrop-blur-lg whitespace-nowrap">
                        About
                    </a>
                    <a href="{{ route('contact') }}" class="relative px-3 py-2 text-sm xl:text-base text-white font-semibold transition-all duration-300 rounded-lg hover:bg-white/20 hover:backdrop-blur-lg whitespace-nowrap">
                        Contact
                    </a>
                </div>

                <!-- Right Section: Auth/Guest + Dark Mode + Mobile Menu -->
                <div class="flex items-center">
                    @auth('company')
                    <div class="flex items-center space-x-4">
                        <!-- Global Search Icon with Live Autocomplete -->
                        <div x-data="{ searchOpen: false, query: '', suggestions: [], loading: false, error: '' }" class="relative">
                            <button @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())"
                                    class="text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 transition">
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
                                            placeholder="Search everything..."
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
                                                    <span class="px-2 py-1 text-xs rounded-lg flex-shrink-0"
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

                        <!-- Cart Icon for Company Users -->
                        <a href="{{ route('shop.cart') }}" class="relative text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 transition" aria-label="Shopping cart">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            @php
                                $cartCount = \App\Models\CartItem::where('company_id', auth('company')->id())->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">{{ $cartCount }}</span>
                            @endif
                        </a>

                        <!-- Organization Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 font-medium transition max-w-xs" aria-label="Organization menu" :aria-expanded="open">
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="truncate max-w-[120px]">{{ auth('company')->user()->name }}</span>
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-64 glass-dropdown rounded-xl py-2 shadow-2xl" style="z-index: 9999;">
                                <div class="px-4 py-3 border-b border-white/20 dark:border-gray-700/50">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth('company')->user()->name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ auth('company')->user()->email }}</p>
                                </div>
                                <a href="{{ route('organization.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('organization.events.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    My Events
                                </a>
                                <a href="{{ route('organization.events.create') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Create Event
                                </a>
                                <a href="{{ route('organization.polls.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                    Polls & Voting
                                </a>
                                <a href="{{ route('organization.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Settings
                                </a>
                                <hr class="my-2 border-white/20 dark:border-gray-700/50">
                                <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400">
                                    Want to buy tickets?
                                </div>
                                <a href="{{ route('switch.to.user') }}" class="block px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Switch to User
                                </a>
                                <hr class="my-2 border-white/20 dark:border-gray-700/50">
                                <form method="POST" action="{{ route('organization.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth

                    @auth
                    <div class="flex items-center space-x-4">
                        <!-- Global Search Icon with Live Autocomplete -->
                        <div x-data="{ searchOpen: false, query: '', suggestions: [], loading: false, error: '' }" class="relative">
                            <button @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())"
                                    class="text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 transition">
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
                                            placeholder="Search everything..."
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
                                                    <span class="px-2 py-1 text-xs rounded-lg flex-shrink-0"
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

                        <!-- Cart Icon for Authenticated Users -->
                        <a href="{{ route('shop.cart') }}" class="relative text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            @php
                                $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">{{ $cartCount }}</span>
                            @endif
                        </a>

                        <!-- User Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 font-medium transition max-w-xs">
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-64 glass-dropdown rounded-xl py-2 shadow-2xl" style="z-index: 9999;">
                                <div class="px-4 py-3 border-b border-white/20 dark:border-gray-700/50">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('user.tickets') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                    My Tickets
                                </a>
                                <hr class="my-2 border-white/20 dark:border-gray-700/50">
                                <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400">
                                    Are you an organizer?
                                </div>
                                <a href="{{ route('switch.to.organizer') }}" class="block px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Switch to Organizer
                                </a>
                                <hr class="my-2 border-white/20 dark:border-gray-700/50">
                                <form method="POST" action="{{ route('user.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center space-x-2 sm:space-x-4 flex-shrink-0">
                        <!-- Global Search Icon with Live Autocomplete -->
                        <div x-data="{ searchOpen: false, query: '', suggestions: [], loading: false, error: '' }" class="relative">
                            <button @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())"
                                    class="text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 transition">
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
                                            placeholder="Search everything..."
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
                                                    <span class="px-2 py-1 text-xs rounded-lg flex-shrink-0"
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

                        <!-- Cart Icon for Guests -->
                        <a href="{{ route('shop.cart') }}" class="relative text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            @php
                                $cartCount = \App\Models\CartItem::where('session_id', session()->getId())->whereNull('user_id')->whereNull('company_id')->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">{{ $cartCount }}</span>
                            @endif
                        </a>

                        <!-- User Login/Register -->
                        <a href="{{ route('user.login') }}" class="hidden md:block text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 font-medium transition whitespace-nowrap flex-shrink-0">Log In</a>
                        <a href="{{ route('user.register') }}" class="glass-btn hidden md:inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium transition-all whitespace-nowrap flex-shrink-0 min-w-fit" style="white-space: nowrap !important;">Sign&nbsp;Up</a>
                    </div>
                    @endauth

                    <!-- Dark Mode Toggle -->
                    <button @click="toggleDarkMode()" class="glass-btn ml-4 p-2 rounded-lg transition-all" :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden ml-2 sm:ml-4 p-2 text-white hover:bg-white/20 rounded-lg transition" aria-label="Toggle mobile menu" :aria-expanded="mobileMenuOpen">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu with Enhanced Glass Effect -->
            <div x-show="mobileMenuOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-4"
                 @click.away="mobileMenuOpen = false"
                 class="lg:hidden absolute left-0 right-0 top-full mt-2 mx-2 sm:mx-4 rounded-xl sm:rounded-2xl glass-dropdown shadow-2xl border-2 border-white/30 dark:border-gray-700/50 overflow-hidden max-h-[calc(100vh-6rem)] overflow-y-auto"
                 style="z-index: 9999;">
                <div class="p-4 space-y-1">
                    <!-- Mobile Search - Visible in Mobile Menu -->
                    <div x-data="{ mobileQuery: '', mobileSuggestions: [], mobileLoading: false }" class="mb-3">
                        <form action="{{ route('search') }}" method="GET"
                              @submit.prevent="if(mobileQuery) window.location.href = '{{ route('search') }}?q=' + mobileQuery">
                            <div class="relative">
                                <input
                                    type="text"
                                    name="q"
                                    x-model="mobileQuery"
                                    @input.debounce.300ms="
                                        if(mobileQuery.length > 0) {
                                            mobileLoading = true;
                                            fetch('/search/quick?q=' + encodeURIComponent(mobileQuery))
                                                .then(r => r.json())
                                                .then(data => { mobileSuggestions = data.suggestions; mobileLoading = false; })
                                                .catch(() => { mobileLoading = false; });
                                        } else {
                                            mobileSuggestions = [];
                                        }
                                    "
                                    placeholder="Search everything..."
                                    class="w-full px-4 py-3 pl-10 pr-10 rounded-xl bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all border border-white/30 dark:border-gray-700/40"
                                    autocomplete="off"
                                >
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <svg x-show="mobileLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-cyan-500 animate-spin"
                                     fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>

                            <!-- Mobile Live Suggestions -->
                            <div x-show="mobileSuggestions.length > 0" class="mt-2 max-h-64 overflow-y-auto rounded-xl bg-white/40 dark:bg-gray-800/40 border border-white/30 dark:border-gray-700/40">
                                <template x-for="item in mobileSuggestions" :key="item.id + '-mobile-' + item.type">
                                    <a :href="item.url" class="block p-2 hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all">
                                        <div class="flex items-center gap-2">
                                            <template x-if="item.image">
                                                <img :src="item.image" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" loading="lazy" />
                                            </template>
                                            <template x-if="!item.image">
                                                <div class="w-10 h-10 rounded-lg flex-shrink-0 flex items-center justify-center bg-cyan-100 dark:bg-cyan-900/30">
                                                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                    </svg>
                                                </div>
                                            </template>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="item.title"></div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400 truncate" x-text="item.subtitle"></div>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </form>
                    </div>

                    <a href="{{ route('events.index') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Events</a>
                    <a href="{{ route('shop.index') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Shop</a>
                    <a href="{{ route('gallery.index') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Gallery</a>
                    <a href="{{ route('user.sms.dashboard') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Bulk SMS</a>
                    <a href="{{ route('jobs.index') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Jobs</a>
                    <a href="{{ route('team.index') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Team</a>
                    <a href="{{ route('about') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">About</a>
                    <a href="{{ route('contact') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Contact</a>

                    @auth('company')
                    <div class="border-t border-white/30 dark:border-gray-700/50 pt-3 mt-3">
                        <div class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Organization</div>
                        <a href="{{ route('organization.dashboard') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Dashboard</a>
                        <a href="{{ route('organization.events.index') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">My Events</a>
                        <a href="{{ route('organization.polls.index') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Polls & Voting</a>
                        <form method="POST" action="{{ route('organization.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-3 text-red-600 dark:text-red-400 font-medium bg-red-50/30 dark:bg-red-900/20 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-all border border-red-200/30 dark:border-red-800/30">Logout</button>
                        </form>
                    </div>
                    @elseauth
                    <div class="border-t border-white/30 dark:border-gray-700/50 pt-3 mt-3 space-y-2">
                        <div class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Account</div>
                        <a href="{{ route('user.dashboard') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Dashboard</a>
                        <a href="{{ route('user.tickets') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">My Tickets</a>
                        <form method="POST" action="{{ route('user.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-3 text-red-600 dark:text-red-400 font-medium bg-red-50/30 dark:bg-red-900/20 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-all border border-red-200/30 dark:border-red-800/30">Logout</button>
                        </form>
                    </div>
                    @else
                    <div class="border-t border-white/30 dark:border-gray-700/50 pt-3 mt-3 space-y-2">
                        <a href="{{ route('user.login') }}" class="block px-4 py-3 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Login</a>
                        <a href="{{ route('user.register') }}" class="glass-btn block px-4 py-3 rounded-xl font-bold text-center shadow-lg">Sign Up</a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" tabindex="-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-gray-950 text-white mt-16 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- About -->
                <div>
                    <h4 class="text-lg font-bold mb-4">9yt !Trybe</h4>
                    <p class="text-gray-400 text-sm">We are the heart of the party, the soul of the night, and the creators of unforgettable memories.</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('events.index') }}" class="text-gray-400 hover:text-white transition">Events</a></li>
                        <li><a href="{{ route('organizers.index') }}" class="text-gray-400 hover:text-white transition">Organizers</a></li>
                        <li><a href="{{ route('polls.index') }}" class="text-gray-400 hover:text-white transition">Polls & Voting</a></li>
                        <li><a href="{{ route('shop.index') }}" class="text-gray-400 hover:text-white transition">Shop</a></li>
                        <li><a href="{{ route('gallery.index') }}" class="text-gray-400 hover:text-white transition">Gallery</a></li>
                        <li><a href="{{ route('user.sms.dashboard') }}" class="text-gray-400 hover:text-white transition">Bulk SMS</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="text-gray-400 hover:text-white transition">Jobs</a></li>
                        <li><a href="{{ route('team.index') }}" class="text-gray-400 hover:text-white transition">Join Team</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition">About Us</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><strong class="text-white">Email:</strong><br>9yttrybe@gmail.com</li>
                        <li><strong class="text-white">Phone:</strong><br>0545566524 / 0267825223</li>
                        <li><strong class="text-white">WhatsApp:</strong><br>0267825223</li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Follow Us</h4>
                    <div class="space-y-3">
                        <a href="https://www.tiktok.com/@9yt.trybe?_r=1&_t=ZM-9191LGZJuSB" target="_blank" class="flex items-center text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/>
                            </svg>
                            @9yt.trybe
                        </a>
                        <a href="https://instagram.com/9yt.trybe" target="_blank" class="flex items-center text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                            @9yt.trybe
                        </a>
                    </div>
                </div>
            </div>

            <!-- Legal Links - Horizontal -->
            <div class="border-t border-gray-800 pt-6 pb-6">
                <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm">
                    <a href="{{ route('legal.terms') }}" class="text-gray-400 hover:text-white transition">Terms & Conditions</a>
                    <span class="text-gray-700">•</span>
                    <a href="{{ route('legal.privacy') }}" class="text-gray-400 hover:text-white transition">Privacy Policy</a>
                    <span class="text-gray-700">•</span>
                    <a href="{{ route('legal.cookies') }}" class="text-gray-400 hover:text-white transition">Cookie Policy</a>
                    <span class="text-gray-700">•</span>
                    <a href="{{ route('legal.refund') }}" class="text-gray-400 hover:text-white transition">Refund Policy</a>
                    <span class="text-gray-700">•</span>
                    <a href="{{ route('legal.disclaimer') }}" class="text-gray-400 hover:text-white transition">Disclaimer</a>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} 9yt !Trybe. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js Component -->
    <script>
        function initApp() {
            return {
                darkMode: localStorage.getItem('darkMode') !== 'false',
                mobileMenuOpen: false,

                init() {
                    // Default to dark mode on first visit
                    if (localStorage.getItem('darkMode') === null) {
                        this.darkMode = true;
                        localStorage.setItem('darkMode', 'true');
                    }
                },

                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode.toString());
                    // Force a refresh of the dark class on the HTML element
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            }
        }

        // Global form loading state component
        document.addEventListener('alpine:init', () => {
            Alpine.data('formLoading', () => ({
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

    {{-- Cookie Consent Banner --}}
    @include('components.cookie-consent')

    <!-- Floating Chatbot Widget -->
    <div x-data="{
        chatOpen: false,
        messages: [],
        newMessage: '',
        loading: false,
        guestName: '',
        guestEmail: '',
        showGuestForm: {{ auth()->check() || auth('company')->check() ? 'false' : 'true' }},
        isAuthenticated: {{ auth()->check() || auth('company')->check() ? 'true' : 'false' }},

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

            // Guest validation
            if (!this.isAuthenticated && (!this.guestName || !this.guestEmail)) {
                alert('Please enter your name and email');
                return;
            }

            this.loading = true;

            const formData = new FormData();
            formData.append('message', this.newMessage);
            if (!this.isAuthenticated) {
                formData.append('name', this.guestName);
                formData.append('email', this.guestEmail);
            }

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
                    this.showGuestForm = false; // Hide form after first message
                } else {
                    alert('Failed to send message');
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
                class="w-16 h-16 rounded-full bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                :class="{ 'scale-0': chatOpen }">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hi there! 👋</h4>
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

            <!-- Guest Info Form (if not authenticated) -->
            <div x-show="showGuestForm && !isAuthenticated" class="px-4 py-3 bg-blue-50 dark:bg-blue-900/20 border-t border-white/20 dark:border-gray-700/50">
                <p class="text-xs text-gray-700 dark:text-gray-300 mb-2">Please introduce yourself:</p>
                <input type="text" x-model="guestName" placeholder="Your name"
                       class="w-full mb-2 px-3 py-2 text-sm rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <input type="email" x-model="guestEmail" placeholder="Your email"
                       class="w-full px-3 py-2 text-sm rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-cyan-500">
            </div>

            <!-- Chat Input -->
            <form @submit.prevent="sendMessage" class="p-4 bg-white dark:bg-gray-800 border-t border-white/20 dark:border-gray-700/50">
                <div class="flex gap-2">
                    <textarea x-model="newMessage"
                              placeholder="Type your message..."
                              rows="2"
                              @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                              class="flex-1 px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 resize-none"></textarea>
                    <button type="submit"
                            :disabled="loading || !newMessage.trim()"
                            :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:scale-110'"
                            class="self-end px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-xl font-medium transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!loading" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <svg x-show="loading" class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Press Enter to send, Shift+Enter for new line</p>
            </form>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
