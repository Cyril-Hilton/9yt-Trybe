<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $survey->meta_title ?: $survey->title }}</title>
    <meta name="description" content="{{ $survey->meta_description ?: Str::limit(strip_tags($survey->description ?? ''), 155) }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .survey-theme { --theme-color: {{ $survey->theme_color }}; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen py-12 survey-theme">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-t-2xl shadow-xl p-8 text-center" style="background: linear-gradient(135deg, {{ $survey->theme_color }} 0%, {{ $survey->theme_color }}dd 100%)">
            <h1 class="text-3xl font-bold text-white mb-3">{{ $survey->title }}</h1>
            @if($survey->description)
                <p class="text-white text-opacity-90">{{ $survey->description }}</p>
            @endif
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('survey.submit', $survey->slug) }}" enctype="multipart/form-data"
              class="bg-white shadow-xl p-8 space-y-8" x-data="{ currentStep: 1, totalSteps: {{ $questions->count() }} }">
            @csrf

            @if(!$survey->allow_anonymous)
                <div class="pb-8 border-b-4 border-indigo-100 bg-indigo-50 rounded-xl p-6 -mx-2">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="h-6 w-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Your Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Name *
                            </label>
                            <input type="text" name="respondent_name" required
                                   class="w-full rounded-xl border-3 border-gray-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-200 px-5 py-4 text-gray-900 font-medium shadow-md transition-all duration-200 hover:border-indigo-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Email *
                            </label>
                            <input type="email" name="respondent_email" required placeholder="your.email@example.com"
                                   class="w-full rounded-xl border-3 border-gray-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-200 px-5 py-4 text-gray-900 font-medium shadow-md transition-all duration-200 hover:border-indigo-500 bg-white">
                        </div>
                    </div>
                </div>
            @else
                <div class="pb-8 border-b-4 border-gray-200 bg-gray-50 rounded-xl p-6 -mx-2">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                        <svg class="h-6 w-6 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Your Information
                        <span class="ml-2 text-sm text-gray-500 font-normal">(Optional)</span>
                    </h3>
                    <p class="text-sm text-gray-600 mb-6">Help us personalize your experience</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Name</label>
                            <input type="text" name="respondent_name" placeholder="Enter your name"
                                   class="w-full rounded-xl border-3 border-gray-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-200 px-5 py-4 text-gray-900 font-medium shadow-md transition-all duration-200 hover:border-indigo-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Email</label>
                            <input type="email" name="respondent_email" placeholder="your.email@example.com"
                                   class="w-full rounded-xl border-3 border-gray-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-200 px-5 py-4 text-gray-900 font-medium shadow-md transition-all duration-200 hover:border-indigo-500 bg-white">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Questions -->
            @foreach($questions as $index => $question)
                <div class="space-y-4 bg-gray-50 rounded-xl p-6 border-2 border-gray-200 hover:border-indigo-300 transition-all duration-200">
                    <label class="block text-lg font-bold text-gray-900 flex items-center">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold mr-3">
                            {{ $loop->iteration }}
                        </span>
                        {{ $question->question_text }}
                        @if($question->required)
                            <span class="text-red-600 ml-2 text-xl">*</span>
                        @endif
                    </label>
                    @if($question->description)
                        <p class="text-sm text-gray-600 ml-11 -mt-2">{{ $question->description }}</p>
                    @endif

                    @php $fieldName = 'question_' . $question->id; @endphp

                    @if($question->type === 'short_text' || $question->type === 'email' || $question->type === 'phone')
                        <input type="{{ $question->type === 'email' ? 'email' : 'text' }}"
                               name="{{ $fieldName }}"
                               {{ $question->required ? 'required' : '' }}
                               class="w-full rounded-xl border-3 border-gray-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-200 px-5 py-4 text-gray-900 font-medium shadow-md transition-all duration-200 hover:border-indigo-500 bg-white">

                    @elseif($question->type === 'long_text')
                        <textarea name="{{ $fieldName }}"
                                  {{ $question->required ? 'required' : '' }}
                                  rows="4"
                                  class="w-full rounded-xl border-3 border-gray-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-200 px-5 py-4 text-gray-900 font-medium shadow-md transition-all duration-200 hover:border-indigo-500 bg-white"></textarea>

                    @elseif($question->type === 'single_choice' || $question->type === 'dropdown')
                        @if($question->type === 'dropdown')
                            <select name="{{ $fieldName }}"
                                    {{ $question->required ? 'required' : '' }}
                                    class="w-full rounded-xl border-3 border-gray-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-200 px-5 py-4 text-gray-900 font-medium shadow-md transition-all duration-200 hover:border-indigo-500 bg-white">
                                <option value="">Select an option</option>
                                @foreach($question->getOptionsArray() as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="space-y-2">
                                @foreach($question->getOptionsArray() as $option)
                                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="{{ $fieldName }}" value="{{ $option }}"
                                               {{ $question->required ? 'required' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-700">{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                    @elseif($question->type === 'multiple_choice')
                        <div class="space-y-2">
                            @foreach($question->getOptionsArray() as $option)
                                <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="{{ $fieldName }}[]" value="{{ $option }}"
                                           class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500">
                                    <span class="ml-3 text-gray-700">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>

                    @elseif($question->type === 'linear_scale' || $question->type === 'rating')
                        <div class="flex flex-col items-center space-y-4 py-4">
                            <div class="flex items-center space-x-2">
                                @for($i = $question->scale_min ?? 1; $i <= ($question->scale_max ?? 5); $i++)
                                    <label class="flex flex-col items-center cursor-pointer">
                                        <input type="radio" name="{{ $fieldName }}" value="{{ $i }}"
                                               {{ $question->required ? 'required' : '' }}
                                               class="sr-only peer">
                                        <div class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center font-semibold text-gray-700 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 hover:border-indigo-400 transition-all">
                                            {{ $i }}
                                        </div>
                                    </label>
                                @endfor
                            </div>
                            @if($question->scale_min_label || $question->scale_max_label)
                                <div class="flex justify-between w-full text-sm text-gray-600">
                                    <span>{{ $question->scale_min_label }}</span>
                                    <span>{{ $question->scale_max_label }}</span>
                                </div>
                            @endif
                        </div>

                    @elseif($question->type === 'yes_no')
                        <div class="flex space-x-4">
                            <label class="flex items-center p-4 border-2 rounded-lg hover:bg-gray-50 cursor-pointer flex-1">
                                <input type="radio" name="{{ $fieldName }}" value="Yes"
                                       {{ $question->required ? 'required' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3 text-gray-700 font-medium">Yes</span>
                            </label>
                            <label class="flex items-center p-4 border-2 rounded-lg hover:bg-gray-50 cursor-pointer flex-1">
                                <input type="radio" name="{{ $fieldName }}" value="No"
                                       {{ $question->required ? 'required' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3 text-gray-700 font-medium">No</span>
                            </label>
                        </div>

                    @elseif($question->type === 'date')
                        <input type="date" name="{{ $fieldName }}"
                               {{ $question->required ? 'required' : '' }}
                               class="w-full rounded-xl border-3 border-gray-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-200 px-5 py-4 text-gray-900 font-medium shadow-md transition-all duration-200 hover:border-indigo-500 bg-white">

                    @elseif($question->type === 'number')
                        <input type="number" name="{{ $fieldName }}"
                               {{ $question->required ? 'required' : '' }}
                               class="w-full rounded-xl border-3 border-gray-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-200 px-5 py-4 text-gray-900 font-medium shadow-md transition-all duration-200 hover:border-indigo-500 bg-white">
                    @endif

                    @error($fieldName)
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <!-- Submit Button -->
            <div class="pt-6">
                <button type="submit"
                        class="w-full py-4 px-6 border border-transparent rounded-lg text-lg font-medium text-white hover:opacity-90 transition-opacity"
                        style="background-color: {{ $survey->theme_color }}">
                    {{ $survey->button_text ?? 'Submit Survey' }}
                </button>
            </div>
        </form>

        <div class="bg-white rounded-b-2xl shadow-xl p-4 text-center text-sm text-gray-500">
            <!-- Powered by Conference Portal -->
                Powered by {{ $survey->company ? $survey->company->name : '9yt !Trybe' }}
        </div>
    </div>
</body>
</html>
