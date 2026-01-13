@extends($layout ?? 'layouts.company')

@php
    $surveyRoutePrefix = $surveyRoutePrefix ?? 'organization.surveys';
    $isAdmin = $isAdmin ?? false;
@endphp

@section('title', 'Response Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route($surveyRoutePrefix . '.responses.index', $survey) }}" class="text-indigo-600 hover:text-indigo-800 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Response Details</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $response->getRespondentDisplayName() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Response Info -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Submitted</p>
                <p class="text-base font-semibold text-gray-900">{{ $response->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Time Taken</p>
                <p class="text-base font-semibold text-gray-900">{{ $response->getTimeTakenFormatted() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                @if($response->is_completed)
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                @else
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Incomplete</span>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-500">Device</p>
                <p class="text-base font-semibold text-gray-900">{{ ucfirst($response->device_type ?? 'Unknown') }} {{ $response->getDeviceIcon() }}</p>
            </div>
        </div>
    </div>

    <!-- Answers -->
    <div class="space-y-6">
        @foreach($response->answers as $answer)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ $answer->question->question_text ?? 'Question not found' }}</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-900">{{ $answer->getDisplayValue() }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
