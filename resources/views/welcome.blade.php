<!DOCTYPE html>
<html lang="en" x-data="initApp()" x-init="init()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>9yt !Trybe - Discover Amazing Events</title>
    <link rel="icon" type="image/png" href="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}">
    <link rel="preload" as="image" href="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}">
    <link rel="preload" as="style" href="{{ asset('css/glassmorphism.css') }}?v={{ time() }}">
    <link rel="preload" as="video" href="{{ asset('ui/sliders/slide1.mp4') }}" type="video/mp4">
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

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/glassmorphism.css') }}?v={{ time() }}">
    <style>
        [x-cloak] { display: none !important; }

        /* FORCE CORRECT HEADER TEXT COLORS - OVERRIDE ALL OTHER STYLES */
        nav a:not(.glass-btn), nav button:not(.glass-btn), nav span {
            color: #111827 !important; /* Black in light mode */
        }

        .dark nav a:not(.glass-btn), .dark nav button:not(.glass-btn), .dark nav span {
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

        /* GLASS BUTTONS - Sign Up, Login, etc */
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

        /* TRANSPARENT GLASS HEADER - ULTRA CLEAR SO SLIDER SHOWS THROUGH */
        nav.glass-header-transparent {
            background: rgba(255, 255, 255, 0.08) !important;
            backdrop-filter: blur(30px) saturate(180%) brightness(120%) !important;
            -webkit-backdrop-filter: blur(30px) saturate(180%) brightness(120%) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05),
                        inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        }

        .dark nav.glass-header-transparent {
            background: rgba(0, 0, 0, 0.1) !important;
            backdrop-filter: blur(30px) saturate(180%) brightness(130%) !important;
            -webkit-backdrop-filter: blur(30px) saturate(180%) brightness(130%) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        }

        /* GLASS SEARCH INPUTS - iOS 26 STYLE */
        nav input[type="text"], nav .glass-dropdown input {
            background: rgba(255, 255, 255, 0.6) !important;
            backdrop-filter: blur(20px) saturate(200%) !important;
            -webkit-backdrop-filter: blur(20px) saturate(200%) !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
        }

        .dark nav input[type="text"], .dark nav .glass-dropdown input {
            background: rgba(255, 255, 255, 0.08) !important;
            backdrop-filter: blur(20px) saturate(200%) !important;
            -webkit-backdrop-filter: blur(20px) saturate(200%) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
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

        /* Page Loader Styles - LIQUID GLASS WITH 2CM LOGO */
        /* Glassmorphism Loader - using component, no custom styles needed here */
        .temp-placeholder {
            height: 2cm;
            animation: pulse-loader 1.5s ease-in-out infinite;
            filter: drop-shadow(0 4px 20px rgba(6, 182, 212, 0.4));
        }
        @keyframes pulse-loader {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        .loader-spinner {
            position: absolute;
            width: 2cm;
            height: 2cm;
            border: 3px solid transparent;
            border-top-color: #06b6d4;
            border-right-color: rgba(6, 182, 212, 0.3);
            border-radius: 50%;
            animation: spin-loader 1s linear infinite;
        }
        @keyframes spin-loader {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Custom scrollbar with gradient */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: linear-gradient(180deg, #1f2937, #111827);
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #0891b2, #06b6d4);
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #7c3aed, #db2777);
        }

        /* Animated gradient backgrounds */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animated-gradient-bg {
            background: linear-gradient(-45deg, #1e40af 0%, #0e7490 25%, #22d3ee 50%, #3b82f6 75%, #1e40af 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #1e40af 0%, #0e7490 50%, #22d3ee 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Neon Glow Effect */
        .neon-glow {
            text-shadow: 0 0 10px rgba(8, 145, 178, 0.8),
                         0 0 20px rgba(6, 182, 212, 0.6),
                         0 0 30px rgba(14, 116, 144, 0.4);
        }

        /* Neon Border */
        .neon-border {
            box-shadow: 0 0 5px rgba(6, 182, 212, 0.5),
                        0 0 10px rgba(6, 182, 212, 0.3),
                        inset 0 0 10px rgba(6, 182, 212, 0.1);
        }

        /* iOS 26-style Liquid Glass Effect - Light Mode */
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(24px) saturate(200%) brightness(105%);
            -webkit-backdrop-filter: blur(24px) saturate(200%) brightness(105%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.5);
        }

        /* Dark Mode Glass Effect - iOS 26 Pure Black */
        .dark .glass-effect {
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(40px) saturate(280%) brightness(105%);
            -webkit-backdrop-filter: blur(40px) saturate(280%) brightness(105%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.8),
                        0 4px 16px 0 rgba(6, 182, 212, 0.2),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.1);
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
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(32px) saturate(180%) brightness(90%);
            -webkit-backdrop-filter: blur(32px) saturate(180%) brightness(90%);
            border: 2px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 10px 40px 0 rgba(0, 0, 0, 0.8),
                        0 2px 8px 0 rgba(0, 0, 0, 0.5),
                        inset 0 2px 4px 0 rgba(255, 255, 255, 0.1);
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

        /* Hover lift effect */
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(6, 182, 212, 0.3);
        }

        /* Hide scrollbar for horizontal scroll containers */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Logo hover - no shadow to preserve transparency */
        .logo-hover {
            transition: transform 0.3s ease;
        }
        .logo-hover:hover {
            transform: translateY(-2px) scale(1.02);
        }

        /* Card glow on hover */
        .card-glow {
            transition: all 0.3s ease;
        }
        .card-glow:hover {
            box-shadow: 0 0 30px rgba(6, 182, 212, 0.4),
                        0 10px 50px rgba(8, 145, 178, 0.3);
            transform: translateY(-8px);
        }

        /* Pulse button animation */
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0.7); }
            50% { box-shadow: 0 0 0 10px rgba(6, 182, 212, 0); }
        }

        .pulse-button {
            animation: pulse 2s infinite;
        }

        /* Premium Glass Card */
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

        /* Glass Button */
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
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
            backdrop-filter: blur(40px) saturate(150%);
            -webkit-backdrop-filter: blur(40px) saturate(150%);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .glass-hero {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.5) 0%, rgba(15, 23, 42, 0.3) 100%);
            border: 1px solid rgba(148, 163, 184, 0.15);
        }

        /* Glass Feature Card */
        .glass-feature {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
            transition: all 0.3s ease;
        }

        .glass-feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.15);
        }

        .dark .glass-feature {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.15);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .dark .glass-feature:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        /* Glass Stats Card */
        .glass-stats {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .dark .glass-stats {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(139, 92, 246, 0.15) 100%);
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        /* Category Card Glass */
        .glass-category {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.3s ease;
        }

        .glass-category:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .dark .glass-category {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .dark .glass-category:hover {
            background: rgba(30, 41, 59, 0.95);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        /* ============================================
           SHEPHERD.JS TOUR GUIDE - LIQUID GLASS STYLING
           Dope, attractive, and easy to read
           ============================================ */

        .shepherd-element {
            z-index: 9999 !important;
        }

        .shepherd-modal-overlay-container {
            z-index: 9998 !important;
        }

        /* Tour Tooltip - Liquid Glass Effect */
        .shepherd-element .shepherd-content {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%) !important;
            backdrop-filter: blur(40px) saturate(200%) brightness(110%) !important;
            -webkit-backdrop-filter: blur(40px) saturate(200%) brightness(110%) !important;
            border: 2px solid rgba(6, 182, 212, 0.3) !important;
            border-radius: 20px !important;
            box-shadow: 0 20px 60px rgba(6, 182, 212, 0.2),
                        0 10px 30px rgba(99, 102, 241, 0.15),
                        0 5px 15px rgba(0, 0, 0, 0.1),
                        inset 0 2px 4px rgba(255, 255, 255, 0.8) !important;
            padding: 0 !important;
            max-width: 420px !important;
            animation: tourSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .dark .shepherd-element .shepherd-content {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 100%) !important;
            border: 2px solid rgba(34, 211, 238, 0.3) !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5),
                        0 10px 30px rgba(34, 211, 238, 0.15),
                        inset 0 2px 4px rgba(255, 255, 255, 0.1) !important;
        }

        @keyframes tourSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Tour Header */
        .shepherd-header {
            background: linear-gradient(135deg, #0891b2 0%, #6366f1 100%) !important;
            padding: 20px 24px !important;
            border-radius: 18px 18px 0 0 !important;
            border-bottom: none !important;
        }

        .shepherd-title {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            margin: 0 !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .shepherd-cancel-icon {
            color: #ffffff !important;
            opacity: 0.9 !important;
            width: 24px !important;
            height: 24px !important;
            transition: all 0.2s;
        }

        .shepherd-cancel-icon:hover {
            opacity: 1 !important;
            transform: scale(1.1) rotate(90deg);
        }

        /* Tour Body Text */
        .shepherd-text {
            padding: 24px !important;
            font-size: 1rem !important;
            line-height: 1.7 !important;
            color: #1f2937 !important;
        }

        .dark .shepherd-text {
            color: #e5e7eb !important;
        }

        .shepherd-text p {
            margin: 0 0 12px 0 !important;
        }

        .shepherd-text p:last-child {
            margin-bottom: 0 !important;
        }

        /* Tour Buttons - Glass Style */
        .shepherd-footer {
            padding: 16px 24px 24px !important;
            border-top: 1px solid rgba(6, 182, 212, 0.1) !important;
            background: transparent !important;
        }

        .shepherd-button {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.7) 0%, rgba(99, 102, 241, 0.6) 100%) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(6, 182, 212, 0.5) !important;
            color: #ffffff !important;
            padding: 12px 24px !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            cursor: pointer !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3),
                        inset 0 1px 2px rgba(255, 255, 255, 0.4) !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .shepherd-button:hover {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.9) 0%, rgba(99, 102, 241, 0.8) 100%) !important;
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4),
                        inset 0 1px 3px rgba(255, 255, 255, 0.5) !important;
        }

        .shepherd-button:active {
            transform: translateY(0) scale(0.98);
        }

        .shepherd-button-secondary {
            background: rgba(255, 255, 255, 0.2) !important;
            border: 2px solid rgba(6, 182, 212, 0.4) !important;
            color: #0891b2 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1),
                        inset 0 1px 2px rgba(255, 255, 255, 0.3) !important;
            text-shadow: none;
        }

        .dark .shepherd-button-secondary {
            color: #22d3ee !important;
            background: rgba(255, 255, 255, 0.1) !important;
        }

        .shepherd-button-secondary:hover {
            background: rgba(255, 255, 255, 0.3) !important;
            border: 2px solid rgba(6, 182, 212, 0.6) !important;
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.2),
                        inset 0 1px 3px rgba(255, 255, 255, 0.4) !important;
        }

        .dark .shepherd-button-secondary:hover {
            background: rgba(255, 255, 255, 0.2) !important;
        }

        /* Tour Arrow - Glass Style */
        .shepherd-arrow {
            display: none !important;
        }

        .shepherd-element[data-popper-placement^="top"] .shepherd-arrow::before,
        .shepherd-element[data-popper-placement^="bottom"] .shepherd-arrow::before,
        .shepherd-element[data-popper-placement^="left"] .shepherd-arrow::before,
        .shepherd-element[data-popper-placement^="right"] .shepherd-arrow::before {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%) !important;
            border: 2px solid rgba(6, 182, 212, 0.3) !important;
        }

        /* Progress Indicator */
        .shepherd-progress {
            padding: 0 24px 16px !important;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
        }

        .dark .shepherd-progress {
            color: #9ca3af;
        }

        /* Highlight/Target Animation */
        .shepherd-target {
            animation: targetPulse 2s ease-in-out infinite;
        }

        @keyframes targetPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(6, 182, 212, 0.7);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(6, 182, 212, 0);
            }
        }

        /* Modal Overlay - Softer */
        .shepherd-modal-overlay-container.shepherd-modal-is-visible {
            opacity: 1 !important;
        }

        .shepherd-modal-overlay-container.shepherd-modal-is-visible path {
            fill: rgba(0, 0, 0, 0.5) !important;
        }

        .dark .shepherd-modal-overlay-container.shepherd-modal-is-visible path {
            fill: rgba(0, 0, 0, 0.7) !important;
        }

        /* Responsive Tour Tooltips */
        @media (max-width: 640px) {
            .shepherd-element .shepherd-content {
                max-width: 90vw !important;
                margin: 0 16px !important;
            }

            .shepherd-header {
                padding: 16px 20px !important;
            }

            .shepherd-title {
                font-size: 1.1rem !important;
            }

            .shepherd-text {
                padding: 20px !important;
                font-size: 0.95rem !important;
            }

            .shepherd-footer {
                padding: 12px 20px 20px !important;
                flex-direction: column !important;
            }

            .shepherd-button {
                width: 100% !important;
                margin: 4px 0 !important;
            }
        }
    </style>

    <!-- Shepherd.js for Product Tours -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@11.2.0/dist/css/shepherd.css"/>
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@11.2.0/dist/js/shepherd.min.js"></script>
</head>
<body class="bg-white dark:bg-black min-h-screen transition-colors duration-300" x-data="{ mobileMenuOpen: false }">
    <!-- Page Loader with Glassmorphism Logo -->
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
            }, 200);
        });
    </script>

    <!-- Header - Sticky Navigation -->
    <nav class="sticky top-0 z-50 glass-header-transparent shadow-lg transition-all duration-300">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="/" class="flex items-center logo-hover">
                        <img x-show="!darkMode" src="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}" alt="9yt !Trybe" class="h-10 sm:h-12 md:h-14 w-auto">
                        <img x-show="darkMode" x-cloak src="{{ asset('ui/logo/9yt-trybe-logo-dark.png') }}" alt="9yt !Trybe" class="h-10 sm:h-12 md:h-14 w-auto">
                    </a>
                </div>

                <!-- Desktop Navigation with Glass Hover Effects -->
                <div class="hidden md:flex items-center space-x-6 lg:space-x-8 flex-1 justify-center ml-8 lg:ml-12">
                    <a href="{{ route('events.index') }}" class="relative px-3 py-2 text-white font-semibold transition-all duration-300 rounded-lg whitespace-nowrap hover:bg-white/20 hover:backdrop-blur-lg">Events</a>
                    <a href="{{ route('shop.index') }}" class="relative px-3 py-2 text-white font-semibold transition-all duration-300 rounded-lg whitespace-nowrap hover:bg-white/20 hover:backdrop-blur-lg">Shop</a>
                    <a href="{{ route('gallery.index') }}" class="relative px-3 py-2 text-white font-semibold transition-all duration-300 rounded-lg whitespace-nowrap hover:bg-white/20 hover:backdrop-blur-lg">Gallery</a>
                    <a href="{{ Auth::guard('company')->check() ? route('organization.sms.dashboard') : route('user.sms.dashboard') }}" class="relative px-3 py-2 text-white font-semibold transition-all duration-300 rounded-lg whitespace-nowrap hover:bg-white/20 hover:backdrop-blur-lg">Bulk SMS</a>
                    <a href="{{ route('jobs.index') }}" class="relative px-3 py-2 text-white font-semibold transition-all duration-300 rounded-lg whitespace-nowrap hover:bg-white/20 hover:backdrop-blur-lg">Jobs</a>
                    <a href="{{ route('team.index') }}" class="relative px-3 py-2 text-white font-semibold transition-all duration-300 rounded-lg whitespace-nowrap hover:bg-white/20 hover:backdrop-blur-lg">Team</a>
                    <a href="{{ route('about') }}" class="relative px-3 py-2 text-white font-semibold transition-all duration-300 rounded-lg whitespace-nowrap hover:bg-white/20 hover:backdrop-blur-lg">About</a>
                    <a href="{{ route('contact') }}" class="relative px-3 py-2 text-white font-semibold transition-all duration-300 rounded-lg whitespace-nowrap hover:bg-white/20 hover:backdrop-blur-lg">Contact</a>
                </div>

                <!-- Right Section: Search + Auth/Guest + Dark Mode + Mobile Menu -->
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    @if(Auth::check())
                    <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                        <!-- Global Search Icon with Live Autocomplete -->
                        <div x-data="{ searchOpen: false, query: '', suggestions: [], loading: false, error: '' }" class="relative">
                            <button x-on:click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())" class="text-white hover:text-cyan-300 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                            <!-- Search Dropdown with Live Suggestions -->
                            <div x-show="searchOpen" x-on:click.away="searchOpen = false" x-cloak
                                 class="absolute right-0 mt-3 w-80 sm:w-96 max-w-[90vw] glass-dropdown rounded-2xl p-3 sm:p-4 z-50 shadow-2xl">
                                <form action="{{ route('search') }}" method="GET" x-on:submit.prevent="if(query) window.location.href = '{{ route('search') }}?q=' + query">
                                    <div class="relative">
                                        <input
                                            x-ref="searchInput"
                                            type="text"
                                            name="q"
                                            x-model="query"
                                            x-on:input.debounce.300ms="
                                                error = '';
                                                if(query.length > 0) {
                                                    loading = true;
                                                    const controller = new AbortController();
                                                    const timeoutId = setTimeout(() => controller.abort(), 20000);
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
                                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <svg x-show="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-cyan-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>

                                    <!-- Live Suggestions -->
                                    <div x-show="suggestions.length > 0" class="mt-3 max-h-96 overflow-y-auto">
                                        <template x-for="item in suggestions" :key="item.id">
                                            <a :href="item.url" class="block p-3 rounded-xl hover:bg-white/30 dark:hover:bg-gray-800/30 transition-all mb-1">
                                                <div class="flex items-center gap-3">
                                                    <img x-show="item.image" :src="item.image" class="w-12 h-12 rounded-lg object-cover" loading="lazy" />
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-semibold text-sm text-gray-900 dark:text-white truncate" x-text="item.title"></div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400" x-text="item.subtitle"></div>
                                                    </div>
                                                    <span class="px-2 py-1 text-xs rounded-lg"
                                                          :class="{
                                                              'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300': item.type === 'event',
                                                              'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': item.type === 'organizer',
                                                              'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300': item.type === 'category'
                                                          }"
                                                          x-text="item.type"></span>
                                                </div>
                                            </a>
                                        </template>
                                    </div>

                                    <div x-show="error" class="mt-3 text-center text-sm text-red-500 py-4" x-text="error"></div>

                                    <div x-show="query && suggestions.length === 0 && !loading && !error" class="mt-3 text-center text-sm text-gray-500 dark:text-gray-400 py-4">
                                        No results found. Press Enter to see all results.
                                    </div>

                                    <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                        Start typing to see suggestions • Press Enter to search
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Cart Icon - Hidden on small mobile -->
                        <a href="{{ route('shop.cart') }}" class="hidden sm:block relative text-white hover:text-cyan-300 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </a>

                        <!-- User Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button x-on:click="open = !open" class="flex items-center gap-1 sm:gap-2 text-white hover:text-cyan-300 font-medium transition whitespace-nowrap">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="hidden lg:inline truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-on:click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-64 glass-dropdown rounded-xl py-2 z-50 shadow-2xl">
                                <div class="px-4 py-3 border-b border-white/20 dark:border-gray-700/50">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('user.dashboard') }}" class="block mx-2 my-1 px-3 py-2 text-sm text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-lg transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('user.tickets') }}" class="block mx-2 my-1 px-3 py-2 text-sm text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-lg transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                    My Tickets
                                </a>
                                <hr class="my-2 border-white/20 dark:border-gray-700/50">
                                <div class="px-4 py-1 text-xs text-gray-500 dark:text-gray-400">
                                    Are you an organizer?
                                </div>
                                <a href="{{ route('switch.to.organizer') }}" class="block mx-2 my-1 px-3 py-2 text-sm text-indigo-600 dark:text-indigo-400 font-medium bg-indigo-50/30 dark:bg-indigo-900/20 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-all border border-indigo-200/30 dark:border-indigo-800/30">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Switch to Organizer
                                </a>
                                <hr class="my-2 border-white/20 dark:border-gray-700/50">
                                <form method="POST" action="{{ route('user.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full mx-2 my-1 px-3 py-2 text-left text-sm text-red-600 dark:text-red-400 font-medium bg-red-50/30 dark:bg-red-900/20 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-all border border-red-200/30 dark:border-red-800/30">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @elseif(Auth::guard('company')->check())
                    <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                        <!-- Global Search Icon with Live Autocomplete -->
                        <div x-data="{ searchOpen: false, query: '', suggestions: [], loading: false, error: '' }" class="relative">
                            <button x-on:click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())" class="text-white hover:text-cyan-300 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                            <!-- Search Dropdown with Live Suggestions -->
                            <div x-show="searchOpen" x-on:click.away="searchOpen = false" x-cloak
                                 class="absolute right-0 mt-3 w-80 sm:w-96 max-w-[90vw] glass-dropdown rounded-2xl p-3 sm:p-4 z-50 shadow-2xl">
                                <form action="{{ route('search') }}" method="GET" x-on:submit.prevent="if(query) window.location.href = '{{ route('search') }}?q=' + query">
                                    <div class="relative">
                                        <input
                                            x-ref="searchInput"
                                            type="text"
                                            name="q"
                                            x-model="query"
                                            x-on:input.debounce.300ms="
                                                error = '';
                                                if(query.length > 0) {
                                                    loading = true;
                                                    const controller = new AbortController();
                                                    const timeoutId = setTimeout(() => controller.abort(), 20000);
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
                                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <svg x-show="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-cyan-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>

                                    <!-- Live Suggestions -->
                                    <div x-show="suggestions.length > 0" class="mt-3 max-h-96 overflow-y-auto">
                                        <template x-for="item in suggestions" :key="item.id">
                                            <a :href="item.url" class="block p-3 rounded-xl hover:bg-white/30 dark:hover:bg-gray-800/30 transition-all mb-1">
                                                <div class="flex items-center gap-3">
                                                    <img x-show="item.image" :src="item.image" class="w-12 h-12 rounded-lg object-cover" loading="lazy" />
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-semibold text-sm text-gray-900 dark:text-white truncate" x-text="item.title"></div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400" x-text="item.subtitle"></div>
                                                    </div>
                                                    <span class="px-2 py-1 text-xs rounded-lg"
                                                          :class="{
                                                              'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300': item.type === 'event',
                                                              'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': item.type === 'organizer',
                                                              'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300': item.type === 'category'
                                                          }"
                                                          x-text="item.type"></span>
                                                </div>
                                            </a>
                                        </template>
                                    </div>

                                    <div x-show="error" class="mt-3 text-center text-sm text-red-500 py-4" x-text="error"></div>

                                    <div x-show="query && suggestions.length === 0 && !loading && !error" class="mt-3 text-center text-sm text-gray-500 dark:text-gray-400 py-4">
                                        No results found. Press Enter to see all results.
                                    </div>

                                    <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                        Start typing to see suggestions • Press Enter to search
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Cart Icon - Hidden on small mobile -->
                        <a href="{{ route('shop.cart') }}" class="hidden sm:block relative text-white hover:text-cyan-300 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </a>

                        <!-- Organization Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button x-on:click="open = !open" class="flex items-center gap-1 sm:gap-2 text-white hover:text-cyan-300 font-medium transition whitespace-nowrap">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="hidden lg:inline truncate max-w-[120px]">{{ auth('company')->user()->name }}</span>
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-on:click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-64 glass-dropdown rounded-xl py-2 z-50 shadow-2xl">
                                <div class="px-4 py-3 border-b border-white/20 dark:border-gray-700/50">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth('company')->user()->name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ auth('company')->user()->email }}</p>
                                </div>
                                <a href="{{ route('organization.dashboard') }}" class="block mx-2 my-1 px-3 py-2 text-sm text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-lg transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('organization.events.index') }}" class="block mx-2 my-1 px-3 py-2 text-sm text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-lg transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    My Events
                                </a>
                                <a href="{{ route('organization.events.create') }}" class="block mx-2 my-1 px-3 py-2 text-sm text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-lg transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Create Event
                                </a>
                                <a href="{{ route('organization.profile.edit') }}" class="block mx-2 my-1 px-3 py-2 text-sm text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-lg transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Settings
                                </a>
                                <a href="{{ route('switch.to.user') }}" class="block mx-2 my-1 px-3 py-2 text-sm text-indigo-600 dark:text-indigo-400 font-medium bg-indigo-50/30 dark:bg-indigo-900/20 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-all border border-indigo-200/30 dark:border-indigo-800/30">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Switch to User
                                </a>
                                <hr class="my-2 border-white/20 dark:border-gray-700/50">
                                <form method="POST" action="{{ route('organization.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full mx-2 my-1 px-3 py-2 text-left text-sm text-red-600 dark:text-red-400 font-medium bg-red-50/30 dark:bg-red-900/20 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-all border border-red-200/30 dark:border-red-800/30">
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
                    <!-- Guest User Login/Signup -->
                    <div class="hidden md:flex items-center space-x-2 sm:space-x-4 flex-shrink-0">
                        <!-- Global Search Icon with Live Autocomplete -->
                        <div x-data="{ searchOpen: false, query: '', suggestions: [], loading: false, error: '' }" class="relative">
                            <button x-on:click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())" class="text-white hover:text-cyan-300 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                            <!-- Search Dropdown with Live Suggestions -->
                            <div x-show="searchOpen" x-on:click.away="searchOpen = false" x-cloak
                                 class="absolute right-0 mt-3 w-80 sm:w-96 max-w-[90vw] glass-dropdown rounded-2xl p-3 sm:p-4 z-50 shadow-2xl">
                                <form action="{{ route('search') }}" method="GET" x-on:submit.prevent="if(query) window.location.href = '{{ route('search') }}?q=' + query">
                                    <div class="relative">
                                        <input
                                            x-ref="searchInput"
                                            type="text"
                                            name="q"
                                            x-model="query"
                                            x-on:input.debounce.300ms="
                                                error = '';
                                                if(query.length > 0) {
                                                    loading = true;
                                                    const controller = new AbortController();
                                                    const timeoutId = setTimeout(() => controller.abort(), 20000);
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
                                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <svg x-show="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-cyan-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>

                                    <!-- Live Suggestions -->
                                    <div x-show="suggestions.length > 0" class="mt-3 max-h-96 overflow-y-auto">
                                        <template x-for="item in suggestions" :key="item.id">
                                            <a :href="item.url" class="block p-3 rounded-xl hover:bg-white/30 dark:hover:bg-gray-800/30 transition-all mb-1">
                                                <div class="flex items-center gap-3">
                                                    <img x-show="item.image" :src="item.image" class="w-12 h-12 rounded-lg object-cover" loading="lazy" />
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-semibold text-sm text-gray-900 dark:text-white truncate" x-text="item.title"></div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400" x-text="item.subtitle"></div>
                                                    </div>
                                                    <span class="px-2 py-1 text-xs rounded-lg"
                                                          :class="{
                                                              'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300': item.type === 'event',
                                                              'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': item.type === 'organizer',
                                                              'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300': item.type === 'category'
                                                          }"
                                                          x-text="item.type"></span>
                                                </div>
                                            </a>
                                        </template>
                                    </div>

                                    <div x-show="error" class="mt-3 text-center text-sm text-red-500 py-4" x-text="error"></div>

                                    <div x-show="query && suggestions.length === 0 && !loading && !error" class="mt-3 text-center text-sm text-gray-500 dark:text-gray-400 py-4">
                                        No results found. Press Enter to see all results.
                                    </div>

                                    <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                        Start typing to see suggestions • Press Enter to search
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Cart Icon -->
                        <a href="{{ route('shop.cart') }}" class="relative text-white hover:text-cyan-300 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </a>

                        <a href="{{ route('user.login') }}" class="px-4 py-2 text-white hover:text-cyan-300 font-medium transition whitespace-nowrap flex-shrink-0">Login</a>
                        <a href="{{ route('user.register') }}" class="glass-btn px-6 py-2 rounded-lg font-semibold transition-all shadow-lg whitespace-nowrap flex-shrink-0" style="white-space: nowrap !important;">Sign&nbsp;Up</a>
                    </div>
                    @endif

                    <!-- Dark Mode Toggle -->
                    <button x-on:click="toggleDarkMode()" class="glass-btn p-2 rounded-lg transition-all flex-shrink-0">
                        <svg x-show="!darkMode" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="darkMode" x-cloak class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>

                    <!-- Mobile Menu Button -->
                    <button x-on:click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-white hover:bg-white/20 rounded-lg transition flex-shrink-0">
                        <svg x-show="!mobileMenuOpen" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu with Enhanced Glass Effect -->
        <div x-show="mobileMenuOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             x-on:click.away="mobileMenuOpen = false"
             class="md:hidden glass-dropdown border-t-2 border-white/30 dark:border-gray-700/50 shadow-2xl">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('events.index') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Events</a>
                <a href="{{ route('shop.index') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Shop</a>
                <a href="{{ route('gallery.index') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Gallery</a>
                <a href="{{ Auth::guard('company')->check() ? route('organization.sms.dashboard') : route('user.sms.dashboard') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Bulk SMS</a>
                <a href="{{ route('jobs.index') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Jobs</a>
                <a href="{{ route('team.index') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Team</a>
                <a href="{{ route('about') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">About</a>
                <a href="{{ route('contact') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Contact</a>
                @if(Auth::check())
                <div class="border-t border-white/30 dark:border-gray-700/50 pt-2 mt-2">
                    <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Dashboard</a>
                    <a href="{{ route('user.tickets') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">My Tickets</a>
                    <form method="POST" action="{{ route('user.logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 dark:text-red-400 font-medium bg-red-50/30 dark:bg-red-900/20 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-all border border-red-200/30 dark:border-red-800/30">Logout</button>
                    </form>
                </div>
                @elseif(Auth::guard('company')->check())
                <div class="border-t border-white/30 dark:border-gray-700/50 pt-2 mt-2">
                    <a href="{{ route('organization.dashboard') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Dashboard</a>
                    <a href="{{ route('organization.events.index') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">My Events</a>
                    <form method="POST" action="{{ route('organization.logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 dark:text-red-400 font-medium bg-red-50/30 dark:bg-red-900/20 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-all border border-red-200/30 dark:border-red-800/30">Logout</button>
                    </form>
                </div>
                @else
                <div class="border-t border-white/30 dark:border-gray-700/50 pt-2 mt-2">
                    <a href="{{ route('user.login') }}" class="block px-4 py-2 text-gray-900 dark:text-white font-medium bg-white/30 dark:bg-gray-800/40 hover:bg-white/50 dark:hover:bg-gray-800/70 rounded-xl transition-all border border-white/30 dark:border-gray-700/40 shadow-lg">Login</a>
                    <a href="{{ route('user.register') }}" class="glass-btn block px-4 py-2 mt-2 rounded-xl font-semibold text-center transition-all">Sign&nbsp;Up</a>
                </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Slider Section with Futuristic Design -->
    <section class="relative bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 dark:from-gray-900 dark:via-slate-900/20 dark:to-slate-800/20 transition-colors duration-300 -mt-20" x-data="slider()" x-init="init()">
        <div class="relative h-[300px] md:h-[400px] lg:h-[500px] overflow-hidden neon-border rounded-b-3xl">
            <!-- Slide 1 - Video -->
            <div x-show="currentSlide === 0"
                 x-transition:enter="transition ease-in-out duration-500"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in-out duration-500"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0">
                <video autoplay muted loop playsinline class="w-full h-full object-cover">
                    <source src="{{ asset('ui/sliders/slide1.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent"></div>
            </div>

            <!-- Slide 2 - Video -->
            <div x-show="currentSlide === 1"
                 x-transition:enter="transition ease-in-out duration-500"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in-out duration-500"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0">
                <video autoplay muted loop playsinline class="w-full h-full object-cover">
                    <source src="{{ asset('ui/sliders/slide2.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="absolute inset-0 bg-gradient-to-t from-pink-900/60 via-transparent to-transparent"></div>
            </div>

            <!-- Navigation Arrows with Glow -->
            <button x-on:click="prevSlide()" class="absolute left-4 top-1/2 transform -translate-y-1/2 glass-effect text-white p-3 rounded-full transition hover-lift neon-border z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button x-on:click="nextSlide()" class="absolute right-4 top-1/2 transform -translate-y-1/2 glass-effect text-white p-3 rounded-full transition hover-lift neon-border z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Dots Navigation with Glow -->
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-3 z-10">
                <button x-on:click="currentSlide = 0" :class="currentSlide === 0 ? 'bg-gradient-to-r from-cyan-500 to-cyan-400 w-8' : 'bg-white bg-opacity-50 w-3'" class="h-3 rounded-full transition-all duration-300 neon-border"></button>
                <button x-on:click="currentSlide = 1" :class="currentSlide === 1 ? 'bg-gradient-to-r from-cyan-500 to-cyan-400 w-8' : 'bg-white bg-opacity-50 w-3'" class="h-3 rounded-full transition-all duration-300 neon-border"></button>
            </div>
        </div>
    </section>

    <!-- Browsing Events Section with Gradient Background -->
    <section class="bg-gradient-to-br from-gray-50 via-slate-50/30 to-pink-50/30 dark:from-gray-900 dark:via-slate-900/10 dark:to-slate-800/10 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Section Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Browsing events in</h2>
                    <form method="GET" action="/" class="relative">
                        @if(request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                        @endif
                        <select name="region"
                                onchange="this.form.submit()"
                                class="pl-10 pr-8 py-2 border-2 border-cyan-300 dark:border-cyan-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 focus:outline-none transition-all hover-lift appearance-none">
                            <option value="">Choose a location</option>
                            @foreach($regions as $region)
                            <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                            @endforeach
                        </select>
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <svg class="absolute right-3 top-3 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </form>
                </div>

                @if(request('region'))
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ now()->format('M j, Y • g:i A') }}</p>
                @endif
            </div>

            <!-- Filters -->
            <div class="flex items-center space-x-4 border-b-2 border-cyan-200 dark:border-cyan-800">
                <a href="/{{ request('region') ? '?region='.request('region') : '' }}" class="pb-3 px-4 {{ !request('filter') ? 'border-b-4 border-gradient-to-r from-cyan-600 to-cyan-500 text-cyan-600 dark:text-cyan-400 font-bold neon-glow' : 'text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 hover:border-b-2 hover:border-cyan-400' }} transition-all duration-300">All</a>
                <a href="/?filter=today{{ request('region') ? '&region='.request('region') : '' }}" class="pb-3 px-4 {{ request('filter') == 'today' ? 'border-b-4 border-gradient-to-r from-cyan-600 to-cyan-500 text-cyan-600 dark:text-cyan-400 font-bold neon-glow' : 'text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 hover:border-b-2 hover:border-cyan-400' }} transition-all duration-300">Today</a>
                <a href="/?filter=this_weekend{{ request('region') ? '&region='.request('region') : '' }}" class="pb-3 px-4 {{ request('filter') == 'this_weekend' ? 'border-b-4 border-gradient-to-r from-cyan-600 to-cyan-500 text-cyan-600 dark:text-cyan-400 font-bold neon-glow' : 'text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 hover:border-b-2 hover:border-cyan-400' }} transition-all duration-300">This weekend</a>
            </div>

            <!-- Event Categories (PROFESSIONAL - iOS 26 Liquid Glass Style) -->
            <div class="mt-6 sm:mt-8">
                <!-- Horizontally Scrollable on Mobile, Grid on Desktop -->
                <div class="overflow-x-auto pb-2 -mx-4 sm:mx-0 sm:overflow-visible scrollbar-hide">
                    <div class="flex sm:grid sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-11 gap-3 sm:gap-4 min-w-max sm:min-w-0 px-4 sm:px-0 after:content-[''] after:block after:w-4 after:flex-shrink-0 sm:after:hidden">
                        @php
                            // Get professional categories from the database
                            $featuredCategories = \App\Models\Category::active()
                                ->whereIn('slug', ['music', 'nightlife', 'business', 'food-drink', 'performing-arts', 'dating', 'hobbies', 'holidays', 'sports-fitness', 'education'])
                                ->limit(10)
                                ->get();

                            // Fall back to a curated list when the DB does not have any active categories yet
                            if ($featuredCategories->isEmpty()) {
                                $fallbackCategories = [
                                    ['slug' => 'music', 'name' => 'Music', 'color' => 'purple', 'icon_svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>'],
                                    ['slug' => 'nightlife', 'name' => 'Nightlife', 'color' => 'indigo', 'icon_svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>'],
                                    ['slug' => 'business', 'name' => 'Business', 'color' => 'blue', 'icon_svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'],
                                    ['slug' => 'food-drink', 'name' => 'Food & Drink', 'color' => 'orange', 'icon_svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>'],
                                    ['slug' => 'performing-arts', 'name' => 'Performing Arts', 'color' => 'pink', 'icon_svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>'],
                                    ['slug' => 'dating', 'name' => 'Dating', 'color' => 'rose', 'icon_svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>'],
                                    ['slug' => 'hobbies', 'name' => 'Hobbies', 'color' => 'cyan', 'icon_svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'],
                                    ['slug' => 'holidays', 'name' => 'Holiday Events', 'color' => 'red', 'icon_svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>'],
                                    ['slug' => 'sports-fitness', 'name' => 'Sports & Fitness', 'color' => 'green', 'icon_svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'],
                                    ['slug' => 'education', 'name' => 'Education', 'color' => 'blue', 'icon_svg' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'],
                                ];

                                $featuredCategories = collect($fallbackCategories)->map(function ($item) {
                                    return \App\Models\Category::make($item);
                                });
                            }
                        @endphp

                        @foreach($featuredCategories as $category)
                            <a href="{{ route('categories.show', $category->slug) }}"
                               class="group flex-shrink-0">
                                <!-- iOS 26 Liquid Glass Card - Responsive -->
                                <div class="flex flex-col items-center w-[72px] sm:w-full p-2.5 sm:p-3 rounded-xl glass-card hover-lift transition-all duration-300 group-hover:scale-105">
                                    <!-- Icon Container -->
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-{{ $category->color }}-400/20 to-{{ $category->color }}-600/30 dark:from-{{ $category->color }}-500/30 dark:to-{{ $category->color }}-700/40 flex items-center justify-center mb-2 group-hover:shadow-lg group-hover:shadow-{{ $category->color }}-500/50 transition-all duration-300" style="backdrop-filter: blur(20px) saturate(180%);">
                                        <div class="text-{{ $category->color }}-600 dark:text-{{ $category->color }}-400 w-5 h-5 sm:w-6 sm:h-6">
                                            {!! $category->getIconHtml() !!}
                                        </div>
                                    </div>
                                    <!-- Category Name -->
                                    <span class="text-[10px] sm:text-xs font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight group-hover:text-{{ $category->color }}-600 dark:group-hover:text-{{ $category->color }}-400 transition-colors whitespace-nowrap">
                                        {{ $category->name }}
                                    </span>
                                </div>
                            </a>
                        @endforeach

                        <!-- "View All" Button -->
                        <a href="{{ route('events.index') }}" class="group flex-shrink-0">
                            <div class="flex flex-col items-center w-[72px] sm:w-full p-2.5 sm:p-3 rounded-xl glass-card hover-lift transition-all duration-300 group-hover:scale-105">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-gray-400/20 to-gray-600/30 dark:from-gray-500/30 dark:to-gray-700/40 flex items-center justify-center mb-2" style="backdrop-filter: blur(20px) saturate(180%);">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] sm:text-xs font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight whitespace-nowrap">
                                    View All
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- Scroll indicator for mobile -->
                <div class="flex justify-center mt-2 sm:hidden">
                    <div class="flex gap-1">
                        <div class="w-8 h-1 bg-cyan-500 rounded-full"></div>
                        <div class="w-2 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                        <div class="w-2 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Cards Grid -->
        @if($events->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($events as $event)
            <a href="{{ route('events.show', $event->slug) }}" class="group">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden card-glow hover-lift border-2 border-transparent hover:border-cyan-500 transition-all duration-300">
                    <!-- Event Image -->
                    <div class="relative bg-gradient-to-br from-indigo-500 to-blue-600 overflow-hidden">
                        @if($event->banner_image)
                        <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-auto" loading="lazy">
                        @else
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                    </div>

                    <!-- Event Details -->
                    <div class="p-4">
                        <!-- Date -->
                        <div class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 uppercase mb-2">
                            {{ $event->start_date->format('D, M j, g:i A') }}
                        </div>

                        <!-- Title -->
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-cyan-600 group-hover:to-cyan-500 transition">
                            {{ $event->title }}
                        </h3>

                        <!-- Location -->
                        <div class="flex items-start text-sm text-gray-600 dark:text-gray-400 mb-3">
                            @if($event->location_type === 'venue')
                                <svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <span class="line-clamp-2">{{ $event->venue_name ?? 'Venue' }}</span>
                            @elseif($event->location_type === 'online')
                                <svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                <span>Online Event</span>
                            @else
                                <svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>To Be Announced</span>
                            @endif
                        </div>

                        <!-- Price -->
                        <div class="text-sm font-semibold">
                            @if($event->hasFreeTickets())
                                <span class="text-green-600 dark:text-green-400">Free</span>
                            @elseif($event->cheapest_ticket_price > 0)
                                <span class="text-gray-900 dark:text-white">From GH₵{{ number_format($event->cheapest_ticket_price, 2) }}</span>
                            @else
                                <span class="text-gray-600 dark:text-gray-400">Price TBA</span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-16">
            <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">No events found</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Try selecting a different location or filter</p>
        </div>
        @endif

        <!-- View All Events Link -->
        @if($events->count() > 0)
        <div class="text-center mt-12">
            <a href="{{ route('events.index') }}" class="inline-block px-8 py-4 bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 hover:from-cyan-700 hover:via-cyan-600 hover:to-indigo-700 text-white text-lg font-bold rounded-xl shadow-lg hover-lift pulse-button transition-all duration-300">
                View All Events
            </a>
        </div>
        @endif
        </div>
    </section>

    <!-- News Section -->
    <section class="py-10 sm:py-14 lg:py-16 bg-white dark:bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 font-semibold">Trending News</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Fashion, Lifestyle & Entertainment</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-300 max-w-2xl">
                        Curated headlines with source attribution. Tap any story to read the original.
                    </p>
                </div>
                <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 text-cyan-600 dark:text-cyan-400 font-semibold hover:text-cyan-700 dark:hover:text-cyan-300 transition">
                    View all news
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if(!empty($newsArticles))
                @if(!empty($newsDigest))
                    <div class="mt-6 rounded-2xl border border-cyan-200 dark:border-cyan-800 bg-cyan-50/40 dark:bg-cyan-900/20 p-6">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <p class="text-xs uppercase tracking-[0.2em] text-cyan-700 dark:text-cyan-300 font-semibold">AI Digest</p>
                            @if(!empty($newsDigest['topics']))
                                <div class="flex flex-wrap gap-2 text-xs">
                                    @foreach($newsDigest['topics'] as $topic)
                                        <span class="px-2 py-1 rounded-full bg-white/70 dark:bg-black/40 text-cyan-700 dark:text-cyan-200 border border-cyan-200/70 dark:border-cyan-700/60">
                                            {{ $topic }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <h3 class="mt-3 text-lg font-bold text-gray-900 dark:text-white">{{ $newsDigest['headline'] ?? '' }}</h3>
                        @if(!empty($newsDigest['bullets']))
                            <ul class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-200">
                                @foreach($newsDigest['bullets'] as $bullet)
                                    <li class="flex gap-2">
                                        <span class="mt-1 w-2 h-2 rounded-full bg-cyan-500"></span>
                                        <span>{{ $bullet }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach(array_slice($newsArticles, 0, 6) as $article)
                        <article class="rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition">
                            @if(!empty($article['image']))
                                <a href="{{ $article['url'] }}" target="_blank" rel="noopener">
                                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-full h-44 object-cover">
                                </a>
                            @endif
                            <div class="p-5">
                                <p class="text-xs uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 font-semibold">
                                    {{ $article['source'] ?? 'Source' }}
                                </p>
                                <h3 class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
                                    <a href="{{ $article['url'] }}" target="_blank" rel="noopener" class="hover:text-cyan-600 dark:hover:text-cyan-400">
                                        {{ $article['title'] }}
                                    </a>
                                </h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                                    {{ $article['description'] ?? '' }}
                                </p>
                                <div class="mt-4 text-xs text-gray-500 flex items-center justify-between">
                                    <span>{{ $article['author'] ?? 'Editorial' }}</span>
                                    <span>
                                        {{ !empty($article['published_at']) ? \Carbon\Carbon::parse($article['published_at'])->format('M d, Y') : '' }}
                                    </span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-10 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-8 text-center">
                    <p class="text-gray-700 dark:text-gray-300">News is warming up. Check back shortly.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Explore Near Me Section -->
    <section x-data="exploreNearMe()" class="py-8 sm:py-12 md:py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-transparent to-cyan-900/5 dark:to-cyan-500/5">
        <div class="max-w-7xl mx-auto px-0">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                    Explore Near <span class="gradient-text">You</span>
                </h2>
                <p class="text-gray-600 dark:text-gray-300 text-lg md:text-xl mb-2">
                    Discover amazing venues around <span x-text="userCity" class="font-semibold text-cyan-600 dark:text-cyan-400"></span>
                </p>



                <!-- Location Tracking Status -->
                <div class="mt-3 flex flex-col items-center gap-3">
                    <!-- Status Indicator -->
                    <div class="flex items-center gap-2">
                        <template x-if="locationPermissionGranted">
                            <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                                <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">GPS Active: <span x-text="userLat.toFixed(4) + ', ' + userLng.toFixed(4)"></span></span>
                            </div>
                        </template>
                        <template x-if="!locationPermissionGranted && !loading">
                            <div class="flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium" x-text="locationPermissionGranted ? 'GPS Active: ' + (userLat ? userLat.toFixed(4) + ', ' + userLng.toFixed(4) : '') : 'Location Blocked - Using Default'"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Manual City Search (Visible when blocked or as alternative) -->
                    <div class="flex items-center gap-2 max-w-sm w-full" x-show="!locationPermissionGranted || showManualSearch">
                        <input type="text" 
                               x-model="manualCity" 
                               x-on:keydown.enter="searchCity()"
                               placeholder="Enter city name..." 
                               class="flex-1 px-4 py-2 rounded-lg border border-cyan-300 dark:border-cyan-700 bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 outline-none transition-all">
                        <button x-on:click="searchCity()" 
                                class="glass-btn-primary glass-btn-sm whitespace-nowrap">
                            Update
                        </button>
                    </div>

                    <!-- Force GPS Button (always visible for debugging) -->
                    <div class="flex items-center gap-2">
                        <button x-on:click="startLocationTracking(true)"
                                class="glass-btn-primary glass-btn-sm hover-lift transition-all">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Try GPS
                        </button>
                        <button x-on:click="showManualSearch = !showManualSearch"
                                class="glass-btn-secondary glass-btn-sm" 
                                x-text="showManualSearch ? 'Hide Search' : 'Search Manually'">
                        </button>
                    </div>
                </div>
            </div>

            <!-- Category Tabs -->
            <div class="flex flex-wrap justify-center gap-3 mb-8">
                <template x-for="cat in categories" :key="cat.id">
                    <button x-on:click="selectedCategory = cat.id; loadVenues()"
                            :class="selectedCategory === cat.id ? 'glass-btn-primary glass-btn-md shadow-lg scale-105' : 'glass-btn-secondary glass-btn-md'"
                            class="font-semibold transition-all hover-lift flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="cat.icon"></svg>
                        <span x-text="cat.name"></span>
                    </button>
                </template>
            </div>

            <!-- Advanced Filters Section -->
            <div class="mb-8 max-w-5xl mx-auto">
                <!-- Filter Toggle Button -->
                <div class="text-center mb-4">
                    <button x-on:click="showFilters = !showFilters"
                            class="glass-btn-secondary glass-btn-md hover-lift transition-all inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                    </button>
                </div>

                <!-- Filters Panel -->
                <div x-show="showFilters"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="glass-card p-4 sm:p-6 rounded-2xl">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <!-- Sort By -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                                Sort By
                            </label>
                            <select x-model="sortBy" x-on:change="applyFiltersAndSort()"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition">
                                <option value="distance">Closest First</option>
                                <option value="rating">Highest Rated</option>
                                <option value="popularity">Most Popular</option>
                                <option value="price_high">Luxury ($$$$)</option>
                                <option value="price_low">Budget ($)</option>
                            </select>
                        </div>

                        <!-- Minimum Rating -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Min Rating
                            </label>
                            <select x-model="filterRating" x-on:change="applyFiltersAndSort()"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition">
                                <option value="0">Any Rating</option>
                                <option value="3">3+ Stars</option>
                                <option value="3.5">3.5+ Stars</option>
                                <option value="4">4+ Stars</option>
                                <option value="4.5">4.5+ Stars</option>
                            </select>
                        </div>

                        <!-- Price Level (Luxury/Class) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Price Range
                            </label>
                            <select x-model="filterPriceLevel" x-on:change="applyFiltersAndSort()"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 transition">
                                <option :value="null">All Prices</option>
                                <option value="1">Budget ($)</option>
                                <option value="2">Moderate ($$)</option>
                                <option value="3">Upscale ($$$)</option>
                                <option value="4">Luxury ($$$$)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Open Now Toggle & Reset -->
                    <div class="mt-4 sm:mt-6 flex flex-wrap items-center justify-between gap-3">
                        <!-- Open Now Toggle -->
                        <label class="flex items-center gap-3 cursor-pointer glass-btn-secondary px-4 py-2 rounded-lg hover-lift transition-all">
                            <input type="checkbox" x-model="filterOpenNow" x-on:change="applyFiltersAndSort()"
                                   class="w-5 h-5 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Open Now Only
                            </span>
                        </label>

                        <!-- Results Count & Reset -->
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                <span x-text="filteredVenuesCount" class="font-bold text-cyan-600 dark:text-cyan-400"></span> results
                            </span>
                            <button x-on:click="resetFilters()"
                                    class="glass-btn-warning glass-btn-sm hover-lift transition-all inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="i in 3" :key="i">
                    <div class="glass-card rounded-2xl overflow-hidden animate-pulse">
                        <div class="h-48 bg-gray-200 dark:bg-gray-700"></div>
                        <div class="p-6 space-y-4">
                            <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                            <div class="flex gap-2">
                                <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                                <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Error State -->
            <div x-show="!loading && errorMessage" class="text-center py-10">
                <svg class="mx-auto w-12 h-12 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-700 dark:text-gray-300" x-text="errorMessage"></p>
            </div>

            <!-- Venues Grid -->
            <div x-show="!loading && venues.length > 0" class="explore-venues-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="venue in venues" :key="venue.id">
                    <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all group">
                        <!-- Venue Image -->
                        <div class="relative h-48 bg-gradient-to-br from-cyan-500 to-blue-600 overflow-hidden">
                            <template x-if="venue.photo_url || venue.photo_reference">
                                <img :src="venue.photo_url || `/api/venue-photo/${venue.photo_reference}`"
                                     :alt="venue.name"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                     x-on:error="$el.src = 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=800&q=80'">
                            </template>
                            <template x-if="!venue.photo_url && !venue.photo_reference">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="getCategoryIcon(selectedCategory)"></svg>
                                </div>
                            </template>
                        </div>

                        <!-- Venue Info -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-2">
                                <h3 x-text="venue.name" class="text-xl font-bold text-gray-900 dark:text-white flex-1"></h3>
                                <!-- Distance Badge -->
                                <template x-if="venue.distance_km">
                                    <span class="ml-2 px-2 py-1 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-400 text-xs font-bold rounded-full whitespace-nowrap">
                                        <span x-text="venue.distance_km"></span> km
                                    </span>
                                </template>
                            </div>
                            <p x-text="venue.address" class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2"></p>

                            <!-- Rating and Status -->
                            <div class="flex items-center gap-3 mb-4 flex-wrap">
                                <template x-if="venue.rating">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span x-text="venue.rating.toFixed(1)" class="font-semibold text-gray-900 dark:text-white"></span>
                                        <template x-if="venue.user_ratings_total">
                                            <span class="text-xs text-gray-500 dark:text-gray-500" x-text="`(${venue.user_ratings_total})`"></span>
                                        </template>
                                    </div>
                                </template>
                                <!-- Open Now Badge -->
                                <template x-if="venue.is_open_now === true">
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 text-xs font-bold rounded-full">
                                        Open Now
                                    </span>
                                </template>
                                <template x-if="venue.is_open_now === false">
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 text-xs font-bold rounded-full">
                                        Closed
                                    </span>
                                </template>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3">
                                <a :href="venue.maps_url" target="_blank"
                                   class="flex-1 text-center glass-btn-primary glass-btn-sm font-semibold shadow-lg">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                    Directions
                                </a>
                                <template x-if="venue.phone">
                                    <a :href="`tel:${venue.phone}`"
                                       class="glass-btn-success glass-btn-sm hover-lift transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="!loading && originalVenues.length === 0" class="text-center py-12">
                <svg class="mx-auto w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-gray-600 dark:text-gray-400 text-lg">No venues found in this category. Try another!</p>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && filteredVenuesCount > perPage" class="mt-8 flex items-center justify-between">
                <!-- Results Info -->
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white" x-text="((currentPage - 1) * perPage) + 1"></span>
                    to <span class="font-semibold text-gray-900 dark:text-white" x-text="Math.min(currentPage * perPage, filteredVenuesCount)"></span>
                    of <span class="font-semibold text-gray-900 dark:text-white" x-text="filteredVenuesCount"></span> venues
                </div>

                <!-- Pagination Buttons -->
                <div class="flex items-center gap-2">
                    <button x-on:click="prevPage()"
                            :disabled="!hasPrevPage"
                            :class="hasPrevPage ? 'glass-btn-primary glass-btn-md' : 'bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-500 cursor-not-allowed px-4 py-2 rounded-lg'"
                            class="font-semibold transition-all shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Previous
                    </button>

                    <div class="hidden sm:flex items-center gap-2">
                        <template x-for="page in totalPages" :key="page">
                            <button x-on:click="currentPage = page; updatePaginatedVenues();"
                                    :class="currentPage === page ? 'glass-btn-primary' : 'glass-btn-secondary'"
                                    class="w-10 h-10 rounded-lg font-semibold transition-all hover-lift"
                                    x-text="page">
                            </button>
                        </template>
                    </div>

                    <div class="sm:hidden text-sm font-semibold text-gray-900 dark:text-white">
                        Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                    </div>

                    <button x-on:click="nextPage()"
                            :disabled="!hasNextPage"
                            :class="hasNextPage ? 'glass-btn-primary glass-btn-md' : 'bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-500 cursor-not-allowed px-4 py-2 rounded-lg'"
                            class="font-semibold transition-all shadow-lg flex items-center gap-2">
                        Next
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-gray-950 text-white mt-16 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-8">
                <!-- About -->
                <div>
                    <h4 class="text-lg font-bold mb-4 text-white">9yt !Trybe</h4>
                    <p class="text-gray-400 dark:text-gray-300 text-sm">We are the heart of the party, the soul of the night, and the creators of unforgettable memories.</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('events.index') }}" class="text-gray-400 hover:text-white transition">Events</a></li>
                        <li><a href="{{ route('shop.index') }}" class="text-gray-400 hover:text-white transition">Shop</a></li>
                        <li><a href="{{ route('gallery.index') }}" class="text-gray-400 hover:text-white transition">Gallery</a></li>
                        <li><a href="{{ Auth::guard('company')->check() ? route('organization.sms.dashboard') : route('user.sms.dashboard') }}" class="text-gray-400 hover:text-white transition">Bulk SMS</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="text-gray-400 hover:text-white transition">Jobs</a></li>
                        <li><a href="{{ route('team.index') }}" class="text-gray-400 hover:text-white transition">Join Team</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition">About Us</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('legal.terms') }}" class="text-gray-400 hover:text-white transition">Terms & Conditions</a></li>
                        <li><a href="{{ route('legal.privacy') }}" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="{{ route('legal.cookies') }}" class="text-gray-400 hover:text-white transition">Cookie Policy</a></li>
                        <li><a href="{{ route('legal.refund') }}" class="text-gray-400 hover:text-white transition">Refund Policy</a></li>
                        <li><a href="{{ route('legal.disclaimer') }}" class="text-gray-400 hover:text-white transition">Disclaimer</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-bold mb-4 text-white">Contact Us</h4>
                    <ul class="space-y-2 text-sm text-gray-400 dark:text-gray-300">
                        <li><strong class="text-white">Email:</strong><br>9yttrybe@@gmail.com</li>
                        <li><strong class="text-white">Phone:</strong><br>0545566524 / 0267825223</li>
                        <li><strong class="text-white">WhatsApp:</strong><br>0267825223</li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Follow Us</h4>
                    <div class="space-y-3">
                        <a href="https://www.tiktok.com/@@9yt.trybe?_r=1&_t=ZM-9191LGZJuSB" target="_blank" class="flex items-center text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/>
                            </svg>
                            @@9yt.trybe
                        </a>
                        <a href="https://instagram.com/9yt.trybe" target="_blank" class="flex items-center text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                            @@9yt.trybe
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} 9yt !Trybe. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js Components -->
    <script>
        function initApp() {
            return {
                darkMode: localStorage.getItem('darkMode') !== 'false',
                
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
        
        function slider() {
            return {
                currentSlide: 0,
                totalSlides: 2,
                autoplayInterval: null,

                init() {
                    this.startAutoplay();
                },

                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                    this.resetAutoplay();
                },

                prevSlide() {
                    this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                    this.resetAutoplay();
                },

                startAutoplay() {
                    this.autoplayInterval = setInterval(() => {
                        this.nextSlide();
                    }, 5000);
                },

                resetAutoplay() {
                    clearInterval(this.autoplayInterval);
                    this.startAutoplay();
                }
            }
        }

        function exploreNearMe() {
            return {
                userLat: 5.6037,
                userLng: -0.1870,
                userCity: 'Accra',
                selectedCategory: 'club',
                preferredRegion: '{{ request('region') }}',
                regionOverride: false,
                regionAutoApplied: false,
                regionCoordinates: {
                    'Greater Accra': { lat: 5.6037, lng: -0.1870, city: 'Accra' },
                    'Ashanti': { lat: 6.6970, lng: -1.6244, city: 'Kumasi' },
                    'Central': { lat: 5.1054, lng: -1.2466, city: 'Cape Coast' },
                    'Western': { lat: 4.8845, lng: -1.7554, city: 'Takoradi' },
                    'Western North': { lat: 6.2012, lng: -2.2640, city: 'Sefwi Wiawso' },
                    'Eastern': { lat: 6.0904, lng: -0.2591, city: 'Koforidua' },
                    'Volta': { lat: 6.6008, lng: 0.4713, city: 'Ho' },
                    'Oti': { lat: 7.7012, lng: 0.2590, city: 'Dambai' },
                    'Northern': { lat: 9.4077, lng: -0.8532, city: 'Tamale' },
                    'Savannah': { lat: 9.5165, lng: -1.0264, city: 'Damongo' },
                    'North East': { lat: 10.7856, lng: -0.8514, city: 'Nalerigu' },
                    'Upper East': { lat: 10.7850, lng: -0.8510, city: 'Bolgatanga' },
                    'Upper West': { lat: 10.0601, lng: -2.5019, city: 'Wa' },
                    'Bono': { lat: 7.3399, lng: -2.3268, city: 'Sunyani' },
                    'Bono East': { lat: 7.8000, lng: -1.0333, city: 'Techiman' },
                    'Ahafo': { lat: 7.1500, lng: -2.5500, city: 'Goaso' }
                },
                categories: [
                    { id: 'club', name: 'Nightclubs', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>' },
                    { id: 'lodging', name: 'Airbnbs', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>' },
                    { id: 'restaurant', name: 'Restaurants', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>' },
                    { id: 'lounge', name: 'Lounges', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a2 2 0 01-2-2V5a2 2 0 012-2h5a2 2 0 012 2v14a2 2 0 01-2 2h-5z M9 18h12 M9 14h12 M9 10h12"/>' },
                    { id: 'arcade', name: 'Arcades', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5V7M9 5V7M7 12H17M7 17H17M5 20H19C20.1046 20 21 19.1046 21 18V6C21 4.89543 20.1046 4 19 4H5C3.89543 4 3 4.89543 3 6V18C3 19.1046 3.89543 20 5 20Z"/>' },
                    { id: 'hotel', name: 'Hotels', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>' }
                ],
                originalVenues: [],
                venues: [],
                loading: false,
                errorMessage: '',
                currentPage: 1,
                currentPage: 1,
                perPage: 15,
                filteredVenuesCount: 0,
                allFilteredVenues: [],

                // Advanced Filters
                sortBy: 'distance',
                filterOpenNow: false,
                filterRating: 0,
                filterPriceLevel: null,
                showFilters: false,
                manualCity: '',
                showManualSearch: false,

                // Location tracking
                watchId: null,
                locationPermissionGranted: false,
                lastLat: null,
                lastLng: null,
                updateThreshold: 0.001, // ~100 meters (0.001 degrees ≈ 111 meters)

                init() {
                    if (this.preferredRegion && this.regionCoordinates[this.preferredRegion]) {
                        this.applyRegionPreference();
                        return;
                    }

                    // Automatically request location permission and start tracking
                    this.startLocationTracking();
                },
                startLocationTracking(force = false) {
                    if (this.regionOverride && !force) {
                        return;
                    }

                    if (navigator.geolocation) {
                        console.log('Requesting GPS location permission...');

                        // First, get current location immediately
                        navigator.geolocation.getCurrentPosition(
                            async (position) => {
                                console.log('GPS Permission granted.');

                                // Set permission flag first
                                this.locationPermissionGranted = true;

                                // Then update location
                                await this.updateLocation(position);

                                // Then start watching for location changes
                                this.watchId = navigator.geolocation.watchPosition(
                                    async (position) => {
                                        await this.handleLocationUpdate(position);
                                    },
                                    (error) => {
                                        console.error('Location watch error:', error);
                                    },
                                    {
                                        enableHighAccuracy: true,
                                        maximumAge: 30000,
                                        timeout: 60000
                                    }
                                );
                            },
                            async (error) => {
                                console.error('GPS permission denied or error:', error.message);

                                // Reset to default Accra coordinates
                                this.userLat = 5.6037;
                                this.userLng = -0.1870;
                                this.userCity = 'Accra';

                                await this.loadLocationFromIp();
                                this.loadVenues();
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 60000,
                                maximumAge: 0
                            }
                        );
                    } else {
                        // Browser doesn't support geolocation
                        console.log('Geolocation not supported by browser');
                        this.userLat = 5.6037;
                        this.userLng = -0.1870;
                        this.userCity = 'Accra';
                        this.loadLocationFromIp().finally(() => this.loadVenues());
                    }
                },

                async searchCity() {
                    if (!this.manualCity) return;
                    this.loading = true;
                    this.errorMessage = '';
                    
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(this.manualCity)}&format=json&limit=1&addressdetails=1`);
                        const data = await response.json();
                        
                        if (data && data.length > 0) {
                            this.userLat = parseFloat(data[0].lat);
                            this.userLng = parseFloat(data[0].lon);
                            this.userCity = data[0].address.city || data[0].address.town || data[0].address.village || data[0].display_name.split(',')[0];
                            this.locationPermissionGranted = false; // Disable GPS pulse
                            this.loadVenues();
                            this.showManualSearch = false;
                        } else {
                            this.errorMessage = 'City not found. Please try another name.';
                        }
                    } catch (error) {
                        this.errorMessage = 'Location search failed. Please try again.';
                    } finally {
                        this.loading = false;
                    }
                },

                getNearestRegion() {
                    let closestRegion = null;
                    let closestDistance = Number.POSITIVE_INFINITY;

                    Object.entries(this.regionCoordinates).forEach(([region, coords]) => {
                        const latDiff = coords.lat - this.userLat;
                        const lngDiff = coords.lng - this.userLng;
                        const distance = Math.hypot(latDiff, lngDiff);

                        if (distance < closestDistance) {
                            closestDistance = distance;
                            closestRegion = region;
                        }
                    });

                    return closestRegion;
                },

                applyRegionPreference() {
                    const region = this.regionCoordinates[this.preferredRegion];
                    if (!region) {
                        return;
                    }

                    this.regionOverride = true;
                    this.userLat = region.lat;
                    this.userLng = region.lng;
                    this.userCity = region.city;
                    this.loadVenues();
                },

                async loadLocationFromIp() {
                    try {
                        const response = await fetch('/api/get-location');
                        if (!response.ok) {
                            return;
                        }
                        const data = await response.json();
                        if (data && data.latitude && data.longitude) {
                            this.userLat = parseFloat(data.latitude);
                            this.userLng = parseFloat(data.longitude);
                            this.userCity = data.city || this.userCity;
                        }
                    } catch (error) {
                        console.log('IP location lookup failed.');
                    }
                },

                async handleLocationUpdate(position) {
                    const newLat = position.coords.latitude;
                    const newLng = position.coords.longitude;

                    // Check if user has moved significantly
                    if (this.lastLat !== null && this.lastLng !== null) {
                        const latDiff = Math.abs(newLat - this.lastLat);
                        const lngDiff = Math.abs(newLng - this.lastLng);

                        // Only update if moved more than threshold
                        if (latDiff < this.updateThreshold && lngDiff < this.updateThreshold) {
                            return; // User hasn't moved enough, don't update
                        }
                    }

                    // User has moved significantly, update venues
                    console.log('Location changed, updating venues...');
                    await this.updateLocation(position);
                },

                async updateLocation(position) {
                    this.userLat = position.coords.latitude;
                    this.userLng = position.coords.longitude;
                    this.lastLat = this.userLat;
                    this.lastLng = this.userLng;

                    console.log('🗺️ Detecting city from coordinates...');

                    // Simple coordinate-based city detection (works without API key!)
                    const detectCity = (lat, lng) => {
                        // Kumasi: ~6.7°N, 1.6°W
                        if (lat > 6.4 && lat < 7.0 && lng > -2.0 && lng < -1.2) return 'Kumasi';
                        // Accra/Legon: ~5.6°N, 0.2°W
                        if (lat > 5.4 && lat < 5.9 && lng > -0.5 && lng < 0.1) return 'Accra';
                        // Takoradi
                        if (lat > 4.8 && lat < 5.2 && lng > -2.0 && lng < -1.5) return 'Takoradi';
                        // Fallback
                        return lat > 6.0 ? 'Northern Ghana' : 'Southern Ghana';
                    };

                    // Detect city from coordinates first (fast & reliable)
                    this.userCity = detectCity(this.userLat, this.userLng);
                    console.log('📍 City detected from GPS:', this.userCity, `(${this.userLat.toFixed(4)}, ${this.userLng.toFixed(4)})`);

                    // Try reverse geocoding for more specific name (with Google -> OSM fallback)
                    try {
                        const provider = "{{ config('services.maps.provider', 'osm') }}";
                        const googleKey = "{{ config('services.google.maps_api_key') }}";
                        let googleSucceeded = false;

                        if (provider === 'google' && googleKey) {
                            const response = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${this.userLat},${this.userLng}&key=${googleKey}`);
                            const data = await response.json();

                            if (data.status === 'OK' && data.results && data.results[0]) {
                                const cityComponent = data.results[0].address_components.find(c => c.types.includes('locality'));
                                const areaComponent = data.results[0].address_components.find(c => c.types.includes('sublocality'));

                                if (areaComponent || cityComponent) {
                                    this.userCity = areaComponent?.long_name || cityComponent?.long_name;
                                    googleSucceeded = true;
                                    console.log('✨ Enhanced location from Google:', this.userCity);
                                }
                            }
                        }

                        if (!googleSucceeded) {
                            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${this.userLat}&lon=${this.userLng}&format=json&accept-language=en`);
                            const data = await response.json();

                            if (data && data.address) {
                                this.userCity = data.address.city || data.address.town || data.address.village || data.address.suburb || this.userCity;
                                console.log('✨ Enhanced location from Nominatim:', this.userCity);
                            }
                        }
                    } catch (error) {
                        console.log('ℹ️ Using coordinate-based detection (Geocoding API unavailable)');
                    }

                    // Auto-apply region filter once if not already selected
                    if (!this.preferredRegion && !this.regionOverride && !this.regionAutoApplied) {
                        const nearestRegion = this.getNearestRegion();
                        if (nearestRegion) {
                            this.regionAutoApplied = true;
                            const params = new URLSearchParams(window.location.search);
                            if (!params.get('region')) {
                                params.set('region', nearestRegion);
                                window.location.search = params.toString();
                                return;
                            }
                        }
                    }

                    // Load venues with new location
                    console.log('🔄 Loading venues for coordinates:', this.userLat, this.userLng);
                    await this.loadVenues();
                },

                stopLocationTracking() {
                    if (this.watchId !== null) {
                        navigator.geolocation.clearWatch(this.watchId);
                        this.watchId = null;
                        console.log('Location tracking stopped');
                    }
                },

                // getCurrentLocation is now handled by startLocationTracking()
                // No more IP-based geolocation - GPS only!

                async loadVenues() {
                    this.loading = true;
                    this.errorMessage = '';
                    this.currentPage = 1; // Reset to first page
                    console.log(`🔍 Loading venues for: ${this.userCity} (${this.selectedCategory})`);
                    
                    try {
                        const controller = new AbortController();
                        const timeoutId = setTimeout(() => controller.abort(), 20000);
                        const response = await fetch(`${window.location.origin}/api/nearby-venues?lat=${this.userLat}&lng=${this.userLng}&category=${this.selectedCategory}`, {
                            signal: controller.signal
                        });
                        clearTimeout(timeoutId);

                        if (!response.ok) {
                            throw new Error('Failed to load venues');
                        }

                        const data = await response.json();
                        this.originalVenues = data.places || [];
                        console.log(`✅ Loaded ${this.originalVenues.length} venues`);
                        this.applyFiltersAndSort();
                    } catch (error) {
                        console.error('❌ Failed to load venues:', error);
                        this.originalVenues = [];
                        this.venues = [];
                        this.errorMessage = error && error.name === 'AbortError'
                            ? 'Nearby venues request timed out. Please try again.'
                            : 'We couldn\'t find any venues right now. Please try again in a moment!';
                    } finally {
                        this.loading = false;
                    }
                },

                applyFiltersAndSort() {
                    // Start with all original venues
                    let filtered = [...this.originalVenues];

                    console.log(`🧪 Applying filters to ${filtered.length} venues...`);

                    // Filter by open now
                    if (this.filterOpenNow) {
                        filtered = filtered.filter(v => v.is_open_now === true);
                    }

                    // Filter by minimum rating
                    if (this.filterRating > 0) {
                        filtered = filtered.filter(v => (v.rating || 0) >= this.filterRating);
                    }

                    // Filter by price level (luxury/class)
                    if (this.filterPriceLevel !== null) {
                        filtered = filtered.filter(v => v.price_level === this.filterPriceLevel);
                    }

                    // Sort venues
                    filtered.sort((a, b) => {
                        switch (this.sortBy) {
                            case 'distance':
                                return (a.distance_km || 999) - (b.distance_km || 999);
                            case 'rating':
                                return (b.rating || 0) - (a.rating || 0);
                            case 'popularity':
                                return (b.user_ratings_total || 0) - (a.user_ratings_total || 0);
                            case 'price_high':
                                return (b.price_level || 0) - (a.price_level || 0);
                            case 'price_low':
                                return (a.price_level || 0) - (b.price_level || 0);
                            default:
                                return 0;
                        }
                    });

                    // Update paginated view and reset pagination
                    this.currentPage = 1;
                    
                    // We need a helper to store the FULL filtered list for pagination UI
                    this.filteredVenuesCount = filtered.length;
                    this.allFilteredVenues = filtered; // Keep the filtered list for pagination
                    
                    this.updatePaginatedVenues();
                },

                updatePaginatedVenues() {
                    const start = (this.currentPage - 1) * this.perPage;
                    const end = start + this.perPage;
                    this.venues = (this.allFilteredVenues || []).slice(start, end);
                },

                resetFilters() {
                    this.sortBy = 'distance';
                    this.filterOpenNow = false;
                    this.filterRating = 0;
                    this.filterPriceLevel = null;
                    this.loadVenues();
                },

                get totalPages() {
                    return Math.ceil((this.allFilteredVenues?.length || 0) / this.perPage);
                },

                get hasPrevPage() {
                    return this.currentPage > 1;
                },

                get hasNextPage() {
                    return this.currentPage < this.totalPages;
                },

                prevPage() {
                    if (this.hasPrevPage) {
                        this.currentPage--;
                        this.updatePaginatedVenues();
                        window.scrollTo({ top: document.querySelector('.explore-venues-grid').offsetTop - 100, behavior: 'smooth' });
                    }
                },

                nextPage() {
                    if (this.hasNextPage) {
                        this.currentPage++;
                        this.updatePaginatedVenues();
                        window.scrollTo({ top: document.querySelector('.explore-venues-grid').offsetTop - 100, behavior: 'smooth' });
                    }
                },

                getCategoryIcon(category) {
                    return this.categories.find(c => c.id === category)?.icon || '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>';
                }
            }
        }

        // Interactive Tour Guide - Professional & Mobile-Responsive
        function initTour() {
            // Check if user has seen the tour
            const tourCompleted = localStorage.getItem('tourCompleted');
            console.log('Tour Status:', tourCompleted ? 'Completed' : 'Not completed');

            if (!tourCompleted) {
                const isMobile = window.innerWidth < 768;

                const tour = new Shepherd.Tour({
                    useModalOverlay: true,
                    defaultStepOptions: {
                        cancelIcon: {
                            enabled: true
                        },
                        classes: 'glass-card shadow-2xl rounded-2xl max-w-md',
                        scrollTo: { behavior: 'smooth', block: 'center' },
                        popperOptions: {
                            modifiers: [
                                {
                                    name: 'offset',
                                    options: {
                                        offset: [0, 12]
                                    }
                                }
                            ]
                        }
                    }
                });

                // Step 1: Welcome
                tour.addStep({
                    id: 'welcome',
                    title: '<div class="flex items-center gap-2"><svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg><span class="font-bold text-lg">Welcome to 9yt !Trybe</span></div>',
                    text: 'Discover amazing events, connect with organizers, shop for tickets, and explore venues near you. Let us show you around.',
                    buttons: [
                        {
                            text: 'Skip',
                            classes: 'px-4 py-2 text-gray-600 hover:text-gray-800 rounded-lg',
                            action: function() {
                                localStorage.setItem('tourCompleted', 'true');
                                tour.complete();
                            }
                        },
                        {
                            text: 'Start Tour',
                            classes: 'px-6 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition',
                            action: tour.next
                        }
                    ]
                });

                // Step 2: Global Search (mobile menu button on mobile, search icon on desktop)
                let searchTarget;
                if (isMobile) {
                    // On mobile, target the mobile menu button
                    searchTarget = document.querySelector('button[class*="md:hidden"]');
                } else {
                    // On desktop, target search icon
                    const searchButtons = document.querySelectorAll('button');
                    searchTarget = Array.from(searchButtons).find(btn => {
                        const svg = btn.querySelector('svg path[d*="21 21"]');
                        return svg !== null;
                    });
                }

                if (searchTarget) {
                    tour.addStep({
                        id: 'search',
                        title: '<div class="flex items-center gap-2"><svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><span class="font-semibold">' + (isMobile ? 'Mobile Menu' : 'Global Search') + '</span></div>',
                        text: isMobile ? 'Tap here to open the mobile menu and access all features including search, events, shop, and more.' : 'Click here to search across events, shops, polls, surveys, conferences, and more with live suggestions.',
                        attachTo: {
                            element: searchTarget,
                            on: isMobile ? 'bottom' : 'bottom'
                        },
                        buttons: [
                            {
                                text: 'Back',
                                classes: 'px-4 py-2 text-gray-600 rounded-lg',
                                action: tour.back
                            },
                            {
                                text: 'Next',
                                classes: 'px-6 py-2 bg-cyan-600 text-white rounded-lg shadow-lg',
                                action: tour.next
                            }
                        ]
                    });
                }

                // Step 3: Dark Mode Toggle
                const darkModeButton = document.querySelector('button[class*="ml-4"][class*="rounded-lg"]');
                if (darkModeButton) {
                    tour.addStep({
                        id: 'dark-mode',
                        title: '<div class="flex items-center gap-2"><svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg><span class="font-semibold">Theme Toggle</span></div>',
                        text: 'Switch between light and dark modes. Dark mode features pure black backgrounds optimized for OLED displays.',
                        attachTo: {
                            element: darkModeButton,
                            on: 'bottom'
                        },
                        buttons: [
                            {
                                text: 'Back',
                                classes: 'px-4 py-2 text-gray-600 rounded-lg',
                                action: tour.back
                            },
                            {
                                text: 'Next',
                                classes: 'px-6 py-2 bg-cyan-600 text-white rounded-lg shadow-lg',
                                action: tour.next
                            }
                        ]
                    });
                }

                // Step 4: Featured Events Section (always visible on all devices)
                const eventsSection = document.querySelector('section.py-16, section.py-12');
                if (eventsSection) {
                    tour.addStep({
                        id: 'events-section',
                        title: '<div class="flex items-center gap-2"><svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><span class="font-semibold">Featured Events</span></div>',
                        text: 'Scroll down to discover trending events, conferences, and experiences. Each event shows date, location, and ticket availability.',
                        attachTo: {
                            element: eventsSection,
                            on: 'top'
                        },
                        buttons: [
                            {
                                text: 'Back',
                                classes: 'px-4 py-2 text-gray-600 rounded-lg',
                                action: tour.back
                            },
                            {
                                text: 'Next',
                                classes: 'px-6 py-2 bg-cyan-600 text-white rounded-lg shadow-lg',
                                action: tour.next
                            }
                        ]
                    });
                }

                // Step 5: Explore Near You
                const exploreSection = document.querySelector('[x-data*="exploreVenues"]');
                if (exploreSection) {
                    tour.addStep({
                        id: 'explore',
                        title: '<div class="flex items-center gap-2"><svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg><span class="font-semibold">Explore Near You</span></div>',
                        text: 'Discover restaurants, hotels, nightclubs, and more near your location. Use GPS or manual search to find venues within 10km radius.',
                        attachTo: {
                            element: exploreSection,
                            on: 'top'
                        },
                        buttons: [
                            {
                                text: 'Back',
                                classes: 'px-4 py-2 text-gray-600 rounded-lg',
                                action: tour.back
                            },
                            {
                                text: 'Finish',
                                classes: 'px-6 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition',
                                action: function() {
                                    localStorage.setItem('tourCompleted', 'true');
                                    tour.complete();
                                }
                            }
                        ]
                    });
                }

                // Auto-start tour after page loads
                setTimeout(() => {
                    tour.start();
                }, 1500);
            } else {
                console.log('Tour already completed. Run localStorage.removeItem("tourCompleted") to reset.');
            }
        }

        // Initialize tour when DOM is ready or immediately if already loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTour);
        } else {
            // DOM already loaded, run immediately
            initTour();
        }
    </script>
</body>
</html>


