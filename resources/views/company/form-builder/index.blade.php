@extends('layouts.company')

@section('title', 'Form Builder')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('organization.conferences.show', $conference) }}" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
            ← Back to Conference
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Form Builder</h1>
        <p class="mt-2 text-gray-600">{{ $conference->title }}</p>
        <p class="mt-1 text-sm text-gray-500">Create custom fields for your registration form</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add New Field Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Add Custom Field</h2>
                
                <form action="{{ route('organization.conferences.form-builder.store', $conference) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="label" class="block text-sm font-medium text-gray-700 mb-1">
                            Field Label <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label" id="label" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="e.g., Organization Name">
                        @error('label')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                            Field Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="text">Text</option>
                            <option value="email">Email</option>
                            <option value="tel">Phone</option>
                            <option value="textarea">Textarea</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="select">Dropdown</option>
                            <option value="radio">Radio Buttons</option>
                            <option value="checkbox">Checkboxes</option>
                        </select>
                    </div>

                    <div id="options-field" style="display: none;">
                        <label for="options" class="block text-sm font-medium text-gray-700 mb-1">
                            Options <span class="text-red-500">*</span>
                        </label>
                        <textarea name="options" id="options" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Option 1, Option 2, Option 3"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Separate options with commas</p>
                    </div>

                    <div>
                        <label for="placeholder" class="block text-sm font-medium text-gray-700 mb-1">
                            Placeholder
                        </label>
                        <input type="text" name="placeholder" id="placeholder"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="e.g., Enter your organization">
                    </div>

                    <div>
                        <label for="help_text" class="block text-sm font-medium text-gray-700 mb-1">
                            Help Text
                        </label>
                        <input type="text" name="help_text" id="help_text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Additional instructions">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="required" id="required" value="1"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="required" class="ml-2 block text-sm text-gray-900">
                            Required Field
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700">
                        Add Field
                    </button>
                </form>

                <!-- Preview Button -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('public.form', $conference->slug) }}" target="_blank"
                        class="block w-full text-center bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">
                        Preview Form
                    </a>
                </div>
            </div>
        </div>

        <!-- Existing Fields -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Form Fields</h2>
                    <p class="text-sm text-gray-600 mt-1">Default fields (Name, Email, Phone) are always included</p>
                </div>

                @if($fields->count() > 0)
                <div id="fields-list" class="divide-y divide-gray-200">
                    @foreach($fields as $field)
                    <div class="p-6 hover:bg-gray-50 transition" data-field-id="{{ $field->id }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-gray-400 cursor-move drag-handle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $field->label }}
                                            @if($field->required)
                                            <span class="text-red-500">*</span>
                                            @endif
                                        </h3>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ ucfirst($field->type) }}
                                            </span>
                                            <span class="text-sm text-gray-500">{{ $field->field_name }}</span>
                                        </div>
                                        @if($field->placeholder)
                                        <p class="text-sm text-gray-600 mt-1">Placeholder: {{ $field->placeholder }}</p>
                                        @endif
                                        @if($field->help_text)
                                        <p class="text-sm text-gray-600 mt-1">Help: {{ $field->help_text }}</p>
                                        @endif
                                        @if(in_array($field->type, ['select', 'radio', 'checkbox']) && $field->options)
                                        <p class="text-sm text-gray-600 mt-1">Options: {{ implode(', ', $field->getOptionsArray()) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button onclick="editField({{ $field->id }})" class="text-indigo-600 hover:text-indigo-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('organization.conferences.form-builder.destroy', [$conference, $field]) }}" method="POST" onsubmit="return confirm('Delete this field?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2">No custom fields yet</p>
                    <p class="text-sm">Add fields using the form on the left</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Show/hide options field based on field type
document.getElementById('type').addEventListener('change', function() {
    const optionsField = document.getElementById('options-field');
    const needsOptions = ['select', 'radio', 'checkbox'].includes(this.value);
    optionsField.style.display = needsOptions ? 'block' : 'none';
    document.getElementById('options').required = needsOptions;
});

// Drag and drop reordering
const fieldsList = document.getElementById('fields-list');
if (fieldsList) {
    new Sortable(fieldsList, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function(evt) {
            const fieldIds = Array.from(fieldsList.children).map(el => el.dataset.fieldId);
            
            fetch('{{ route('organization.conferences.form-builder.reorder', $conference) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ fields: fieldIds })
            });
        }
    });
}

function editField(fieldId) {
    alert('Edit modal coming soon! For now, please delete and recreate the field.');
}
</script>
@endsection