<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - {{ $order->event->title ?? 'Event' }}</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Order Confirmed!</h1>
                <p class="text-lg text-gray-600 mt-2">Your tickets have been sent to your email</p>
            </div>

            <!-- Order Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Order Details</h2>
                        <p class="text-sm text-gray-600 mt-1">Order #{{ $order->order_number }}</p>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>

                <!-- Event Info -->
                <div class="border-b border-gray-200 pb-4 mb-4">
                    @if($order->event)
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $order->event->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $order->event->formatted_date }}</p>
                    <p class="text-sm text-gray-600">{{ $order->event->formatted_time }}</p>
                    @if($order->event->location_type === 'venue')
                    <p class="text-sm text-gray-600 mt-2">{{ $order->event->venue_name }}</p>
                    @elseif($order->event->location_type === 'online')
                    <p class="text-sm text-gray-600 mt-2">Online Event</p>
                    @endif
                    @else
                    <h3 class="font-semibold text-gray-900 mb-2">Event not found</h3>
                    @endif
                </div>

                <!-- Customer Info -->
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Contact Information</h3>
                    <p class="text-sm text-gray-600">{{ $order->customer_name }}</p>
                    <p class="text-sm text-gray-600">{{ $order->customer_email }}</p>
                    @if($order->customer_phone)
                    <p class="text-sm text-gray-600">{{ $order->customer_phone }}</p>
                    @endif
                </div>

                <!-- Tickets -->
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Your Tickets</h3>
                    <div class="space-y-3">
                        @foreach($order->attendees as $attendee)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $attendee->ticket->name }}</p>
                                    <p class="text-sm text-gray-600 mt-1">Ticket Code: <span class="font-mono font-bold text-indigo-600">{{ $attendee->ticket_code }}</span></p>
                                    <p class="font-semibold text-gray-900 mt-2">GH₵{{ number_format($attendee->price_paid, 2) }}</p>
                                </div>
                                <div class="flex flex-col items-center gap-2">
                                    <div class="bg-white p-2 rounded-lg border-2 border-gray-300">
                                        {!! QrCode::size(120)->generate($attendee->ticket_code) !!}
                                    </div>
                                    <p class="text-xs text-gray-500">Scan to verify</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pricing -->
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">GH₵{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->service_fee > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Service Fee</span>
                        <span class="font-semibold">GH₵{{ number_format($order->service_fee, 2) }}</span>
                    </div>
                    @endif
                    @if($order->processing_fee > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Processing Fee</span>
                        <span class="font-semibold">GH₵{{ number_format($order->processing_fee, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                        <span>Total Paid</span>
                        <span>{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            <!-- Important Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-blue-900 mb-2">📧 Check Your Email</h3>
                <p class="text-sm text-blue-800">
                    We've sent your tickets to <strong>{{ $order->customer_email }}</strong>.
                    Please check your inbox and spam folder.
                </p>
                @if($order->attendees->where('ticket.type', 'in_person')->count() > 0)
                <p class="text-sm text-blue-800 mt-3">
                    <strong>Important:</strong> Please present your ticket QR code at the event entrance for check-in.
                </p>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex justify-center space-x-4">
                @if($order->event)
                <a href="{{ route('events.show', $order->event->slug) }}"
                   class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                    Back to Event
                </a>
                @endif
                <a href="{{ route('events.index') }}"
                   class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-blue-700 transition">
                    Browse More Events
                </a>
            </div>
        </div>
    </div>
</body>
</html>
