@extends('layouts.company')

@section('title', 'Ticket Scanner - ' . $event->title)

@section('content')
<div x-data="ticketScanner()" x-init="init()" class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">Ticket Scanner</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ $event->title }}</p>
            </div>
            <a href="{{ route('organization.events.index') }}" class="glass-btn-secondary glass-btn-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Events
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="glass-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Tickets</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white" x-text="stats.total">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Checked In</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400" x-text="stats.checked_in">{{ $stats['checked_in'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400" x-text="stats.pending">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Check-in Rate</p>
                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"><span x-text="stats.rate">{{ $stats['rate'] }}</span>%</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Scanner Section -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-2xl p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Scan Ticket</h2>

                <!-- Scan Method Tabs -->
                <div class="flex gap-2 mb-6">
                    <button @click="scanMethod = 'qr'" :class="scanMethod === 'qr' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="glass-btn-md flex-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        QR Code Scanner
                    </button>
                    <button @click="scanMethod = 'manual'" :class="scanMethod === 'manual' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="glass-btn-md flex-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Manual Entry
                    </button>
                </div>

                <!-- QR Scanner -->
                <div x-show="scanMethod === 'qr'" class="mb-6">
                    <div id="qr-reader" class="rounded-xl overflow-hidden bg-gray-900" style="min-height: 300px;"></div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 text-center">Position QR code within the frame to scan</p>
                </div>

                <!-- Manual Entry -->
                <div x-show="scanMethod === 'manual'" class="mb-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ticket Code</label>
                            <input type="text"
                                   x-model="ticketCode"
                                   @keyup.enter="verifyTicket()"
                                   placeholder="Enter ticket code (e.g., TKT-ABC123)"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                            <textarea x-model="notes"
                                      rows="2"
                                      placeholder="Add any notes about this check-in..."
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"></textarea>
                        </div>
                        <button @click="verifyTicket()" :disabled="!ticketCode || verifying" class="glass-btn-primary glass-btn-lg glass-btn-block">
                            <svg x-show="!verifying" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="verifying" class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="verifying ? 'Verifying...' : 'Verify Ticket'"></span>
                        </button>
                    </div>
                </div>

                <!-- Verification Result -->
                <div x-show="result" x-transition class="mt-6">
                    <!-- Success -->
                    <div x-show="result?.status === 'valid'" class="bg-green-50 dark:bg-green-900/20 border-2 border-green-500 rounded-xl p-6">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-green-900 dark:text-green-100 mb-2">✓ Ticket Verified!</h3>
                                <p class="text-green-800 dark:text-green-200 mb-4" x-text="result?.message"></p>
                                <div class="space-y-2 text-sm">
                                    <p><span class="font-semibold">Holder:</span> <span x-text="result?.ticket?.holder_name"></span></p>
                                    <p><span class="font-semibold">Email:</span> <span x-text="result?.ticket?.holder_email"></span></p>
                                    <p><span class="font-semibold">Type:</span> <span x-text="result?.ticket?.ticket_type"></span></p>
                                    <p><span class="font-semibold">Code:</span> <span x-text="result?.ticket?.code"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Already Used -->
                    <div x-show="result?.status === 'duplicate'" class="bg-orange-50 dark:bg-orange-900/20 border-2 border-orange-500 rounded-xl p-6">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-orange-600 dark:text-orange-400 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-orange-900 dark:text-orange-100 mb-2">⚠ Already Checked In</h3>
                                <p class="text-orange-800 dark:text-orange-200 mb-4" x-text="result?.message"></p>
                                <div class="space-y-2 text-sm">
                                    <p><span class="font-semibold">Holder:</span> <span x-text="result?.ticket?.holder_name"></span></p>
                                    <p><span class="font-semibold">Checked in:</span> <span x-text="result?.checked_in_at"></span></p>
                                    <p><span class="font-semibold">By:</span> <span x-text="result?.checked_in_by"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invalid/Error -->
                    <div x-show="result?.status === 'invalid' || result?.status === 'error'" class="bg-red-50 dark:bg-red-900/20 border-2 border-red-500 rounded-xl p-6">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-red-600 dark:text-red-400 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-red-900 dark:text-red-100 mb-2">✗ Invalid Ticket</h3>
                                <p class="text-red-800 dark:text-red-200" x-text="result?.message"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-1">
            <div class="glass-card rounded-2xl p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Recent Check-ins</h2>
                <div class="space-y-3" x-show="recentCheckins.length > 0">
                    <template x-for="checkin in recentCheckins" :key="checkin.ticket_code">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                            <p class="font-semibold text-gray-900 dark:text-white text-sm" x-text="checkin.holder_name"></p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                <span x-text="checkin.ticket_code"></span> • <span x-text="checkin.checked_in_at"></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                <span x-text="checkin.method"></span> by <span x-text="checkin.checked_in_by"></span>
                            </p>
                        </div>
                    </template>
                </div>
                <div x-show="recentCheckins.length === 0" class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No check-ins yet</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
