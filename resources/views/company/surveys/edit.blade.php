@extends($layout ?? 'layouts.company')

@php
    $surveyRoutePrefix = $surveyRoutePrefix ?? 'organization.surveys';
    $isAdmin = $isAdmin ?? false;
@endphp

@section('title', 'Edit Survey')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showDeleteModal: false }">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route($surveyRoutePrefix . '.show', $survey) }}"
           class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors duration-200 group">
            <svg class="h-5 w-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Survey Details
        </a>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Survey</h1>
        <p class="mt-1 text-sm text-gray-600">Update your survey settings and configuration</p>
    </div>

    <!-- Main Form -->
    <div class="bg-white rounded-2xl shadow-2xl border-4 border-indigo-100 overflow-hidden">
        <!-- Modern Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
            <h3 class="text-2xl font-bold text-white flex items-center">
                <svg class="h-7 w-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Survey Settings
            </h3>
            <p class="text-indigo-100 text-sm mt-1">Manage your survey configuration and preferences</p>
        </div>

        <form method="POST" action="{{ route($surveyRoutePrefix . '.update', $survey) }}" class="p-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
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
                            <option value="{{ $company->id }}" {{ old('company_id', $survey->company_id) == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-600">Leave blank to make this survey global.</p>
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
                           value="{{ old('title', $survey->title) }}"
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
                              class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400 transition-all duration-200"
                              placeholder="Describe your survey...">{{ old('description', $survey->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

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
                        <option value="draft" {{ old('status', $survey->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $survey->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="paused" {{ old('status', $survey->status) === 'paused' ? 'selected' : '' }}>Paused</option>
                        <option value="closed" {{ old('status', $survey->status) === 'closed' ? 'selected' : '' }}>Closed</option>
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

                <!-- Settings Section -->
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
                               {{ old('allow_anonymous', $survey->allow_anonymous) ? 'checked' : '' }}
                               class="rounded-lg border-2 border-gray-400 text-indigo-600 focus:ring-2 focus:ring-indigo-500 w-5 h-5">
                        <span class="ml-3 text-sm font-semibold text-gray-800">Allow anonymous responses</span>
                    </label>

                    <label class="flex items-center cursor-pointer p-3 bg-white rounded-lg hover:bg-indigo-50 transition-all duration-200">
                        <input type="checkbox"
                               name="allow_multiple_responses"
                               value="1"
                               {{ old('allow_multiple_responses', $survey->allow_multiple_responses) ? 'checked' : '' }}
                               class="rounded-lg border-2 border-gray-400 text-indigo-600 focus:ring-2 focus:ring-indigo-500 w-5 h-5">
                        <span class="ml-3 text-sm font-semibold text-gray-800">Allow multiple responses</span>
                    </label>

                    <label class="flex items-center cursor-pointer p-3 bg-white rounded-lg hover:bg-indigo-50 transition-all duration-200">
                        <input type="checkbox"
                               name="show_progress_bar"
                               value="1"
                               {{ old('show_progress_bar', $survey->show_progress_bar) ? 'checked' : '' }}
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
                           value="{{ old('response_limit', $survey->response_limit) }}"
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
                               value="{{ old('theme_color', $survey->theme_color ?? '#3b82f6') }}"
                               class="h-12 w-20 rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 shadow-sm hover:border-indigo-400 transition-all duration-200 cursor-pointer">
                        <div class="flex-1">
                            <p class="text-xs font-medium text-gray-700">Choose your survey theme</p>
                            <p class="text-xs text-gray-500 mt-1">This color will be used throughout the survey</p>
                        </div>
                    </div>
                </div>

                <!-- Thank You Message -->
                <div>
                    <label for="thank_you_message" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        Thank You Message
                    </label>
                    <textarea name="thank_you_message"
                              id="thank_you_message"
                              rows="3"
                              class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400 transition-all duration-200"
                              placeholder="Thank you for completing our survey!">{{ old('thank_you_message', $survey->thank_you_message) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200">
                    <div class="flex space-x-3">
                        <a href="{{ route($surveyRoutePrefix . '.show', $survey) }}"
                           class="inline-flex items-center px-6 py-3 border-2 border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>

                        <button type="button"
                                @click="showDeleteModal = true"
                                class="inline-flex items-center px-6 py-3 border-2 border-red-300 rounded-xl text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-all duration-200 shadow-sm hover:shadow hover:border-red-400">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Survey
                        </button>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center px-8 py-4 border-2 border-transparent rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 font-bold text-base transition-all duration-200 transform hover:scale-105">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Survey
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="showDeleteModal = false"
                 aria-hidden="true"></div>

            <!-- Modal panel -->
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-4 border-red-200">

                <!-- Red gradient header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5">
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <svg class="h-7 w-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Delete Survey
                    </h3>
                    <p class="text-red-100 text-sm mt-1">This action cannot be undone</p>
                </div>

                <div class="bg-white px-6 py-5">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-100 sm:mx-0 sm:h-12 sm:w-12">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                Are you sure you want to delete this survey?
                            </h3>
                            <div class="mt-3">
                                <p class="text-sm text-gray-600 font-medium">
                                    You are about to permanently delete "<span class="text-gray-900 font-bold">{{ $survey->title }}</span>".
                                </p>
                                <p class="text-sm text-red-600 mt-3 bg-red-50 p-3 rounded-lg border-2 border-red-200 font-semibold">
                                    <span class="flex items-start gap-2">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span>All questions, responses, and analytics data will be permanently deleted. This action cannot be undone.</span>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse">
                    <form method="POST" action="{{ route($surveyRoutePrefix . '.destroy', $survey) }}" class="sm:ml-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center rounded-xl border-2 border-transparent shadow-lg px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-base font-bold text-white hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 sm:w-auto transition-all duration-200 transform hover:scale-105">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Yes, Delete Survey
                        </button>
                    </form>
                    <button type="button"
                            @click="showDeleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border-2 border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto transition-all duration-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
