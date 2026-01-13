<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class AdminSurveyResponseController extends Controller
{
    public function index(Request $request, Survey $survey)
    {
        $query = $survey->responses()->with('survey');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('respondent_name', 'like', '%' . $request->search . '%')
                    ->orWhere('respondent_email', 'like', '%' . $request->search . '%')
                    ->orWhere('respondent_identifier', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->where('is_completed', true);
            } elseif ($request->status === 'incomplete') {
                $query->where('is_completed', false);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $responses = $query->latest()->paginate(20);

        return view('company.surveys.responses.index', [
            'survey' => $survey,
            'responses' => $responses,
            'isAdmin' => true,
            'layout' => 'layouts.admin',
            'surveyRoutePrefix' => 'admin.surveys',
        ]);
    }

    public function show(Survey $survey, SurveyResponse $response)
    {
        if ($response->survey_id !== $survey->id) {
            abort(403);
        }

        $response->load(['answers.question', 'survey']);

        return view('company.surveys.responses.show', [
            'survey' => $survey,
            'response' => $response,
            'isAdmin' => true,
            'layout' => 'layouts.admin',
            'surveyRoutePrefix' => 'admin.surveys',
        ]);
    }

    public function destroy(Survey $survey, SurveyResponse $response)
    {
        if ($response->survey_id !== $survey->id) {
            abort(403);
        }

        if ($response->is_completed) {
            $survey->decrement('responses_count');
            $survey->updateCompletionRate();
        }

        $response->delete();

        return back()->with('success', 'Response deleted successfully!');
    }

    public function bulkDelete(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'response_ids' => ['required', 'array'],
            'response_ids.*' => ['exists:survey_responses,id'],
        ]);

        $deletedCount = SurveyResponse::whereIn('id', $validated['response_ids'])
            ->where('survey_id', $survey->id)
            ->delete();

        $survey->responses_count = $survey->completedResponses()->count();
        $survey->updateCompletionRate();
        $survey->save();

        return back()->with('success', "{$deletedCount} responses deleted successfully!");
    }

    public function export(Request $request, Survey $survey)
    {
        $format = $request->get('format', 'csv');

        return app(\App\Services\SurveyExportService::class)->exportResponses($survey, $format);
    }
}
