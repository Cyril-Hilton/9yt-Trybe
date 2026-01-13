<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') !== 'false' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Scanner - {{ $staff->name }}</title>
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
    </style>
    <link rel="stylesheet" href="{{ asset('css/glassmorphism.css') }}">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    <div class="min-h-screen" x-data="ticketScanner()">
        {{-- Header --}}
        <header class="glass-effect border-b-2 border-white/30 dark:border-gray-700/50 shadow-2xl sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 sm:h-20">
                    <div class="min-w-0 flex-1">
                        <h1 class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white truncate">Ticket Scanner</h1>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 truncate">{{ $staff->name }}</p>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            <svg x-show="!darkMode" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <svg x-show="darkMode" x-cloak class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </button>
                        <!-- Logout Button -->
                        <form action="{{ route('staff.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="glass-btn-secondary px-2 py-2 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 inline sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6 space-y-4 sm:space-y-6">

            {{-- Event Selector --}}
            <div class="glass-card overflow-hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Event
                </label>
                <select x-model="selectedEventId" @change="eventChanged()"
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 text-sm sm:text-base max-w-full">
                    <option value="">-- Select an event --</option>
                    @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->title }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Scanner Section --}}
            <div x-show="selectedEventId" x-transition class="glass-card">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4 mb-4">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Scan Ticket</h2>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button @click="scanMethod = 'qr'" :class="scanMethod === 'qr' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="flex-1 sm:flex-none px-3 sm:px-4 py-2 text-sm sm:text-base">
                            QR Scanner
                        </button>
                        <button @click="scanMethod = 'manual'" :class="scanMethod === 'manual' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="flex-1 sm:flex-none px-3 sm:px-4 py-2 text-sm sm:text-base">
                            Manual
                        </button>
                    </div>
                </div>

                {{-- QR Scanner --}}
                <div x-show="scanMethod === 'qr'" x-transition>
                    <div id="qr-reader" class="rounded-lg overflow-hidden mb-4"></div>
                    <button @click="toggleScanner()" x-text="scannerActive ? 'Stop Scanner' : 'Start Scanner'" class="glass-btn-primary w-full py-2.5 sm:py-3 text-sm sm:text-base"></button>
                </div>

                {{-- Manual Entry --}}
                <div x-show="scanMethod === 'manual'" x-transition>
                    <div class="space-y-3 sm:space-y-4">
                        <input type="text"
                               x-model="ticketCode"
                               @keyup.enter="verifyTicket()"
                               placeholder="Enter ticket code"
                               class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 text-base sm:text-lg font-mono uppercase">
                        <button @click="verifyTicket()" class="glass-btn-primary w-full py-2.5 sm:py-3 text-sm sm:text-base" :disabled="verifying">
                            <span x-show="!verifying">Verify Ticket</span>
                            <span x-show="verifying" class="flex items-center justify-center">
                                <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Result Display --}}
            <div x-show="result" x-transition class="glass-card">
                {{-- Valid Ticket --}}
                <div x-show="result && result.status === 'valid'" class="text-center">
                    <svg class="w-16 h-16 sm:w-20 sm:h-20 mx-auto text-green-600 dark:text-green-400 mb-3 sm:mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400 mb-3 sm:mb-4">Valid Ticket!</h3>
                    <div class="space-y-2 text-sm sm:text-base lg:text-lg">
                        <p class="text-gray-900 dark:text-white break-words"><strong>Name:</strong> <span x-text="result.ticket.holder_name"></span></p>
                        <p class="text-gray-900 dark:text-white break-all"><strong>Email:</strong> <span x-text="result.ticket.holder_email"></span></p>
                        <p class="text-gray-900 dark:text-white"><strong>Type:</strong> <span x-text="result.ticket.ticket_type"></span></p>
                        <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">Checked in <span x-text="result.ticket.checked_in_at"></span></p>
                    </div>
                </div>

                {{-- Duplicate/Already Used --}}
                <div x-show="result && result.status === 'duplicate'" class="text-center">
                    <svg class="w-16 h-16 sm:w-20 sm:h-20 mx-auto text-orange-600 dark:text-orange-400 mb-3 sm:mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-xl sm:text-2xl font-bold text-orange-600 dark:text-orange-400 mb-3 sm:mb-4">Already Used!</h3>
                    <div class="space-y-2 text-sm sm:text-base">
                        <p class="text-gray-900 dark:text-white break-words"><strong>Name:</strong> <span x-text="result.ticket.holder_name"></span></p>
                        <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">Previously checked in <span x-text="result.checked_in_at"></span></p>
                        <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">By: <span x-text="result.checked_in_by"></span></p>
                    </div>
                </div>

                {{-- Invalid Ticket --}}
                <div x-show="result && result.status === 'invalid'" class="text-center">
                    <svg class="w-16 h-16 sm:w-20 sm:h-20 mx-auto text-red-600 dark:text-red-400 mb-3 sm:mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-xl sm:text-2xl font-bold text-red-600 dark:text-red-400 mb-3 sm:mb-4">Invalid Ticket!</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base break-words px-2" x-text="result.message"></p>
                </div>

                {{-- Error --}}
                <div x-show="result && result.status === 'error'" class="text-center">
                    <svg class="w-16 h-16 sm:w-20 sm:h-20 mx-auto text-red-600 dark:text-red-400 mb-3 sm:mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-xl sm:text-2xl font-bold text-red-600 dark:text-red-400 mb-3 sm:mb-4">Error</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base break-words px-2" x-text="result.message"></p>
                </div>

                <button @click="reset()" class="glass-btn-primary w-full mt-4 sm:mt-6 py-2.5 sm:py-3 text-sm sm:text-base">Scan Next Ticket</button>
            </div>

        </div>
    </div>

    <script>
        function ticketScanner() {
            return {
                selectedEventId: '',
                scanMethod: 'qr',
                ticketCode: '',
                result: null,
                verifying: false,
                scannerActive: false,
                html5QrCode: null,

                eventChanged() {
                    this.reset();
                    if (this.scanMethod === 'qr' && this.scannerActive) {
                        this.stopScanner();
                        this.startScanner();
                    }
                },

                async toggleScanner() {
                    if (this.scannerActive) {
                        this.stopScanner();
                    } else {
                        this.startScanner();
                    }
                },

                async startScanner() {
                    if (!this.selectedEventId) {
                        alert('Please select an event first');
                        return;
                    }

                    this.html5QrCode = new Html5Qrcode("qr-reader");

                    try {
                        await this.html5QrCode.start(
                            { facingMode: "environment" },
                            { fps: 10, qrbox: { width: 250, height: 250 } },
                            (decodedText) => {
                                this.ticketCode = decodedText;
                                this.verifyTicket();
                                this.stopScanner();
                            }
                        );
                        this.scannerActive = true;
                    } catch (err) {
                        console.error('Failed to start scanner:', err);
                        alert('Failed to start camera. Please check permissions.');
                    }
                },

                stopScanner() {
                    if (this.html5QrCode) {
                        this.html5QrCode.stop();
                        this.scannerActive = false;
                    }
                },

                async verifyTicket() {
                    if (!this.selectedEventId) {
                        alert('Please select an event first');
                        return;
                    }

                    if (!this.ticketCode) {
                        return;
                    }

                    this.verifying = true;
                    this.result = null;

                    try {
                        const response = await fetch('/api/staff/verify-ticket', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ticket_code: this.ticketCode,
                                event_id: this.selectedEventId,
                                method: this.scanMethod
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            // Server returned an error
                            this.result = {
                                status: 'error',
                                message: data.message || `Error ${response.status}: ${data.error || 'Failed to verify ticket'}`
                            };
                        } else {
                            this.result = data;
                        }
                    } catch (error) {
                        console.error('Verification error:', error);
                        this.result = {
                            status: 'error',
                            message: `Network error: ${error.message}. Please check your connection or try again.`
                        };
                    } finally {
                        this.verifying = false;
                    }
                },

                reset() {
                    this.ticketCode = '';
                    this.result = null;
                    if (this.scanMethod === 'qr' && this.scannerActive) {
                        this.stopScanner();
                        this.startScanner();
                    }
                }
            }
        }
    </script>
</body>
</html>
