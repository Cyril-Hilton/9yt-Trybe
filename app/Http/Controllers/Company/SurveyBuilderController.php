<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;

class SurveyBuilderController extends Controller
{
    public function index(Survey $survey)
    {
        if ($survey->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        $survey->load(['questions' => fn($q) => $q->orderBy('order')]);

        $questionTypes = SurveyQuestion::TYPES;

        return view('company.surveys.builder.index', compact('survey', 'questionTypes'));
    }

    public function store(Request $request, Survey $survey)
    {
        if ($survey->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:' . implode(',', array_keys(SurveyQuestion::TYPES))],
            'required' => ['boolean'],
            'options' => ['nullable', 'array'],
            'options.*' => ['string'],
            'scale_min' => ['nullable', 'integer'],
            'scale_max' => ['nullable', 'integer', 'gte:scale_min'],
            'scale_min_label' => ['nullable', 'string', 'max:50'],
            'scale_max_label' => ['nullable', 'string', 'max:50'],
            'allowed_file_types' => ['nullable', 'string'],
            'max_file_size' => ['nullable', 'integer', 'min:1'],
            'validation_rules' => ['nullable', 'array'],
        ]);

        // Set order to be last
        $validated['order'] = $survey->questions()->max('order') + 1;
        $validated['survey_id'] = $survey->id;

        // Clean up options - remove empty values
        if (isset($validated['options'])) {
            $validated['options'] = array_values(array_filter($validated['options']));
        }

        $question = SurveyQuestion::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question added successfully!',
                'question' => $question,
            ]);
        }

        return back()->with('success', 'Question added successfully!');
    }

    public function update(Request $request, Survey $survey, SurveyQuestion $question)
    {
        if ($survey->company_id !== auth()->guard('company')->id() || $question->survey_id !== $survey->id) {
            abort(403);
        }

        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:' . implode(',', array_keys(SurveyQuestion::TYPES))],
            'required' => ['boolean'],
            'options' => ['nullable', 'array'],
            'options.*' => ['string'],
            'scale_min' => ['nullable', 'integer'],
            'scale_max' => ['nullable', 'integer', 'gte:scale_min'],
            'scale_min_label' => ['nullable', 'string', 'max:50'],
            'scale_max_label' => ['nullable', 'string', 'max:50'],
            'allowed_file_types' => ['nullable', 'string'],
            'max_file_size' => ['nullable', 'integer', 'min:1'],
            'validation_rules' => ['nullable', 'array'],
        ]);

        // Clean up options - remove empty values
        if (isset($validated['options'])) {
            $validated['options'] = array_values(array_filter($validated['options']));
        }

        $question->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully!',
                'question' => $question->fresh(),
            ]);
        }

        return back()->with('success', 'Question updated successfully!');
    }

    public function destroy(Survey $survey, SurveyQuestion $question)
    {
        if ($survey->company_id !== auth()->guard('company')->id() || $question->survey_id !== $survey->id) {
            abort(403);
        }

        $question->delete();

        // Reorder remaining questions
        $survey->questions()->where('order', '>', $question->order)
            ->decrement('order');

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully!',
            ]);
        }

        return back()->with('success', 'Question deleted successfully!');
    }

    public function reorder(Request $request, Survey $survey)
    {
        if ($survey->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'questions' => ['required', 'array'],
            'questions.*.id' => ['required', 'exists:survey_questions,id'],
            'questions.*.order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['questions'] as $questionData) {
            SurveyQuestion::where('id', $questionData['id'])
                ->where('survey_id', $survey->id)
                ->update(['order' => $questionData['order']]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Questions reordered successfully!',
            ]);
        }

        return back()->with('success', 'Questions reordered successfully!');
    }

    public function duplicate(Survey $survey, SurveyQuestion $question)
    {
        if ($survey->company_id !== auth()->guard('company')->id() || $question->survey_id !== $survey->id) {
            abort(403);
        }

        $newQuestion = $question->replicate();
        $newQuestion->question_text = $question->question_text . ' (Copy)';
        $newQuestion->order = $survey->questions()->max('order') + 1;
        $newQuestion->save();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question duplicated successfully!',
                'question' => $newQuestion,
            ]);
        }

        return back()->with('success', 'Question duplicated successfully!');
    }
}
