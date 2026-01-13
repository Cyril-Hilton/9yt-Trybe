@extends($layout ?? 'layouts.company')

@php
    $surveyRoutePrefix = $surveyRoutePrefix ?? 'organization.surveys';
    $isAdmin = $isAdmin ?? false;
@endphp

@section('title', $survey->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route($surveyRoutePrefix . '.index') }}"
               class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors duration-200 group">
                <svg class="h-5 w-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Surveys
            </a>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $survey->title }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $survey->description }}</p>
                @if($isAdmin)
                    <p class="mt-1 text-xs text-gray-500">Organizer: {{ $survey->company->name ?? 'Global' }}</p>
                @endif
            </div>
            <div class="flex space-x-3">
                <a href="{{ route($surveyRoutePrefix . '.edit', $survey) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Edit
                </a>
                <a href="{{ route($surveyRoutePrefix . '.builder.index', $survey) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Edit Questions
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-600">Total Responses</p>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $stats['total_responses'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-600">Completed</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['completed_responses'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-600">Views</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['views_count'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-600">Completion Rate</p>
            <p class="text-3xl font-bold text-orange-600 mt-2">{{ number_format($stats['completion_rate'], 1) }}%</p>
        </div>
    </div>

    <!-- Public URL -->
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Share Your Survey</h3>
        <div class="flex items-center space-x-3">
            <input type="text" readonly value="{{ $survey->public_url }}"
                   class="flex-1 rounded-lg border-gray-300 bg-gray-50">
            <button onclick="navigator.clipboard.writeText('{{ $survey->public_url }}')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Copy Link
            </button>
            <a href="{{ $survey->public_url }}" target="_blank"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Preview
            </a>
        </div>
    </div>

    <!-- Recent Responses -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Responses</h3>
            <a href="{{ route($surveyRoutePrefix . '.responses.index', $survey) }}"
               class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                View All
            </a>
        </div>
        @if($survey->responses->count() > 0)
            <div class="space-y-4">
                @foreach($survey->responses->take(5) as $response)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $response->getRespondentDisplayName() }}</p>
                            <p class="text-sm text-gray-500">{{ $response->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route($surveyRoutePrefix . '.responses.show', [$survey, $response]) }}"
                           class="text-indigo-600 hover:text-indigo-800">
                            View Details
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-8">No responses yet</p>
        @endif
    </div>
</div>
@endsection
