<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .details {
            background-color: #F9FAFB;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #6366F1;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6B7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: bold;
            background-color: #FEF3C7;
            color: #92400E;
        }
        .sender-id-display {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            color: #6366F1;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 15px;
            background-color: #EEF2FF;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .alert-box {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚠️ New SMS Sender ID Request</h1>
        <p style="margin: 0; opacity: 0.9;">Admin Action Required</p>
    </div>

    <div class="content">
        <p>Hello Admin,</p>

        <p>A new SMS Sender ID request has been submitted and requires your review.</p>

        <div class="alert-box">
            <strong>⏰ Action Required:</strong> Please review and approve or reject this request as soon as possible.
        </div>

        <div class="sender-id-display">
            {{ $senderId->sender_id }}
        </div>

        <div class="details">
            <h3 style="margin-top: 0; color: #6366F1;">Request Details</h3>

            <p><strong>Sender ID:</strong> {{ $senderId->sender_id }}</p>

            <p><strong>Requested By:</strong> {{ $senderId->owner->name ?? 'N/A' }} ({{ class_basename($senderId->owner_type) }})</p>

            @if($senderId->owner && method_exists($senderId->owner, 'email'))
                <p><strong>Email:</strong> {{ $senderId->owner->email }}</p>
            @endif

            <p>
                <strong>Status:</strong>
                <span class="badge">PENDING REVIEW</span>
            </p>

            <p><strong>Purpose:</strong></p>
            <div style="background-color: #ffffff; padding: 12px; border-radius: 6px; border: 1px solid #E5E7EB; margin-top: 8px;">
                {{ $senderId->purpose }}
            </div>

            <p style="margin-bottom: 0;"><strong>Submitted At:</strong> {{ $senderId->created_at->format('F j, Y g:i A') }}</p>
        </div>

        <div style="background-color: #EFF6FF; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #1E40AF;">💡 Review Guidelines</h4>
            <ul style="margin: 10px 0; padding-left: 20px; color: #374151;">
                <li>Sender ID should be relevant to the organization</li>
                <li>Maximum 15 characters (alphanumeric only)</li>
                <li>Should not impersonate other brands or services</li>
                <li>Purpose should clearly explain intended usage</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('admin.sms.sender-ids') }}" class="button" style="color: white;">
                Review Sender ID Request
            </a>
        </div>

        <p style="color: #6B7280; font-size: 14px; margin-top: 20px;">
            You can approve or reject this request from the Admin SMS Management dashboard.
        </p>
    </div>

    <div class="footer">
        <p>This is an automated notification from the SMS Management System.</p>
        <p style="margin-top: 5px;">
            <a href="{{ route('admin.sms.sender-ids') }}" style="color: #6366F1; text-decoration: none;">View All Sender ID Requests</a>
        </p>
    </div>
</body>
</html>
