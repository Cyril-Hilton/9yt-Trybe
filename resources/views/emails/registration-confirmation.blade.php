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
            background-color: #4F46E5;
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
        .unique-id-box {
            background-color: #F3F4F6;
            border: 2px dashed #4F46E5;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 8px;
        }
        .unique-id {
            font-size: 32px;
            font-weight: bold;
            color: #4F46E5;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .details {
            background-color: #F9FAFB;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #4F46E5;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6B7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Registration Confirmed!</h1>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $registration->name }}</strong>,</p>

        <p>Thank you for registering for <strong>{{ $conference->title }}</strong>. Your registration has been successful.</p>

        @if($registration->attendance_type === 'in_person')
            <div class="unique-id-box">
                <p style="margin: 0 0 10px 0; color: #6B7280;">Your Unique ID</p>
                <div class="unique-id">{{ $registration->unique_id }}</div>
                <p style="margin: 10px 0 0 0; color: #6B7280; font-size: 14px;">
                    Please keep this safe. You will be asked to present it at the reception.
                </p>
            </div>

            <div style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0; color: #92400E;">
                    <strong>Important:</strong> Please bring your Unique ID on the day of the conference. You will need to present it when checking in at the reception.
                </p>
            </div>
        @else
            <div style="background-color: #DBEAFE; border-left: 4px solid #3B82F6; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0; color: #1E40AF;">
                    <strong>ℹ️ Online Attendance:</strong> You have registered for online attendance. Further details and the meeting link will be sent to you closer to the conference date.
                </p>
            </div>
        @endif

        <div class="details">
            <h3 style="margin-top: 0; color: #4F46E5;">Conference Details</h3>
            <p><strong>Title:</strong> {{ $conference->title }}</p>
            @if($conference->description)
                <p><strong>Description:</strong> {{ $conference->description }}</p>
            @endif
            <p><strong>Date:</strong> {{ $conference->start_date->format('l, F j, Y') }}</p>
            <p><strong>Time:</strong> {{ $conference->start_date->format('g:i A') }} - {{ $conference->end_date->format('g:i A') }}</p>
            @if($conference->venue && $registration->attendance_type === 'in_person')
                <p><strong>Venue:</strong> {{ $conference->venue }}</p>
            @endif
            <p><strong>Attendance Type:</strong> {{ ucfirst(str_replace('_', '-', $registration->attendance_type)) }}</p>
        </div>

        <div class="details">
            <h3 style="margin-top: 0; color: #4F46E5;">Your Registration Details</h3>
            <p><strong>Name:</strong> {{ $registration->name }}</p>
            <p><strong>Email:</strong> {{ $registration->email }}</p>
            <p><strong>Phone:</strong> {{ $registration->phone }}</p>
            @if($registration->attendance_type === 'in_person')
                <p><strong>Unique ID:</strong> {{ $registration->unique_id }}</p>
            @endif
        </div>

        <p>We look forward to {{ $registration->attendance_type === 'in_person' ? 'seeing' : 'having' }} you at the conference!</p>

        <p>If you have any questions, please don't hesitate to contact us.</p>

        <p>Best regards,<br>
        <strong>{{ $companyName }}</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>If you did not register for this conference, please contact {{ $companyName }} immediately.</p>
    </div>
</body>
</html>