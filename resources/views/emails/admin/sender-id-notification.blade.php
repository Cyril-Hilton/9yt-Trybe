<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Sender ID Request</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .alert-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin-bottom: 30px;
            border-radius: 6px;
        }
        .alert-box p {
            margin: 0;
            color: #92400e;
            font-weight: 600;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin: 30px 0;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            padding: 12px 0;
            font-weight: 600;
            color: #6b7280;
            width: 35%;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            padding: 12px 0 12px 20px;
            color: #111827;
            vertical-align: top;
        }
        .sender-id-display {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: bold;
            padding: 15px 20px;
            border-radius: 8px;
            text-align: center;
            letter-spacing: 2px;
            margin: 10px 0;
        }
        .button-container {
            text-align: center;
            margin: 40px 0 30px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3);
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(79, 70, 229, 0.4);
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .footer a {
            color: #4f46e5;
            text-decoration: none;
        }
        .badge {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>⚠️ New Sender ID Request</h1>
            <p>Admin Action Required</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="alert-box">
                <p>🔔 A new sender ID request requires your review and approval.</p>
            </div>

            <p>Hello Admin,</p>
            <p>A sender ID request has been submitted and is pending your review:</p>

            <!-- Sender ID Display -->
            <div class="sender-id-display">
                {{ $senderId->sender_id }}
            </div>

            <!-- Request Details -->
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Requestor:</div>
                    <div class="info-value">
                        @if($senderId->owner_type === 'App\\Models\\Company')
                            <strong>{{ $senderId->owner->company_name }}</strong><br>
                            {{ $senderId->owner->company_email }}
                        @else
                            <strong>{{ $senderId->owner->name }}</strong><br>
                            {{ $senderId->owner->email }}
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Type:</div>
                    <div class="info-value">
                        @if($senderId->owner_type === 'App\\Models\\Company')
                            <span class="badge">Company</span>
                        @else
                            <span class="badge">User</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Sender ID:</div>
                    <div class="info-value"><strong>{{ $senderId->sender_id }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Purpose:</div>
                    <div class="info-value">{{ $senderId->purpose }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Requested:</div>
                    <div class="info-value">{{ $senderId->created_at->format('F d, Y \a\t h:i A') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value"><span class="badge">⏳ Pending Review</span></div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="button-container">
                <a href="{{ url('/admin/sms/sender-ids') }}" class="button">
                    Review Request →
                </a>
            </div>

            <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
                Please review this sender ID request and take appropriate action (approve or reject) at your earliest convenience.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p><a href="{{ url('/') }}">Visit Website</a></p>
        </div>
    </div>
</body>
</html>
