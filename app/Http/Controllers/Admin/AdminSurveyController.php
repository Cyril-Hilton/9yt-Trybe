<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSurveyController extends Controller
{
    public function index(Request $request)
    {
        $query = Survey::with('company');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhereHas('company', function ($cq) use ($request) {
                        $cq->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $surveys = $query->withCount([
            'questions',
            'responses',
            'completedResponses as completed_responses_count'
        ])->latest()->paginate(12);

        $stats = [
            'total_surveys' => Survey::count(),
            'active_surveys' => Survey::where('status', 'active')->count(),
            'total_responses' => Survey::sum('responses_count'),
            'avg_completion_rate' => round(Survey::avg('completion_rate'), 1),
        ];

        $companies = Company::orderBy('name')->get();

        return view('company.surveys.index', [
            'surveys' => $surveys,
            'stats' => $stats,
            'companies' => $companies,
            'isAdmin' => true,
            'layout' => 'layouts.admin',
            'surveyRoutePrefix' => 'admin.surveys',
        ]);
    }

    public function create()
    {
        $templates = [
            [
                'key' => 'customer_satisfaction',
                'name' => 'Customer Satisfaction',
                'description' => 'Measure customer satisfaction with your product or service',
                'questions' => [
                    ['text' => 'How satisfied are you with our service?', 'type' => 'rating'],
                    ['text' => 'What did you like most?', 'type' => 'long_text'],
                    ['text' => 'What can we improve?', 'type' => 'long_text'],
                    ['text' => 'Would you recommend us to others?', 'type' => 'yes_no'],
                ]
            ],
            [
                'key' => 'event_feedback',
                'name' => 'Event Feedback',
                'description' => 'Gather feedback from event attendees',
                'questions' => [
                    ['text' => 'How would you rate the event?', 'type' => 'linear_scale'],
                    ['text' => 'What session did you enjoy most?', 'type' => 'short_text'],
                    ['text' => 'How likely are you to attend future events?', 'type' => 'linear_scale'],
                    ['text' => 'Additional comments', 'type' => 'long_text'],
                ]
            ],
            [
                'key' => 'product_feedback',
                'name' => 'Product Feedback',
                'description' => 'Collect product feedback from users',
                'questions' => [
                    ['text' => 'Which features do you use most?', 'type' => 'multiple_choice'],
                    ['text' => 'How easy is the product to use?', 'type' => 'linear_scale'],
                    ['text' => 'What features would you like to see?', 'type' => 'long_text'],
                    ['text' => 'Rate your overall experience', 'type' => 'rating'],
                ]
            ],
            [
                'key' => 'employee_engagement',
                'name' => 'Employee Engagement',
                'description' => 'Measure employee satisfaction and engagement',
                'questions' => [
                    ['text' => 'How satisfied are you with your role?', 'type' => 'linear_scale'],
                    ['text' => 'Do you feel valued at work?', 'type' => 'yes_no'],
                    ['text' => 'What would improve your work experience?', 'type' => 'long_text'],
                    ['text' => 'Rate your work-life balance', 'type' => 'rating'],
                ]
            ],
        ];

        $companies = Company::orderBy('name')->get();

        return view('company.surveys.create', [
            'templates' => $templates,
            'companies' => $companies,
            'isAdmin' => true,
            'layout' => 'layouts.admin',
            'surveyRoutePrefix' => 'admin.surveys',
            'defaultStatus' => 'active',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,active,paused,closed'],
            'allow_anonymous' => ['boolean'],
            'allow_multiple_responses' => ['boolean'],
            'show_progress_bar' => ['boolean'],
            'randomize_questions' => ['boolean'],
            'response_limit' => ['nullable', 'integer', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'theme_color' => ['nullable', 'string', 'max:7'],
            'button_text' => ['nullable', 'string', 'max:50'],
            'thank_you_message' => ['nullable', 'string'],
            'redirect_url' => ['nullable', 'url'],
            'template' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(8);
        $validated['company_id'] = $validated['company_id'] ?? null;
        $validated['response_limit'] = $validated['response_limit'] ?? 0;
        $validated['status'] = 'active';

        $survey = Survey::create($validated);

        if ($request->filled('template')) {
            $this->createTemplateQuestions($survey, $request->template);
        }

        return redirect()->route('admin.surveys.builder.index', $survey)
            ->with('success', 'Survey created successfully! Now add your questions.');
    }

    public function show(Survey $survey)
    {
        $survey->load(['questions', 'responses' => fn($q) => $q->latest()]);

        $stats = [
            'total_responses' => $survey->responses()->count(),
            'completed_responses' => $survey->completedResponses()->count(),
            'incomplete_responses' => $survey->responses()->where('is_completed', false)->count(),
            'views_count' => $survey->views_count,
            'completion_rate' => $survey->completion_rate,
            'average_time' => $survey->getAverageTimeFormatted(),
            'responses_remaining' => $survey->responses_remaining,
        ];

        return view('company.surveys.show', [
            'survey' => $survey,
            'stats' => $stats,
            'isAdmin' => true,
            'layout' => 'layouts.admin',
            'surveyRoutePrefix' => 'admin.surveys',
        ]);
    }

    public function edit(Survey $survey)
    {
        $companies = Company::orderBy('name')->get();

        return view('company.surveys.edit', [
            'survey' => $survey,
            'companies' => $companies,
            'isAdmin' => true,
            'layout' => 'layouts.admin',
            'surveyRoutePrefix' => 'admin.surveys',
        ]);
    }

    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,active,paused,closed'],
            'allow_anonymous' => ['boolean'],
            'allow_multiple_responses' => ['boolean'],
            'show_progress_bar' => ['boolean'],
            'randomize_questions' => ['boolean'],
            'response_limit' => ['nullable', 'integer', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'theme_color' => ['nullable', 'string', 'max:7'],
            'button_text' => ['nullable', 'string', 'max:50'],
            'thank_you_message' => ['nullable', 'string'],
            'redirect_url' => ['nullable', 'url'],
        ]);

        $validated['company_id'] = $validated['company_id'] ?? null;
        $validated['response_limit'] = $validated['response_limit'] ?? 0;

        $survey->update($validated);

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Survey updated successfully!');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey deleted successfully!');
    }

    public function duplicate(Survey $survey)
    {
        $newSurvey = $survey->replicate();
        $newSurvey->title = $survey->title . ' (Copy)';
        $newSurvey->slug = Str::slug($newSurvey->title) . '-' . Str::random(8);
        $newSurvey->status = 'draft';
        $newSurvey->views_count = 0;
        $newSurvey->responses_count = 0;
        $newSurvey->completion_rate = 0;
        $newSurvey->average_time_seconds = 0;
        $newSurvey->save();

        foreach ($survey->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->survey_id = $newSurvey->id;
            $newQuestion->save();
        }

        return redirect()->route('admin.surveys.show', $newSurvey)
            ->with('success', 'Survey duplicated successfully!');
    }

    private function createTemplateQuestions(Survey $survey, string $templateName): void
    {
        $templates = [
            'customer_satisfaction' => [
                ['question_text' => 'How satisfied are you with our service?', 'type' => 'rating', 'required' => true, 'order' => 1],
                ['question_text' => 'What did you like most?', 'type' => 'long_text', 'required' => false, 'order' => 2],
                ['question_text' => 'What can we improve?', 'type' => 'long_text', 'required' => false, 'order' => 3],
                ['question_text' => 'Would you recommend us to others?', 'type' => 'yes_no', 'required' => true, 'order' => 4],
            ],
            'event_feedback' => [
                ['question_text' => 'How would you rate the event?', 'type' => 'linear_scale', 'required' => true, 'order' => 1, 'scale_min' => 1, 'scale_max' => 10, 'scale_min_label' => 'Poor', 'scale_max_label' => 'Excellent'],
                ['question_text' => 'What session did you enjoy most?', 'type' => 'short_text', 'required' => false, 'order' => 2],
                ['question_text' => 'How likely are you to attend future events?', 'type' => 'linear_scale', 'required' => true, 'order' => 3, 'scale_min' => 1, 'scale_max' => 10, 'scale_min_label' => 'Not likely', 'scale_max_label' => 'Very likely'],
                ['question_text' => 'Additional comments', 'type' => 'long_text', 'required' => false, 'order' => 4],
            ],
            'product_feedback' => [
                ['question_text' => 'Which features do you use most?', 'type' => 'multiple_choice', 'required' => true, 'order' => 1, 'options' => ['Feature 1', 'Feature 2', 'Feature 3', 'Other']],
                ['question_text' => 'How easy is the product to use?', 'type' => 'linear_scale', 'required' => true, 'order' => 2, 'scale_min' => 1, 'scale_max' => 5, 'scale_min_label' => 'Very difficult', 'scale_max_label' => 'Very easy'],
                ['question_text' => 'What features would you like to see?', 'type' => 'long_text', 'required' => false, 'order' => 3],
                ['question_text' => 'Rate your overall experience', 'type' => 'rating', 'required' => true, 'order' => 4],
            ],
            'employee_engagement' => [
                ['question_text' => 'How satisfied are you with your role?', 'type' => 'linear_scale', 'required' => true, 'order' => 1, 'scale_min' => 1, 'scale_max' => 10, 'scale_min_label' => 'Not satisfied', 'scale_max_label' => 'Very satisfied'],
                ['question_text' => 'Do you feel valued at work?', 'type' => 'yes_no', 'required' => true, 'order' => 2],
                ['question_text' => 'What would improve your work experience?', 'type' => 'long_text', 'required' => false, 'order' => 3],
                ['question_text' => 'Rate your work-life balance', 'type' => 'rating', 'required' => true, 'order' => 4],
            ],
        ];

        if (isset($templates[$templateName])) {
            foreach ($templates[$templateName] as $questionData) {
                $questionData['survey_id'] = $survey->id;
                $survey->questions()->create($questionData);
            }
        }
    }
}
