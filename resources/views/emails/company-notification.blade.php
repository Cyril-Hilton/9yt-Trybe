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
            background-color: #10B981;
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
            border-left: 4px solid #10B981;
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
            background-color: #10B981;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-online {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        .badge-in-person {
            background-color: #FEE2E2;
            color: #991B1B;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Registration!</h1>
    </div>

    <div class="content">
        <p>Hello,</p>

        <p>You have received a new registration for your conference: <strong>{{ $conference->title }}</strong></p>

        <div class="details">
            <h3 style="margin-top: 0; color: #10B981;">Registrant Details</h3>
            <p><strong>Name:</strong> {{ $registration->name }}</p>
            <p><strong>Email:</strong> {{ $registration->email }}</p>
            <p><strong>Phone:</strong> {{ $registration->phone }}</p>
            <p>
                <strong>Attendance Type:</strong> 
                <span class="badge {{ $registration->attendance_type === 'online' ? 'badge-online' : 'badge-in-person' }}">
                    {{ strtoupper(str_replace('_', '-', $registration->attendance_type)) }}
                </span>
            </p>
            @if($registration->attendance_type === 'in_person')
                <p><strong>Unique ID:</strong> <span style="font-family: 'Courier New', monospace; font-size: 18px; color: #4F46E5;">{{ $registration->unique_id }}</span></p>
            @endif
            <p><strong>Registered At:</strong> {{ $registration->created_at->format('F j, Y g:i A') }}</p>
        </div>

        <div style="background-color: #EFF6FF; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #1E40AF;">Current Statistics</h4>
            <p style="margin: 5px 0;">
                <strong>Total Registrations:</strong> {{ $conference->online_count + $conference->in_person_count }}
            </p>
            <p style="margin: 5px 0;">
                <strong>Online:</strong> {{ $conference->online_count }}
                @if($conference->online_limit > 0)
                    / {{ $conference->online_limit }}
                @endif
            </p>
            <p style="margin: 5px 0;">
                <strong>In-Person:</strong> {{ $conference->in_person_count }}
                @if($conference->in_person_limit > 0)
                    / {{ $conference->in_person_limit }}
                @endif
            </p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('organization.conferences.registrations.show', [$conference, $registration]) }}" class="button" style="color: white;">
                View Registration Details
            </a>
        </div>

        <p style="color: #6B7280; font-size: 14px; margin-top: 20px;">
            You can manage all registrations from your company dashboard.
        </p>
    </div>

    <div class="footer">
        <p>This is an automated notification from your Conference Registration Portal.</p>
    </div>
</body>
</html>