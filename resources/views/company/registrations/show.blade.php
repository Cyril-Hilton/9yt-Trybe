@extends('layouts.company')

@section('title', 'Registration Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('organization.conferences.registrations.index', $conference) }}" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
            ← Back to Registrations
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Registration Details</h1>
        <p class="mt-2 text-gray-600">{{ $conference->title }}</p>
    </div>
    
    <!-- Main Registration Info -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $registration->name }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $registration->email }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $registration->phone }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Registration ID</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $registration->unique_id ?? 'N/A' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Attendance Type</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $registration->attendance_type === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($registration->attendance_type) }}
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Registration Date</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $registration->created_at->format('M j, Y H:i') }}</dd>
                </div>

                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Attendance Status</dt>
                    <dd class="mt-1">
                        @if($registration->attended)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Attended at {{ $registration->attended_at->format('M j, Y H:i') }}
                            </span>
                        @else
                            <form action="{{ route('organization.conferences.registrations.mark-attendance', [$conference, $registration]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                    Mark as Attended
                                </button>
                            </form>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Delete Section -->
        <div class="px-6 py-4 bg-gray-50">
            <form action="{{ route('organization.conferences.registrations.destroy', [$conference, $registration]) }}" 
                  method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this registration? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete Registration
                </button>
            </form>
        </div>
    </div>

    <!-- Custom Fields Data (if any) -->
    @if($registration->custom_data && count($registration->custom_data) > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($registration->custom_data as $fieldName => $value)
                @php
                    $field = $conference->customFields->where('field_name', $fieldName)->first();
                    $label = $field ? $field->label : ucfirst(str_replace('_', ' ', $fieldName));
                @endphp
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ $label }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if(is_array($value))
                            {{ implode(', ', $value) }}
                        @else
                            {{ $value ?: 'N/A' }}
                        @endif
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>
    @endif

    <!-- Legacy Additional Fields (backward compatibility) -->
    @if($registration->additional_fields)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Additional Information (Legacy)</h3>
        <div class="bg-gray-50 rounded p-4">
            <dl class="grid grid-cols-1 gap-y-4">
                @foreach($registration->additional_fields as $field => $value)
                    <div>
                        <dt class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $field)) }}</dt>
                        <dd class="mt-1 text-sm text-gray-700">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('organization.conferences.form-builder.index', $conference) }}" 
               class="block w-full text-center bg-purple-50 text-purple-700 px-4 py-2 rounded-lg hover:bg-purple-100 transition">
                📝 Customize Form Fields
            </a>
            <a href="{{ $conference->public_url }}" target="_blank" 
               class="block w-full text-center bg-indigo-50 text-indigo-700 px-4 py-2 rounded-lg hover:bg-indigo-100 transition">
                View Public Form
            </a>
            <button onclick="copyLink('{{ $conference->public_url }}')" 
                    class="block w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                Copy Registration Link
            </button>
            <a href="{{ route('organization.conferences.bulk-email', $conference) }}" 
               class="block w-full text-center bg-green-50 text-green-700 px-4 py-2 rounded-lg hover:bg-green-100 transition">
                Send Bulk Email
            </a>
        </div>
    </div>
</div>

<script>
function copyLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        alert('Link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endsection