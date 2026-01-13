<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $conference->title }} - Registrations</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .stats {
            background-color: #f5f5f5;
            padding: 12px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4F46E5;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .page-break {
            page-break-before: always;
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
    </style>
</head>
<body>
    <!-- Page 1: Basic Information -->
    <div class="header">
        <h1>{{ $conference->title }}</h1>
        <p>Registration Report - Generated on: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <div class="stats">
        <h3>Summary</h3>
        <p><strong>Total:</strong> {{ $stats['total_registrations'] }} | <strong>Online:</strong> {{ $stats['online_registrations'] }} | <strong>In-Person:</strong> {{ $stats['in_person_registrations'] }}</p>
    </div>

    <h2>Basic Registration Information</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Type</th>
                <th>ID</th>
                <th>Attended</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $registration)
            <tr>
                <td>{{ $registration->name }}</td>
                <td style="font-size: 8px;">{{ $registration->email }}</td>
                <td>{{ $registration->phone }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $registration->attendance_type)) }}</td>
                <td>{{ $registration->unique_id ?? '-' }}</td>
                <td>{{ $registration->attended ? 'Yes' : 'No' }}</td>
                <td>{{ $registration->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($conference->customFields->count() > 0)
    <!-- Page 2: Custom Fields -->
    <div class="page-break"></div>
    
    <h2>Custom Field Responses</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                @foreach($conference->customFields as $field)
                <th>{{ $field->label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $registration)
            <tr>
                <td>{{ $registration->name }}</td>
                @foreach($conference->customFields as $field)
                <td>
                    @php
                        $value = $registration->custom_data[$field->field_name] ?? '-';
                        echo is_array($value) ? implode(', ', $value) : $value;
                    @endphp
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>