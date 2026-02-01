<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $conference->meta_title ?: ('Register for ' . $conference->title) }}</title>
    <meta name="description" content="{{ $conference->meta_description ?: Str::limit(strip_tags($conference->description ?? ''), 155) }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">

 <!-- Header Image Section -->
            <!-- @if($conference->header_image)
            <div class="mb-8 rounded-xl overflow-hidden shadow-lg">
                <img 
                    src="{{ asset('storage/' . $conference->header_image) }}" 
                    alt="{{ $conference->title }} header" 
                    class="w-full h-64 object-cover"
                    onerror="this.parentElement.style.display='none'"
                >
            </div>
            @endif -->

            <!-- Logo and Title Section -->
            <!-- <div class="text-center mb-8">
                @if($conference->logo)
                <div class="mb-6">
                    <img 
                        src="{{ asset('storage/' . $conference->logo) }}" 
                        alt="{{ $conference->title }} logo" 
                        class="h-24 mx-auto object-contain"
                        onerror="this.parentElement.style.display='none'"
                    >
                </div>
                @endif -->
           
                <!-- Header -->
            <div class="text-center mb-8 fade-in">
                @if($conference->company->logo)
                    <img src="{{ $conference->company->logo_url }}" alt="{{ $conference->company->name }}" class="h-16 mx-auto mb-4">
                @endif
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $conference->title }}</h1>
                <p class="text-lg text-gray-600">Hosted by {{ $conference->company->name }}</p>
            </div>

            <!-- Conference Details Card -->
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6 fade-in" style="animation-delay: 0.1s">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Conference Details</h2>
                
                @if($conference->description)
                    <p class="text-gray-700 mb-4">{{ $conference->description }}</p>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Date</p>
                            <p class="text-gray-600">{{ $conference->start_date->format('l, F j, Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Time</p>
                            <p class="text-gray-600">{{ $conference->start_date->format('g:i A') }} - {{ $conference->end_date->format('g:i A') }}</p>
                        </div>
                    </div>

                    @if($conference->venue)
                    <div class="flex items-start md:col-span-2">
                        <svg class="w-5 h-5 text-indigo-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Venue</p>
                            <p class="text-gray-600">{{ $conference->venue }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Availability Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 fade-in" style="animation-delay: 0.2s">
                @if($conference->online_limit > 0)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-900">Online Seats</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $conference->online_count }} / {{ $conference->online_limit }}</p>
                        </div>
                        <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    @if(!$conference->isOnlineAvailable())
                    <p class="text-red-600 text-sm mt-2 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Full
                    </p>
                    @else
                    <p class="text-green-600 text-sm mt-2 font-medium">✓ Available</p>
                    @endif
                </div>
                @endif

                @if($conference->in_person_limit > 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-900">In-Person Seats</p>
                            <p class="text-2xl font-bold text-red-600">{{ $conference->in_person_count }} / {{ $conference->in_person_limit }}</p>
                        </div>
                        <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    @if(!$conference->isInPersonAvailable())
                    <p class="text-red-600 text-sm mt-2 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Full
                    </p>
                    @else
                    <p class="text-green-600 text-sm mt-2 font-medium">✓ Available</p>
                    @endif
                </div>
                @endif
            </div>

            <!-- Registration Form -->
            <div class="bg-white shadow-lg rounded-lg p-8 fade-in" style="animation-delay: 0.3s">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Register Now</h2>

                @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('public.submit', $conference->slug) }}" method="POST" id="registrationForm">
                    @csrf

                    <!-- Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="John Doe"
                        >
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            required
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="john@example.com"
                        >
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <!-- <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="tel" 
                            name="phone" 
                            id="phone" 
                            required
                            value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="055 000 1111"
                        >
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div> -->
<!-- Phone -->
                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="tel" 
                            name="phone" 
                            id="phone" 
                            required
                            value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="055 000 1111"
                        >
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

               

                    <!-- Attendance Type -->
                    <!-- <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            How are you attending the conference? <span class="text-red-500">*</span>
                        </label> -->
                    <!-- Attendance Type -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            How are you attending the conference? <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="space-y-3">
                            @if($conference->isOnlineAvailable())
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                                <input 
                                    type="radio" 
                                    name="attendance_type" 
                                    value="online" 
                                    required
                                    {{ old('attendance_type') === 'online' ? 'checked' : '' }}
                                    class="w-5 h-5 text-indigo-600 focus:ring-indigo-500"
                                >
                                <div class="ml-4 flex-1">
                                    <span class="block font-medium text-gray-900">Online</span>
                                    <span class="block text-sm text-gray-600">Join virtually from anywhere</span>
                                </div>
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </label>
                            @endif

                            @if($conference->isInPersonAvailable())
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                                <input 
                                    type="radio" 
                                    name="attendance_type" 
                                    value="in_person" 
                                    required
                                    {{ old('attendance_type') === 'in_person' ? 'checked' : '' }}
                                    class="w-5 h-5 text-indigo-600 focus:ring-indigo-500"
                                >
                                <div class="ml-4 flex-1">
                                    <span class="block font-medium text-gray-900">In-Person</span>
                                    <span class="block text-sm text-gray-600">Attend physically at the venue</span>
                                </div>
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </label>
                            @endif
                        </div>

                        @error('attendance_type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @if(!$conference->isOnlineAvailable() && !$conference->isInPersonAvailable())
                        <div class="mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                            <p class="font-medium">Registration is currently full</p>
                            <p class="text-sm">All seats have been filled. Please contact the organizer for more information.</p>
                        </div>
                        @endif
                    </div>

                    <!-- In-Person Notice -->
                    <div id="inPersonNotice" class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4" style="display: none;">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-yellow-900">Important: In-Person Attendance</h4>
                                <p class="text-sm text-yellow-800 mt-1">
                                    If you select in-person attendance, you will receive a unique 4-digit ID via email. 
                                    Please keep this ID safe as you'll need to present it at the reception on the day of the conference.
                                </p>
                            </div>
                        </div>
                    </div>

     <!-- CUSTOM FIELDS - ADD THIS SECTION -->
                    @if($conference->customFields->count() > 0)
                    <div class="mb-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                        
                        @foreach($conference->customFields as $field)
                        <div class="mb-6">
                            <label for="custom_{{ $field->field_name }}" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $field->label }}
                                @if($field->required)
                                <span class="text-red-500">*</span>
                                @endif
                            </label>

                            @if($field->type === 'textarea')
                                <textarea 
                                    name="custom_{{ $field->field_name }}" 
                                    id="custom_{{ $field->field_name }}"
                                    {{ $field->required ? 'required' : '' }}
                                    rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="{{ $field->placeholder }}"
                                >{{ old('custom_' . $field->field_name) }}</textarea>

                            @elseif($field->type === 'select')
                                <select 
                                    name="custom_{{ $field->field_name }}" 
                                    id="custom_{{ $field->field_name }}"
                                    {{ $field->required ? 'required' : '' }}
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                >
                                    <option value="">-- Select --</option>
                                    @foreach($field->getOptionsArray() as $option)
                                    <option value="{{ $option }}" {{ old('custom_' . $field->field_name) === $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                    @endforeach
                                </select>

                            @elseif($field->type === 'radio')
                                <div class="space-y-2">
                                    @foreach($field->getOptionsArray() as $option)
                                    <label class="flex items-center">
                                        <input 
                                            type="radio" 
                                            name="custom_{{ $field->field_name }}" 
                                            value="{{ $option }}"
                                            {{ $field->required ? 'required' : '' }}
                                            {{ old('custom_' . $field->field_name) === $option ? 'checked' : '' }}
                                            class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        >
                                        <span class="ml-2 text-gray-700">{{ $option }}</span>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($field->type === 'checkbox')
                                <div class="space-y-2">
                                    @foreach($field->getOptionsArray() as $option)
                                    <label class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            name="custom_{{ $field->field_name }}[]" 
                                            value="{{ $option }}"
                                            {{ in_array($option, old('custom_' . $field->field_name, [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        >
                                        <span class="ml-2 text-gray-700">{{ $option }}</span>
                                    </label>
                                    @endforeach
                                </div>

                            @else
                                <input 
                                    type="{{ $field->type }}" 
                                    name="custom_{{ $field->field_name }}" 
                                    id="custom_{{ $field->field_name }}"
                                    {{ $field->required ? 'required' : '' }}
                                    value="{{ old('custom_' . $field->field_name) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="{{ $field->placeholder }}"
                                >
                            @endif

                            @if($field->help_text)
                            <p class="mt-1 text-sm text-gray-600">{{ $field->help_text }}</p>
                            @endif

                            @error('custom_' . $field->field_name)
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                    @endif
                    <!-- END CUSTOM FIELDS -->

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-indigo-600 text-white font-semibold py-4 px-6 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                        {{ (!$conference->isOnlineAvailable() && !$conference->isInPersonAvailable()) ? 'disabled' : '' }}
                    >
                        Complete Registration
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-600 text-sm fade-in" style="animation-delay: 0.4s">
                <p>Powered by {{ $conference->company->name }}</p>
                @if($conference->company->website)
                <a href="{{ $conference->company->website }}" target="_blank" class="text-indigo-600 hover:underline">
                    Visit our website
                </a>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Show in-person notice when in-person is selected
        const attendanceRadios = document.querySelectorAll('input[name="attendance_type"]');
        const inPersonNotice = document.getElementById('inPersonNotice');

        attendanceRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'in_person') {
                    inPersonNotice.style.display = 'block';
                } else {
                    inPersonNotice.style.display = 'none';
                }
            });
        });

        // Check on page load if in-person is already selected
        const selectedRadio = document.querySelector('input[name="attendance_type"]:checked');
        if (selectedRadio && selectedRadio.value === 'in_person') {
            inPersonNotice.style.display = 'block';
        }
    </script>
</body>
</html>
