@extends($layout ?? 'layouts.company')

@php
    $surveyRoutePrefix = $surveyRoutePrefix ?? 'organization.surveys';
    $isAdmin = $isAdmin ?? false;
    $defaultStatus = $defaultStatus ?? 'draft';
@endphp

@section('title', 'Create Survey')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route($surveyRoutePrefix . '.index') }}" class="text-indigo-600 hover:text-indigo-800 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Survey</h1>
                <p class="mt-1 text-sm text-gray-600">Start from a template or build from scratch</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="surveyCreateForm()">
        <!-- Templates Section -->
        <div class="lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Choose a Template</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Blank Template -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border-2 border-gray-300 cursor-pointer"
                     :class="{ 'ring-4 ring-indigo-500': selectedTemplate === '' }"
                     @click="selectTemplate('')">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-white rounded-lg mb-4">
                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Start from Scratch</h3>
                        <p class="text-sm text-gray-600">Build your own custom survey with complete flexibility</p>
                    </div>
                </div>

                <!-- Template Cards -->
                @foreach($templates as $template)
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border-2 border-gray-200 cursor-pointer transform hover:scale-105"
                         :class="{ 'ring-4 ring-indigo-500 border-indigo-500': selectedTemplate === '{{ $template['key'] }}' }"
                         @click="selectTemplate('{{ $template['key'] }}')">
                        <div class="p-6">
                            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg mb-4">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $template['name'] }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ $template['description'] }}</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ count($template['questions']) }} questions included
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Survey Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-2xl border-4 border-indigo-100 overflow-hidden sticky top-6"
                 x-show="showForm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100">

                <!-- Modern Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <svg class="h-7 w-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Survey Details
                    </h3>
                    <p class="text-indigo-100 text-sm mt-1">Configure your survey settings</p>
                </div>

                <form method="POST" action="{{ route($surveyRoutePrefix . '.store') }}" class="p-6 space-y-6">
                    @csrf

                    <input type="hidden" name="template" x-model="selectedTemplate">

                    @if($isAdmin)
                    <div>
                        <label for="company_id" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7v11a2 2 0 002 2h10a2 2 0 002-2V7M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2"/>
                            </svg>
                            Organizer (Optional)
                        </label>
                        <select name="company_id"
                                id="company_id"
                                class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 px-4 py-3 text-gray-900 font-medium shadow-sm hover:border-indigo-400 transition-all duration-200">
                            <option value="">Global (No Company)</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-600">Leave blank to create a global survey.</p>
                    </div>
                    @endif

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Survey Title *
                        </label>
                        <input type="text"
                               name="title"
                               id="title"
                               required
                               x-model="surveyTitle"
                               class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 px-4 py-3 text-gray-900 font-medium shadow-sm hover:border-indigo-400 transition-all duration-200"
                               placeholder="e.g., Customer Satisfaction Survey">
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            Description
                        </label>
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  x-model="surveyDescription"
                                  class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400 transition-all duration-200"
                                  placeholder="Describe your survey..."></textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    @if($isAdmin)
                    <input type="hidden" name="status" value="{{ old('status', $defaultStatus) }}">
                    <div class="rounded-xl border-2 border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">
                        Status: Active (auto-approved)
                    </div>
                    @else
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Status *
                        </label>
                        <select name="status"
                                id="status"
                                required
                                class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 px-4 py-3 text-gray-900 font-medium shadow-sm hover:border-indigo-400 transition-all duration-200">
                            <option value="draft" {{ old('status', $defaultStatus) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ old('status', $defaultStatus) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ old('status', $defaultStatus) === 'paused' ? 'selected' : '' }}>Paused</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    @endif

                    <!-- Settings -->
                    <div class="bg-indigo-50 rounded-xl p-5 border-2 border-indigo-200 space-y-3">
                        <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Survey Settings
                        </h4>
                        <label class="flex items-center cursor-pointer p-3 bg-white rounded-lg hover:bg-indigo-50 transition-all duration-200">
                            <input type="checkbox"
                                   name="allow_anonymous"
                                   value="1"
                                   {{ old('allow_anonymous', true) ? 'checked' : '' }}
                                   class="rounded-lg border-2 border-gray-400 text-indigo-600 focus:ring-2 focus:ring-indigo-500 w-5 h-5">
                            <span class="ml-3 text-sm font-semibold text-gray-800">Allow anonymous responses</span>
                        </label>

                        <label class="flex items-center cursor-pointer p-3 bg-white rounded-lg hover:bg-indigo-50 transition-all duration-200">
                            <input type="checkbox"
                                   name="allow_multiple_responses"
                                   value="1"
                                   {{ old('allow_multiple_responses') ? 'checked' : '' }}
                                   class="rounded-lg border-2 border-gray-400 text-indigo-600 focus:ring-2 focus:ring-indigo-500 w-5 h-5">
                            <span class="ml-3 text-sm font-semibold text-gray-800">Allow multiple responses</span>
                        </label>

                        <label class="flex items-center cursor-pointer p-3 bg-white rounded-lg hover:bg-indigo-50 transition-all duration-200">
                            <input type="checkbox"
                                   name="show_progress_bar"
                                   value="1"
                                   {{ old('show_progress_bar', true) ? 'checked' : '' }}
                                   class="rounded-lg border-2 border-gray-400 text-indigo-600 focus:ring-2 focus:ring-indigo-500 w-5 h-5">
                            <span class="ml-3 text-sm font-semibold text-gray-800">Show progress bar</span>
                        </label>
                    </div>

                    <!-- Response Limit -->
                    <div>
                        <label for="response_limit" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Response Limit
                        </label>
                        <input type="number"
                               name="response_limit"
                               id="response_limit"
                               min="0"
                               value="{{ old('response_limit', 0) }}"
                               class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 px-4 py-3 text-gray-900 font-medium shadow-sm hover:border-indigo-400 transition-all duration-200"
                               placeholder="0 = unlimited">
                        <p class="mt-2 text-xs text-gray-600 flex items-center">
                            <svg class="h-3 w-3 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Set to 0 for unlimited responses
                        </p>
                        @error('response_limit')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Theme Color -->
                    <div>
                        <label for="theme_color" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                            Theme Color
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="color"
                                   name="theme_color"
                                   id="theme_color"
                                   value="{{ old('theme_color', '#3b82f6') }}"
                                   class="h-12 w-20 rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 shadow-sm hover:border-indigo-400 transition-all duration-200 cursor-pointer">
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-700">Choose your survey theme</p>
                                <p class="text-xs text-gray-500 mt-1">This color will be used throughout the survey</p>
                            </div>
                        </div>
                        @error('theme_color')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full py-4 px-6 border-2 border-transparent rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 font-bold text-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center">
                        <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Create Survey
                    </button>
                </form>
            </div>

            <!-- Instructions -->
            <div class="mt-6 bg-blue-50 rounded-lg p-4"
                 x-show="!showForm"
                 x-transition>
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Select a template to continue</h3>
                        <p class="mt-2 text-sm text-blue-700">Choose a pre-built template or start from scratch to create your survey.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
function surveyCreateForm() {
    return {
        selectedTemplate: '{{ old('template', '') }}',
        showForm: {{ old('template') || old('title') ? 'true' : 'false' }},
        surveyTitle: '{{ old('title', '') }}',
        surveyDescription: '{{ old('description', '') }}',
        templates: @json($templates),

        selectTemplate(key) {
            this.selectedTemplate = key;
            this.showForm = true;
            
            if (key) {
                const template = this.templates.find(t => t.key === key);
                if (template) {
                    this.surveyTitle = template.name + ' Survey';
                    this.surveyDescription = template.description;
                }
            } else {
                this.surveyTitle = '';
                this.surveyDescription = '';
            }
        }
    }
}
</script>
