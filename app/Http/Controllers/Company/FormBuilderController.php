<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Models\ConferenceField;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class FormBuilderController extends Controller
{
    use AuthorizesRequests;

    public function index(Conference $conference)
    {
        // Direct ownership check instead of policy
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }

        $fields = $conference->customFields()->orderBy('order')->get();

        return view('company.form-builder.index', compact('conference', 'fields'));
    }

    public function store(Request $request, Conference $conference)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:text,email,tel,textarea,number,date,select,checkbox,radio'],
            'required' => ['boolean'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'help_text' => ['nullable', 'string'],
            'options' => ['nullable', 'string'],
        ]);

        // Generate field name from label
        $fieldName = Str::slug($validated['label'], '_');
        
        // Ensure unique field name
        $counter = 1;
        $originalFieldName = $fieldName;
        while ($conference->customFields()->where('field_name', $fieldName)->exists()) {
            $fieldName = $originalFieldName . '_' . $counter;
            $counter++;
        }

        // Convert options string to array
        if (!empty($validated['options']) && in_array($validated['type'], ['select', 'radio', 'checkbox'])) {
            $options = array_map('trim', explode(',', $validated['options']));
            $validated['options'] = $options;
        } else {
            $validated['options'] = null;
        }

        $validated['field_name'] = $fieldName;
        $validated['order'] = $conference->customFields()->max('order') + 1;

        $conference->customFields()->create($validated);

        return back()->with('success', 'Custom field added successfully!');
    }

    public function update(Request $request, Conference $conference, ConferenceField $field)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }

        if ($field->conference_id !== $conference->id) {
            abort(404);
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'required' => ['boolean'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'help_text' => ['nullable', 'string'],
            'options' => ['nullable', 'string'],
        ]);

        // Convert options string to array
        if (!empty($validated['options']) && in_array($field->type, ['select', 'radio', 'checkbox'])) {
            $options = array_map('trim', explode(',', $validated['options']));
            $validated['options'] = $options;
        } else {
            $validated['options'] = null;
        }

        $field->update($validated);

        return back()->with('success', 'Custom field updated successfully!');
    }

    public function destroy(Conference $conference, ConferenceField $field)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }

        if ($field->conference_id !== $conference->id) {
            abort(404);
        }

        $field->delete();

        return back()->with('success', 'Custom field deleted successfully!');
    }

    public function reorder(Request $request, Conference $conference)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }

        $validated = $request->validate([
            'fields' => ['required', 'array'],
            'fields.*' => ['required', 'integer', 'exists:conference_fields,id'],
        ]);

        foreach ($validated['fields'] as $order => $fieldId) {
            ConferenceField::where('id', $fieldId)
                ->where('conference_id', $conference->id)
                ->update(['order' => $order]);
        }

        return response()->json(['success' => true]);
    }
}