function ticketScanner() {
    return {
        scanMethod: 'qr',
        ticketCode: '',
        notes: '',
        verifying: false,
        result: null,
        stats: {
            total: {{ $stats['total'] }},
            checked_in: {{ $stats['checked_in'] }},
            pending: {{ $stats['pending'] }},
            rate: {{ $stats['rate'] }}
        },
        recentCheckins: [],
        qrScanner: null,

        init() {
            this.loadActivity();
            this.initQRScanner();
            // Refresh stats every 10 seconds
            setInterval(() => this.loadActivity(), 10000);
        },

        initQRScanner() {
            if (this.scanMethod === 'qr') {
                this.$nextTick(() => {
                    this.qrScanner = new Html5Qrcode("qr-reader");
                    this.qrScanner.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: 250 },
                        (decodedText) => {
                            this.ticketCode = decodedText;
                            this.verifyTicket();
                        }
                    ).catch(err => {
                        console.error('QR Scanner error:', err);
                    });
                });
            }
        },

        async verifyTicket() {
            if (!this.ticketCode) return;

            this.verifying = true;
            this.result = null;

            try {
                const response = await fetch('{{ route('organization.tickets.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ticket_code: this.ticketCode,
                        event_id: {{ $event->id }},
                        method: this.scanMethod,
                        notes: this.notes
                    })
                });

                this.result = await response.json();

                // Play sound based on result
                if (this.result.status === 'valid') {
                    this.playSound('success');
                } else {
                    this.playSound('error');
                }

                // Refresh activity
                this.loadActivity();

                // Clear form after successful scan
                if (this.result.status === 'valid') {
                    setTimeout(() => {
                        this.ticketCode = '';
                        this.notes = '';
                        this.result = null;
                    }, 3000);
                }

            } catch (error) {
                this.result = {
                    status: 'error',
                    message: 'Failed to verify ticket. Please try again.'
                };
                this.playSound('error');
            } finally {
                this.verifying = false;
            }
        },

        async loadActivity() {
            try {
                const response = await fetch('{{ route('organization.events.check-in-activity', $event) }}');
                const data = await response.json();
                this.stats = data.stats;
                this.recentCheckins = data.recent_checkins;
            } catch (error) {
                console.error('Failed to load activity:', error);
            }
        },

        playSound(type) {
            const audio = new Audio(type === 'success'
                ? 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjCI0fPTgjMGHm7A7+OZUQ0OVKno66xdGAo+l9r0yoAzBSaBzPLaizsIGGS54+SdUhENTKXk8bllHwo3jtTw14Y1BxNo'
                : 'data:audio/wav;base64,UklGRhQCAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YfABAAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8AAP//AAD//wAA//8=');
            audio.play();
        }
    }
}
</script>
@endsection
