<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') !== 'false' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))\" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendant Login - Ticket Scanner</title>
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

        .logo-hover {
            transition: transform 0.3s ease;
        }
        .logo-hover:hover {
            transform: translateY(-2px) scale(1.02);
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    @include('components.logo-loader', ['id' => 'page-loader', 'text' => 'Loading Scanner Portal...'])

    <!-- Minimal Navigation Header with Glass Effect -->
    <nav class="glass-effect border-b-2 border-white/30 dark:border-gray-700/50 shadow-2xl transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center logo-hover">
                        <img x-show="!darkMode" src="{{ asset('ui/logo/9yt-trybe-logo-light.png') }}" alt="9yt !Trybe Logo" class="h-14 w-auto">
                        <img x-show="darkMode" x-cloak src="{{ asset('ui/logo/9yt-trybe-logo-dark.png') }}" alt="9yt !Trybe Logo" class="h-14 w-auto">
                    </a>
                </div>

                <!-- Right Section: Back to Home + Dark Mode Toggle -->
                <div class="flex items-center space-x-4">
                    <!-- Back to Home Button -->
                    <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Home
                    </a>

                    <!-- Mobile: Icon Only -->
                    <a href="{{ route('home') }}" class="sm:hidden p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>

                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="darkMode" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            {{-- Logo --}}
            <div class="text-center mb-8">
                <div class="inline-block bg-gradient-to-br from-cyan-500 to-blue-600 p-3 rounded-2xl shadow-lg mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Ticket Scanner Portal</h1>
                <p class="text-base text-gray-600 dark:text-gray-400 mt-2">Attendant Login</p>
                <p class="mt-1 text-sm font-medium text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-600 dark:from-cyan-400 dark:to-blue-400">
                    Scan & verify with ease 📱
                </p>
            </div>

            {{-- Login Card --}}
            <div class="glass-card" x-data="{ 
                otpSent: {{ session('otp_sent') ? 'true' : 'false' }}, 
                phone: '{{ session('phone') ?? '' }}',
                countdown: 60,
                timer: null,
                startTimer() {
                    this.countdown = 60;
                    if (this.timer) clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        if (this.countdown > 0) this.countdown--;
                        else clearInterval(this.timer);
                    }, 1000);
                }
            }" x-init="if (otpSent) startTimer()">

                {{-- Messages --}}
                @if(session('message'))
                <div class="mb-4 p-3 rounded-lg bg-cyan-50 dark:bg-cyan-900/30 border border-cyan-200 dark:border-cyan-700">
                    <p class="text-sm text-cyan-800 dark:text-cyan-300">{{ session('message') }}</p>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700">
                    <p class="text-sm text-red-800 dark:text-red-300">{{ $errors->first() }}</p>
                </div>
                @endif

                {{-- Phone Number Step --}}
                <div x-show="!otpSent">
                    <form x-ref="phoneForm" action="{{ route('staff.send-otp') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number
                            </label>
                            <div class="relative">
                                <input type="tel"
                                       id="phone-otp"
                                       name="phone"
                                       x-model="phone"
                                       required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 text-lg">
                                <input type="hidden" name="full_phone" id="full_phone">
                            </div>
                        </div>

                        <button type="submit" class="w-full glass-btn-primary py-3">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Send Verification Code
                        </button>
                    </form>
                </div>

                {{-- OTP Verification Step --}}
                <div x-show="otpSent">
                    <form action="{{ route('staff.verify-otp') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="phone" x-model="phone">

                        <div class="text-center mb-6">
                            <svg class="w-16 h-16 mx-auto text-cyan-600 dark:text-cyan-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                We sent a 6-digit code to<br>
                                <span class="font-semibold text-gray-900 dark:text-white" x-text="phone"></span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Verification Code
                            </label>
                            <input type="text"
                                   name="otp"
                                   required
                                   maxlength="6"
                                   placeholder="000000"
                                   pattern="[0-9]{6}"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 text-center text-2xl font-bold tracking-widest">
                        </div>

                        <button type="submit" class="w-full glass-btn-primary py-3">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Verify & Login
                        </button>

                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Didn't receive the code? 
                                <span x-show="countdown > 0" class="font-medium text-cyan-600 dark:text-cyan-400">
                                    Resend in <span x-text="countdown"></span>s
                                </span>
                                <button type="button" 
                                        x-show="countdown === 0"
                                        @click="otpSent = false; $nextTick(() => $refs.phoneForm.submit())"
                                        class="font-medium text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 transition-colors">
                                    Resend OTP
                                </button>
                            </p>
                        </div>

                        <button type="button"
                                @click="otpSent = false"
                                class="w-full text-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            Use different phone number
                        </button>
                    </form>
                </div>

                {{-- Back to Home --}}
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400">
                        Back to Home
                    </a>
                </div>
            </div>

            {{-- Help Text --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Need help? Contact your event organizer
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.querySelector("#phone-otp");
            const fullPhoneInput = document.querySelector("#full_phone");
            
            if (phoneInput) {
                const iti = window.intlTelInput(phoneInput, {
                    initialCountry: "gh",
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
                });

                phoneInput.addEventListener('change', function() {
                    fullPhoneInput.value = iti.getNumber();
                });
                phoneInput.addEventListener('keyup', function() {
                    fullPhoneInput.value = iti.getNumber();
                });
                
                // Form submission
                const form = phoneInput.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        fullPhoneInput.value = iti.getNumber();
                    });
                }
            }
        });

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
</body>
</html>
