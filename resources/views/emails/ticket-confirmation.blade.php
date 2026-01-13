<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Reset and Base Styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background: #f3f4f6;
        }

        .email-container {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            margin: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        /* Premium Header */
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            animation: shimmer 3s infinite;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            position: relative;
        }

        .header p {
            margin: 12px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
            position: relative;
        }

        .brand-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 15px;
            border: 1px solid rgba(255,255,255,0.3);
        }

        /* Content Section */
        .content {
            padding: 30px;
            background: #ffffff;
        }

        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .greeting strong {
            color: #4f46e5;
        }

        /* Event Details Card */
        .event-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 24px;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
        }

        .event-card h2 {
            color: #1e293b;
            margin: 0 0 20px 0;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
        }

        .event-card h2::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 20px;
            background: linear-gradient(to bottom, #4f46e5, #7c3aed);
            border-radius: 2px;
            margin-right: 12px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-row > div {
            display: table-cell;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .info-row:last-child > div {
            border-bottom: none;
        }

        .label {
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 40%;
        }

        .value {
            color: #1e293b;
            font-weight: 600;
            font-size: 15px;
        }

        /* Premium Ticket */
        .premium-ticket {
            position: relative;
            margin: 30px 0;
            border-radius: 20px;
            overflow: hidden;
        }

        .ticket-border {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            padding: 3px;
            border-radius: 20px;
        }

        .ticket-inner {
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
        }

        .ticket-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ticket-type {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .ticket-number {
            font-size: 13px;
            opacity: 0.9;
        }

        .ticket-body {
            padding: 20px;
        }

        .attendee-info {
            margin-bottom: 20px;
        }

        .attendee-name {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 6px 0;
        }

        .attendee-email {
            font-size: 14px;
            color: #64748b;
        }

        /* Event Flier in Ticket */
        .ticket-flier {
            margin: 20px 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 3px solid transparent;
            background: linear-gradient(white, white) padding-box, linear-gradient(135deg, #4f46e5, #7c3aed, #ec4899) border-box;
        }

        .ticket-flier img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Dashed Separator */
        .ticket-separator {
            position: relative;
            margin: 20px -20px;
            border-top: 2px dashed #e2e8f0;
        }

        .ticket-separator::before,
        .ticket-separator::after {
            content: '';
            position: absolute;
            top: -10px;
            width: 20px;
            height: 20px;
            background: #f3f4f6;
            border-radius: 50%;
        }

        .ticket-separator::before {
            left: -10px;
        }

        .ticket-separator::after {
            right: -10px;
        }

        /* QR Code Section */
        .qr-section {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px;
            margin: 15px 0;
        }

        .qr-wrapper {
            display: inline-block;
            padding: 15px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.15), 0 2px 10px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .qr-wrapper::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: 18px;
            z-index: -1;
        }

        .qr-code img {
            display: block;
            width: 180px;
            height: 180px;
        }

        .qr-label {
            margin-top: 15px;
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .qr-fallback {
            margin-top: 8px;
            font-size: 12px;
            color: #64748b;
        }
        .qr-fallback a {
            color: #4f46e5;
            text-decoration: none;
        }

        .ticket-code-display {
            display: inline-block;
            margin-top: 12px;
            padding: 10px 24px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            border-radius: 50px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        /* Ticket Footer */
        .ticket-footer {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 12px 20px;
            display: table;
            width: 100%;
            font-size: 12px;
            color: #64748b;
        }

        .ticket-footer > span {
            display: table-cell;
        }

        .ticket-footer .brand {
            text-align: center;
            font-weight: 700;
            color: #4f46e5;
        }

        .ticket-footer .location {
            text-align: right;
        }

        /* Order Summary */
        .order-summary {
            background: #f8fafc;
            border-radius: 16px;
            padding: 24px;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
        }

        .order-summary h3 {
            color: #1e293b;
            margin: 0 0 16px 0;
            font-size: 18px;
            font-weight: 700;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-total {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 16px;
            border-radius: 12px;
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            font-size: 18px;
        }

        /* Alert Box */
        .alert {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 24px 0;
            border-radius: 0 12px 12px 0;
        }

        .alert strong {
            color: #92400e;
            display: block;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
            color: #78350f;
        }

        .alert li {
            margin: 8px 0;
        }

        /* CTA Button */
        .cta-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease;
        }

        .button:hover {
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5);
            transform: translateY(-2px);
        }

        /* Organizer Info */
        .organizer-info {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
        }

        .organizer-info p {
            margin: 6px 0;
            color: #475569;
        }

        .organizer-info strong {
            color: #1e293b;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #94a3b8;
            padding: 30px;
            text-align: center;
        }

        .footer-links {
            margin: 15px 0;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            margin: 0 15px;
            font-size: 14px;
        }

        .footer-links a:hover {
            color: #e2e8f0;
        }

        .footer-brand {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .footer p {
            margin: 8px 0;
            font-size: 13px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 12px;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .content {
                padding: 20px;
            }

            .ticket-header {
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }

            .qr-code img {
                width: 150px;
                height: 150px;
            }

            .button {
                padding: 14px 30px;
                font-size: 14px;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background-color: #ffffff;
            }
            .email-container {
                max-width: 100%;
                margin: 0;
                box-shadow: none;
            }
            .header, .ticket-header, .summary-total, .footer {
                background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .qr-code, .ticket-wrapper {
                page-break-inside: avoid;
            }
            .button {
                display: none;
            }
            .footer {
                page-break-before: avoid;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background: #f3f4f6; color: #1f2937;">
    <div class="email-container" style="background: #ffffff; color: #1f2937;">
        <!-- Premium Header -->
        <div class="header" style="background: #4f46e5; color: #ffffff;">
            <div class="brand-badge">9YT !TRYBE</div>
            <h1>Your Tickets Are Ready!</h1>
            <p>Get ready for an amazing experience</p>
        </div>

        <div class="content" style="background: #ffffff; color: #1f2937;">
            <!-- Greeting -->
            <p class="greeting">Hi <strong>{{ $order->customer_name }}</strong>,</p>
            <p>Great news! Your registration for <strong>{{ $order->event->title }}</strong> has been confirmed. We can't wait to see you there!</p>

            <!-- Event Details Card -->
            <div class="event-card" style="background: #ffffff; color: #1e293b;">
                <h2>Event Details</h2>

                <div class="info-grid">
                    <div class="info-row">
                        <div class="label">Event</div>
                        <div class="value">{{ $order->event->title }}</div>
                    </div>

                    <div class="info-row">
                        <div class="label">Date & Time</div>
                        <div class="value">{{ $order->event->formatted_date }} at {{ $order->event->formatted_time }}</div>
                    </div>

                    @if($order->event->door_time)
                    <div class="info-row">
                        <div class="label">Doors Open</div>
                        <div class="value">{{ \Carbon\Carbon::parse($order->event->door_time)->format('g:i A') }}</div>
                    </div>
                    @endif

                    <div class="info-row">
                        <div class="label">Location</div>
                        <div class="value">
                            @if($order->event->location_type === 'venue')
                                {{ $order->event->venue_name }}<br>
                                @if($order->event->venue_address)
                                <span style="color: #64748b; font-weight: normal; font-size: 14px;">{{ $order->event->venue_address }}</span><br>
                                <a href="@if($order->event->venue_latitude && $order->event->venue_longitude)https://www.google.com/maps/dir/?api=1&destination={{ $order->event->venue_latitude }},{{ $order->event->venue_longitude }}@else https://www.google.com/maps/search/?api=1&query={{ urlencode($order->event->venue_address) }}@endif"
                                   target="_blank"
                                   style="color: #4f46e5; text-decoration: none; font-size: 14px; font-weight: 500;">
                                    Get Directions &rarr;
                                </a>
                                @endif
                            @elseif($order->event->location_type === 'online')
                                Online Event<br>
                                <span style="color: #64748b; font-weight: normal; font-size: 14px;">Meeting link will be sent closer to the event date</span>
                            @else
                                To Be Announced
                            @endif
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="label">Organized By</div>
                        <div class="value">{{ $order->event->company->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Tickets -->
            <h2 style="color: #1e293b; font-size: 22px; margin: 30px 0 20px 0; font-weight: 700;">Your Tickets</h2>

            @foreach($order->attendees as $index => $attendee)
            <div class="premium-ticket">
                <div class="ticket-border">
                    <div class="ticket-inner">
                        <!-- Ticket Header -->
                        <div class="ticket-header">
                            <span class="ticket-type">{{ $attendee->ticket->name }}</span>
                            <span class="ticket-number">Ticket #{{ $index + 1 }}</span>
                        </div>

                        <div class="ticket-body">
                            <!-- Attendee Info -->
                            <div class="attendee-info">
                                <p class="attendee-name">{{ $attendee->attendee_name }}</p>
                                <p class="attendee-email">{{ $attendee->attendee_email }}</p>
                                @if($attendee->price_paid > 0)
                                <p style="color: #4f46e5; font-weight: 600; margin-top: 8px;">GH₵{{ number_format($attendee->price_paid, 2) }}</p>
                                @endif
                            </div>

                            <!-- Event Flier -->
                            @if($order->event->banner_image)
                            <div class="ticket-flier" style="margin: 20px 0; border-radius: 12px; overflow: hidden;">
                                <img src="{{ $embeddedBannerCid ?? $order->event->banner_url }}" alt="{{ $order->event->title }}" style="width: 100%; height: auto; display: block;">
                            </div>
                            @endif

                            <!-- Separator -->
                            <div class="ticket-separator"></div>

                            <!-- QR Code -->
                            <div class="qr-section">
                                <div class="qr-wrapper">
                                        <div class="qr-code">
                                            @if(!empty($embeddedCids[$attendee->id]))
                                                <img src="{{ $embeddedCids[$attendee->id] }}" alt="QR Code">
                                            @else
                                                @php
                                                    $qrCode = $attendee->qr_code_base64;
                                                    $isSvg = is_string($qrCode) && str_starts_with($qrCode, 'data:image/svg+xml');
                                                @endphp
                                                @if($qrCode && !$isSvg)
                                                    <img src="{{ $qrCode }}" alt="QR Code">
                                                @else
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($attendee->ticket_code) }}" alt="QR Code">
                                                @endif
                                            @endif
                                        </div>
                                </div>
                                    <p class="qr-label">Scan at Entry</p>
                                    <p class="qr-fallback"><a href="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($attendee->ticket_code) }}">Open QR code</a></p>
                                    <div class="ticket-code-display">{{ $attendee->ticket_code }}</div>
                            </div>
                        </div>

                        <!-- Ticket Footer -->
                        <div class="ticket-footer">
                            <span>{{ $order->event->formatted_date }}</span>
                            <span class="brand">9yt !Trybe</span>
                            <span class="location">{{ $order->event->location_type === 'venue' ? Str::limit($order->event->venue_name, 15) : 'Online' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Order Summary -->
            <div class="order-summary">
                <h3>Order Summary</h3>

                <div class="summary-row">
                    <span style="color: #64748b;">Order Number</span>
                    <span style="font-weight: 600; font-family: monospace;">{{ $order->order_number }}</span>
                </div>

                <div class="summary-row">
                    <span style="color: #64748b;">Order Date</span>
                    <span style="font-weight: 600;">{{ $order->created_at->format('M j, Y g:i A') }}</span>
                </div>

                <div class="summary-row">
                    <span style="color: #64748b;">Subtotal</span>
                    <span style="font-weight: 600;">GH₵{{ number_format($order->subtotal, 2) }}</span>
                </div>

                @if($order->service_fee > 0)
                <div class="summary-row">
                    <span style="color: #64748b;">Service Fee</span>
                    <span style="font-weight: 600;">GH₵{{ number_format($order->service_fee, 2) }}</span>
                </div>
                @endif

                @if($order->processing_fee > 0)
                <div class="summary-row">
                    <span style="color: #64748b;">Processing Fee</span>
                    <span style="font-weight: 600;">GH₵{{ number_format($order->processing_fee, 2) }}</span>
                </div>
                @endif

                <div class="summary-total">
                    <span>Total Paid</span>
                    <span>{{ $order->formatted_total }}</span>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="alert">
                <strong>Important Notes</strong>
                <ul>
                    @if($order->event->location_type === 'venue')
                    <li>Please arrive at least 15 minutes before the event starts</li>
                    <li>Present your QR code or ticket code at the entrance for check-in</li>
                    @endif
                    @if($order->event->age_restriction)
                    <li>Age Restriction: {{ $order->event->age_restriction }}</li>
                    @endif
                    <li>Keep this email safe - you'll need it to access the event</li>
                    <li>Screenshots of QR codes are acceptable</li>
                </ul>
            </div>

            <!-- CTA Button -->
            <div class="cta-container">
                <a href="{{ route('events.show', $order->event->slug) }}" class="button">
                    View Event Details
                </a>
            </div>

            <!-- Organizer Info -->
            <div class="organizer-info">
                <p style="font-weight: 600; color: #1e293b; margin-bottom: 12px;">Questions? Contact the organizer:</p>
                <p><strong>{{ $order->event->company->name }}</strong></p>
                <p>Email: <a href="mailto:{{ $order->event->company->email }}" style="color: #4f46e5; text-decoration: none;">{{ $order->event->company->email }}</a></p>
                @if($order->event->company->phone)
                <p>Phone: {{ $order->event->company->phone }}</p>
                @endif
            </div>

            <p style="font-size: 16px; color: #1e293b;">See you at the event!</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-brand">9yt !Trybe</div>
            <p>Ghana's Premier Event Ticketing Platform</p>
            <div class="footer-links">
                <a href="{{ route('events.index') }}">Browse Events</a>
                <a href="{{ route('home') }}">Home</a>
            </div>
            <p>&copy; {{ date('Y') }} 9yt !Trybe. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
