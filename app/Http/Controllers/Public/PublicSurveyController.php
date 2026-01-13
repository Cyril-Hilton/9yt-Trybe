<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicSurveyController extends Controller
{
    public function show(string $slug)
    {
        $survey = Survey::where('slug', $slug)
            ->with(['questions' => fn($q) => $q->orderBy('order')])
            ->firstOrFail();

        // Check if survey is accepting responses
        if (!$survey->isAcceptingResponses()) {
            return view('public.survey-closed', compact('survey'));
        }

        // Increment views count
        $survey->incrementViewsCount();

        // Check for existing response (for multiple response prevention)
        $existingResponse = null;
        if (!$survey->allow_multiple_responses) {
            $identifier = $this->getRespondentIdentifier($survey);
            $existingResponse = SurveyResponse::where('survey_id', $survey->id)
                ->where('respondent_identifier', $identifier)
                ->where('is_completed', true)
                ->first();

            if ($existingResponse) {
                return view('public.survey-already-submitted', compact('survey'));
            }
        }

        // Randomize questions if enabled
        $questions = $survey->questions;
        if ($survey->randomize_questions) {
            $questions = $questions->shuffle();
        }

        return view('public.survey-form', compact('survey', 'questions'));
    }

    public function submit(Request $request, string $slug)
    {
        $survey = Survey::where('slug', $slug)
            ->with(['questions'])
            ->firstOrFail();

        // Check if survey is accepting responses
        if (!$survey->isAcceptingResponses()) {
            return back()->with('error', 'This survey is no longer accepting responses.');
        }

        // Check for multiple responses
        if (!$survey->allow_multiple_responses) {
            $identifier = $this->getRespondentIdentifier($survey);
            $existingResponse = SurveyResponse::where('survey_id', $survey->id)
                ->where('respondent_identifier', $identifier)
                ->where('is_completed', true)
                ->first();

            if ($existingResponse) {
                return redirect()->route('survey.already-submitted', $slug);
            }
        }

        // Build validation rules
        $rules = [];
        $attributes = [];

        foreach ($survey->questions as $question) {
            $fieldName = 'question_' . $question->id;
            $rules[$fieldName] = $question->getValidationRulesForSubmission();
            $attributes[$fieldName] = $question->question_text;
        }

        // Add respondent info validation if not anonymous
        if (!$survey->allow_anonymous) {
            $rules['respondent_name'] = ['required', 'string', 'max:255'];
            $rules['respondent_email'] = ['required', 'email', 'max:255'];
        } else {
            $rules['respondent_name'] = ['nullable', 'string', 'max:255'];
            $rules['respondent_email'] = ['nullable', 'email', 'max:255'];
        }

        $validated = $request->validate($rules, [], $attributes);

        // Create response
        $response = SurveyResponse::create([
            'survey_id' => $survey->id,
            'respondent_name' => $validated['respondent_name'] ?? null,
            'respondent_email' => $validated['respondent_email'] ?? null,
            'respondent_identifier' => $this->getRespondentIdentifier($survey),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer'),
            'device_type' => $this->detectDeviceType($request),
        ]);

        // Store answers
        foreach ($survey->questions as $question) {
            $fieldName = 'question_' . $question->id;

            if ($request->has($fieldName)) {
                $value = $request->input($fieldName);

                // Handle file uploads
                if ($question->type === 'file_upload' && $request->hasFile($fieldName)) {
                    $value = $request->file($fieldName)->store('survey-uploads', 'public');
                }

                $response->storeAnswer($question, $value);
            }
        }

        // Mark response as completed
        $response->markAsCompleted();

        // Redirect to thank you page or custom URL
        if ($survey->redirect_url) {
            return redirect($survey->redirect_url);
        }

        return redirect()->route('survey.thank-you', $slug);
    }

    public function thankYou(string $slug)
    {
        $survey = Survey::where('slug', $slug)->firstOrFail();

        return view('public.survey-thank-you', compact('survey'));
    }

    public function alreadySubmitted(string $slug)
    {
        $survey = Survey::where('slug', $slug)->firstOrFail();

        return view('public.survey-already-submitted', compact('survey'));
    }

    private function getRespondentIdentifier(Survey $survey): string
    {
        // Create a unique identifier based on IP and user agent
        $identifier = request()->ip() . '|' . request()->userAgent();

        return hash('sha256', $identifier . $survey->id);
    }

    private function detectDeviceType(Request $request): string
    {
        $userAgent = $request->userAgent();

        if (preg_match('/mobile/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }
}
