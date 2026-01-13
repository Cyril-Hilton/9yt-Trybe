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
        .message {
            background-color: #F9FAFB;
            padding: 20px;
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
        <h1>{{ $conference->title }}</h1>
    </div>

    <div class="content">
        <p>Dear {{ $registration->name }},</p>

        <div class="message">
            {!! nl2br(e($emailMessage)) !!}
        </div>

        <p>Best regards,<br>
        <strong>{{ $companyName }}</strong></p>
    </div>

    <div class="footer">
        <p>This email was sent to you because you registered for {{ $conference->title }}.</p>
        <p>If you have any questions, please contact {{ $companyName }}.</p>
    </div>
</body>
</html>