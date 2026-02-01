<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Complementary Ticket</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            color: #e0e7ff;
            margin: 10px 0 0 0;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin: 0 0 20px 0;
            font-weight: 600;
        }
        .message {
            color: #4b5563;
            line-height: 1.6;
            margin: 0 0 30px 0;
            font-size: 15px;
        }
        .ticket-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        .event-name {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 20px 0;
            text-align: center;
        }
        .ticket-details {
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #e0e7ff;
            font-size: 13px;
            font-weight: 500;
        }
        .detail-value {
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
        }
        .qr-section {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 20px 0;
        }
        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto 15px;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 8px;
        }
        .qr-code img {
            width: 100%;
            height: 100%;
        }
        .qr-text {
            color: #4b5563;
            font-size: 13px;
            margin: 10px 0 0 0;
        }
        .reference-code {
            background-color: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .reference-label {
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin: 0 0 8px 0;
        }
        .reference-value {
            color: #111827;
            font-size: 18px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }
        .important-info {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .important-info h3 {
            color: #92400e;
            margin: 0 0 12px 0;
            font-size: 16px;
            font-weight: 700;
        }
        .important-info ul {
            margin: 0;
            padding-left: 20px;
            color: #78350f;
            font-size: 14px;
            line-height: 1.8;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.6;
            margin: 5px 0;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
            .ticket-card {
                padding: 20px;
            }
            .event-name {
                font-size: 20px;
            }
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .email-container {
                max-width: 100%;
            }
            .header, .ticket-card {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .qr-code {
                page-break-inside: avoid;
            }
            .footer {
                page-break-before: avoid;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background: #f3f4f6; color: #1f2937;">
    <div class="email-container" style="background: #ffffff; color: #1f2937;">
        <!-- Header -->
        <div class="header" style="background: #4f46e5; color: #ffffff;">
            <h1>🎟️ Complementary Ticket</h1>
            <p>You've been granted Complement Access! for {{ $attendee->ticket->name }}</p>
        </div>

        <!-- Content -->
        <div class="content" style="background: #ffffff; color: #1f2937;">
            <p class="greeting">Hello {{ $attendee->attendee_name }},</p>

            <p class="message">
                Great news! You have been issued a complimentary ticket for <strong>{{ $event->title }}</strong>.
                This is a {{ $attendee->ticket->name }} ticket issued for you.
            </p>

            <!-- Event Flier -->
            @if($event->flier_path)
                <div style="text-align: center; margin: 30px 0;">
                    <img src="{{ $embeddedBannerCid ?? $event->flier_url }}" alt="{{ $event->title }}" style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                </div>
            @endif

            <!-- Ticket Card -->
            <div class="ticket-card" style="background: #ffffff; color: #1e293b;">
                <h2 class="event-name">{{ $event->title }}</h2>

                <div class="ticket-details">
                    @if($event->start_date)
                        <div class="detail-row">
                            <span class="detail-label">Event Date</span>
                            <span class="detail-value">{{ $event->start_date->format('l, F d, Y') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Event Time</span>
                            <span class="detail-value">{{ $event->start_date->format('g:i A') }}</span>
                        </div>
                    @endif
                    @if($event->location_type === 'venue' && $event->venue_name)
                        <div class="detail-row">
                            <span class="detail-label">Venue</span>
                            <span class="detail-value">{{ $event->venue_name }}</span>
                        </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">Ticket Type</span>
                        <span class="detail-value">{{ $attendee->ticket->name }}</span>
                    </div>
                    @if($attendee->ticket->price > 0)
                        <div class="detail-row">
                            <span class="detail-label">Ticket Value</span>
                            <span class="detail-value">GHS {{ number_format($attendee->ticket->price, 2) }}</span>
                        </div>
                    @endif
                </div>

                <!-- QR Code Section -->
                <div class="qr-section">
                    <div class="qr-code">
                        @if(!empty($embeddedCid))
                            <img src="{{ $embeddedCid }}" alt="QR Code">
                        @else
                            @php
                                $qrCode = $attendee->qr_code_base64;
                                $isSvg = is_string($qrCode) && str_starts_with($qrCode, 'data:image/svg+xml');
                            @endphp
                            @if($qrCode && !$isSvg)
                                <img src="{{ $qrCode }}" alt="QR Code">
                            @elseif($attendee->qr_code_path && !str_ends_with($attendee->qr_code_path, '.svg'))
                                <img src="{{ asset('storage/' . $attendee->qr_code_path) }}" alt="QR Code">
                            @else
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($attendee->ticket_code) }}" alt="QR Code">
                            @endif
                        @endif
                    </div>
                    <p class="qr-text">Present this QR code at the event entrance</p>
                    @if(!empty($qrCodeUrl))
                        <p class="qr-text">If you cannot see the QR image, open it here: <a href="{{ $qrCodeUrl }}">Open QR code</a></p>
                    @endif
                </div>
            </div>

            <!-- Reference Code -->
            <div class="reference-code">
                <p class="reference-label">Ticket Reference</p>
                <p class="reference-value">{{ $attendee->ticket_code }}</p>
            </div>

            <!-- Important Information -->
            <div class="important-info">
                <h3>📋 Important Information</h3>
                <ul>
                    <li>This is a complimentary ticket issued for you</li>
                    <li>Please arrive at least 30 minutes before the event starts</li>
                    <li>Present the QR code above or your ticket reference at the entrance</li>
                    <li>You can show this email on your phone or print it out</li>
                    <li>Keep this email safe - you'll need it to gain entry</li>
                </ul>
            </div>

            <p class="message">
                If you have any questions or need assistance, please don't hesitate to contact us.
                We look forward to seeing you at the event!
            </p>

            <p class="message" style="margin-top: 30px;">
                Best regards,<br>
                <strong>Conference Portal Team</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Conference Portal</strong></p>
            <p>This ticket was issued as a complimentary courtesy ticket.</p>
            <p>For support, visit <a href="{{ url('/') }}">{{ url('/') }}</a></p>
            <p style="margin-top: 20px; font-size: 12px;">
                © {{ date('Y') }} Conference Portal. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
