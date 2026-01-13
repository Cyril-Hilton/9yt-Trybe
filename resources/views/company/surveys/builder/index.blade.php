@extends($layout ?? 'layouts.company')

@php
    $surveyRoutePrefix = $surveyRoutePrefix ?? 'organization.surveys';
    $isAdmin = $isAdmin ?? false;
@endphp

@section('title', 'Survey Builder - ' . $survey->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="surveyBuilder()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route($surveyRoutePrefix . '.show', $survey) }}" class="text-indigo-600 hover:text-indigo-800 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $survey->title }}</h1>
                    <p class="mt-1 text-sm text-gray-600">Build your survey questions</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ $survey->public_url }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Preview Survey
                </a>
                <button @click="showAddQuestion = true"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Question
                </button>
            </div>
        </div>
    </div>

    @if($survey->questions->count() > 0)
        <!-- Questions List -->
        <div class="space-y-4 mb-8">
            @foreach($survey->questions as $question)
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800">
                                    {{ $question->getTypeLabel() }}
                                </span>
                                @if($question->required)
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                        Required
                                    </span>
                                @endif
                                <span class="text-xs text-gray-500">Question #{{ $loop->iteration }}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $question->question_text }}</h3>
                            @if($question->description)
                                <p class="mt-1 text-sm text-gray-600">{{ $question->description }}</p>
                            @endif
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button @click="editQuestion({{ $question->id }})"
                                    class="text-gray-400 hover:text-indigo-600 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <form method="POST" action="{{ route($surveyRoutePrefix . '.builder.destroy', [$survey, $question]) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this question?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Question Preview -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        @if($question->isChoiceType())
                            <div class="space-y-2">
                                @foreach($question->getOptionsArray() as $option)
                                    <label class="flex items-center">
                                        <input type="{{ $question->type === 'multiple_choice' ? 'checkbox' : 'radio' }}"
                                               disabled
                                               class="rounded border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($question->isScaleType())
                            <div class="flex items-center justify-between">
                                @if($question->scale_min_label)
                                    <span class="text-sm text-gray-600">{{ $question->scale_min_label }}</span>
                                @endif
                                <div class="flex space-x-2">
                                    @for($i = $question->scale_min ?? 1; $i <= ($question->scale_max ?? 5); $i++)
                                        <button disabled class="w-10 h-10 rounded-full border-2 border-gray-300 text-sm font-medium text-gray-700">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                @if($question->scale_max_label)
                                    <span class="text-sm text-gray-600">{{ $question->scale_max_label }}</span>
                                @endif
                            </div>
                        @else
                            <input type="text" disabled placeholder="Answer preview..." class="w-full rounded-lg border-gray-300 bg-white">
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No questions yet</h3>
            <p class="mt-2 text-sm text-gray-500">Get started by adding your first question!</p>
            <button @click="showAddQuestion = true"
                    class="mt-6 inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700">
                Add Your First Question
            </button>
        </div>
    @endif

    <!-- Add Question Modal -->
    <div x-show="showAddQuestion"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddQuestion"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"
                 @click="showAddQuestion = false"
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showAddQuestion"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border-4 border-indigo-100">
                <form method="POST" action="{{ route($surveyRoutePrefix . '.builder.store', $survey) }}" x-data="questionForm()">
                    @csrf
                    <!-- Modern Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                        <h3 class="text-2xl font-bold text-white flex items-center">
                            <svg class="h-7 w-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add New Question
                        </h3>
                        <p class="text-indigo-100 text-sm mt-1">Create a custom question for your survey</p>
                    </div>

                    <div class="bg-white px-6 py-6 max-h-[70vh] overflow-y-auto">

                        <div class="space-y-5">
                            <!-- Question Type -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Question Type *
                                </label>
                                <select name="type" x-model="questionType" required class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3 text-gray-900 font-medium shadow-sm hover:border-indigo-400">
                                    @foreach($questionTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Question Text -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Question Text *
                                </label>
                                <textarea name="question_text" required rows="3" class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400" placeholder="Enter your question..."></textarea>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Description (Optional)
                                </label>
                                <input type="text" name="description" class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400" placeholder="Add a helpful description...">
                            </div>

                            <!-- Required -->
                            <div class="bg-indigo-50 rounded-xl p-4 border-2 border-indigo-200">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="required" value="1" class="rounded-lg border-2 border-gray-400 text-indigo-600 focus:ring-2 focus:ring-indigo-500 w-5 h-5">
                                    <span class="ml-3 text-sm font-semibold text-gray-800">Mark as required question</span>
                                </label>
                            </div>

                            <!-- Options for choice-based questions -->
                            <div x-show="['single_choice', 'multiple_choice', 'dropdown'].includes(questionType)" class="bg-purple-50 rounded-xl p-5 border-2 border-purple-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Answer Options *
                                </label>
                                <div x-data="{ options: ['Option 1', 'Option 2'] }">
                                    <template x-for="(option, index) in options" :key="index">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-600 text-white text-xs font-bold">
                                                <span x-text="index + 1"></span>
                                            </span>
                                            <input type="text" :name="'options[' + index + ']'" x-model="options[index]"
                                                   class="flex-1 rounded-xl border-2 border-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 px-4 py-2 shadow-sm"
                                                   placeholder="Enter option text">
                                            <button type="button" @click="options.splice(index, 1)" x-show="options.length > 1"
                                                    class="p-2 text-red-600 hover:text-white hover:bg-red-600 rounded-lg transition-all duration-200">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="options.push('Option ' + (options.length + 1))"
                                            class="mt-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-200 flex items-center text-sm font-semibold shadow-md">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Option
                                    </button>
                                </div>
                            </div>

                            <!-- Scale settings -->
                            <div x-show="['linear_scale', 'rating'].includes(questionType)" class="bg-blue-50 rounded-xl p-5 border-2 border-blue-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Scale Configuration
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Minimum Value</label>
                                        <input type="number" name="scale_min" value="1" min="0"
                                               class="w-full rounded-xl border-2 border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 px-4 py-2 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Maximum Value</label>
                                        <input type="number" name="scale_max" value="5" min="1"
                                               class="w-full rounded-xl border-2 border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 px-4 py-2 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Min Label</label>
                                        <input type="text" name="scale_min_label" placeholder="e.g., Poor"
                                               class="w-full rounded-xl border-2 border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 px-4 py-2 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Max Label</label>
                                        <input type="text" name="scale_max_label" placeholder="e.g., Excellent"
                                               class="w-full rounded-xl border-2 border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 px-4 py-2 shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 sm:flex sm:flex-row-reverse border-t-2 border-gray-200">
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center rounded-xl border-2 border-transparent shadow-lg px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-base font-bold text-white hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 sm:ml-3 sm:w-auto transform hover:scale-105 transition-all duration-200">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Add Question
                        </button>
                        <button type="button"
                                @click="showAddQuestion = false"
                                class="mt-3 w-full inline-flex justify-center items-center rounded-xl border-2 border-gray-300 shadow-md px-6 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-100 hover:border-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-300 sm:mt-0 sm:w-auto transform hover:scale-105 transition-all duration-200">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Question Modal -->
    <div x-show="showEditQuestion"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditQuestion"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"
                 @click="showEditQuestion = false"
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditQuestion"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border-4 border-orange-100">
                <form x-bind:action="editFormAction" method="POST" x-init="questionType = $parent.editData.type || 'short_text'" x-data="{ questionType: 'short_text' }">
                    @csrf
                    @method('PUT')
                    <!-- Modern Header -->
                    <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-5">
                        <h3 class="text-2xl font-bold text-white flex items-center">
                            <svg class="h-7 w-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Question
                        </h3>
                        <p class="text-orange-100 text-sm mt-1">Update your survey question</p>
                    </div>

                    <div class="bg-white px-6 py-6 max-h-[70vh] overflow-y-auto">

                        <div class="space-y-4">
                            <!-- Question Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question Type *</label>
                                <select name="type" x-model="questionType" required class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400">
                                    @foreach($questionTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Question Text -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question Text *</label>
                                <textarea name="question_text" x-model="$parent.editData.question_text" required rows="2" class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400" placeholder="Enter your question..."></textarea>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                                <input type="text" name="description" x-model="$parent.editData.description" class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400" placeholder="Add a description...">
                            </div>

                            <!-- Required -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="required" value="1" x-bind:checked="$parent.editData.required" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Required question</span>
                                </label>
                            </div>

                            <!-- Options for choice-based questions -->
                            <div x-show="['single_choice', 'multiple_choice', 'dropdown'].includes(questionType)">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Options *</label>
                                <div x-init="options = $parent.$parent.editData.options && $parent.$parent.editData.options.length > 0 ? $parent.$parent.editData.options : ['Option 1', 'Option 2']" x-data="{ options: ['Option 1', 'Option 2'] }">
                                    <template x-for="(option, index) in options" :key="index">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <input type="text" :name="'options[' + index + ']'" x-model="options[index]"
                                                   class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                   placeholder="Option text">
                                            <button type="button" @click="options.splice(index, 1)" x-show="options.length > 1"
                                                    class="text-red-600 hover:text-red-800">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="options.push('Option ' + (options.length + 1))"
                                            class="mt-2 text-sm text-indigo-600 hover:text-indigo-800">
                                        + Add Option
                                    </button>
                                </div>
                            </div>

                            <!-- Scale settings -->
                            <div x-show="['linear_scale', 'rating'].includes(questionType)" class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Scale Min</label>
                                    <input type="number" name="scale_min" x-model="$parent.editData.scale_min" min="0"
                                           class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Scale Max</label>
                                    <input type="number" name="scale_max" x-model="$parent.editData.scale_max" min="1"
                                           class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Label</label>
                                    <input type="text" name="scale_min_label" x-model="$parent.editData.scale_min_label" placeholder="e.g., Poor"
                                           class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Label</label>
                                    <input type="text" name="scale_max_label" x-model="$parent.editData.scale_max_label" placeholder="e.g., Excellent"
                                           class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3 text-gray-900 shadow-sm hover:border-indigo-400">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 sm:flex sm:flex-row-reverse border-t-2 border-gray-200">
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center rounded-xl border-2 border-transparent shadow-lg px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-base font-bold text-white hover:from-orange-600 hover:to-red-700 focus:outline-none focus:ring-4 focus:ring-orange-300 sm:ml-3 sm:w-auto transform hover:scale-105 transition-all duration-200">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Question
                        </button>
                        <button type="button"
                                @click="showEditQuestion = false"
                                class="mt-3 w-full inline-flex justify-center items-center rounded-xl border-2 border-gray-300 shadow-md px-6 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-100 hover:border-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-300 sm:mt-0 sm:w-auto transform hover:scale-105 transition-all duration-200">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Question data from Laravel
const questionsData = @json($survey->questions);

function surveyBuilder() {
    return {
        showAddQuestion: false,
        showEditQuestion: false,
        editFormAction: '',
        editData: {
            question_text: '',
            description: '',
            required: false,
            options: [],
            scale_min: 1,
            scale_max: 5,
            scale_min_label: '',
            scale_max_label: ''
        },

        editQuestion(questionId) {
            const question = questionsData.find(q => q.id === questionId);
            if (question) {
                this.editData = {
                    question_text: question.question_text,
                    description: question.description || '',
                    required: question.required,
                    type: question.type,
                    options: question.options || [],
                    scale_min: question.scale_min || 1,
                    scale_max: question.scale_max || 5,
                    scale_min_label: question.scale_min_label || '',
                    scale_max_label: question.scale_max_label || ''
                };
                this.editFormAction = `/company/surveys/{{ $survey->id }}/builder/questions/${questionId}`;
                this.showEditQuestion = true;
            }
        }
    }
}

function questionForm() {
    return {
        questionType: 'short_text'
    }
}
</script>
@endsection
