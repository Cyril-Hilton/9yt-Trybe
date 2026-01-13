<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'respondent_name',
        'respondent_email',
        'respondent_identifier',
        'is_completed',
        'started_at',
        'completed_at',
        'time_taken_seconds',
        'ip_address',
        'user_agent',
        'referrer',
        'device_type',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_taken_seconds' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($response) {
            if (empty($response->started_at)) {
                $response->started_at = now();
            }
        });
    }

    /**
     * Relationships
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class, 'survey_response_id');
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Helper Methods
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'time_taken_seconds' => $this->calculateTimeTaken(),
        ]);

        // Update survey statistics
        $this->survey->incrementResponsesCount();
        $this->survey->updateAverageTime();
    }

    public function calculateTimeTaken(): int
    {
        if ($this->started_at) {
            return now()->diffInSeconds($this->started_at);
        }

        return 0;
    }

    public function getTimeTakenFormatted(): string
    {
        if (!$this->time_taken_seconds) {
            return 'N/A';
        }

        $minutes = floor($this->time_taken_seconds / 60);
        $seconds = $this->time_taken_seconds % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }

    public function isAnonymous(): bool
    {
        return empty($this->respondent_email);
    }

    public function getRespondentDisplayName(): string
    {
        if ($this->respondent_name) {
            return $this->respondent_name;
        }

        if ($this->respondent_email) {
            return $this->respondent_email;
        }

        return 'Anonymous #' . $this->id;
    }

    public function getDeviceIcon(): string
    {
        return match ($this->device_type) {
            'mobile' => '📱',
            'tablet' => '📱',
            'desktop' => '💻',
            default => '🖥️',
        };
    }

    /**
     * Get answer for a specific question
     */
    public function getAnswerForQuestion(int $questionId)
    {
        return $this->answers()
            ->where('survey_question_id', $questionId)
            ->first();
    }

    /**
     * Store answer for a question
     */
    public function storeAnswer(SurveyQuestion $question, $value): SurveyAnswer
    {
        $answerData = [
            'survey_response_id' => $this->id,
            'survey_question_id' => $question->id,
        ];

        // Store the value in the appropriate column based on question type
        switch ($question->type) {
            case 'multiple_choice':
                $answerData['answer_array'] = is_array($value) ? $value : [$value];
                break;

            case 'number':
            case 'linear_scale':
            case 'rating':
                $answerData['answer_number'] = $value;
                break;

            case 'date':
                $answerData['answer_date'] = $value;
                break;

            case 'time':
                $answerData['answer_time'] = $value;
                break;

            case 'file_upload':
                $answerData['answer_file_path'] = $value;
                break;

            default:
                $answerData['answer_text'] = $value;
                break;
        }

        // Update or create the answer
        return SurveyAnswer::updateOrCreate(
            [
                'survey_response_id' => $this->id,
                'survey_question_id' => $question->id,
            ],
            $answerData
        );
    }

    /**
     * Get all answers as an associative array
     */
    public function getAnswersArray(): array
    {
        $result = [];

        foreach ($this->answers as $answer) {
            $question = $answer->question;
            if ($question) {
                $result[$question->id] = [
                    'question' => $question->question_text,
                    'answer' => $answer->getDisplayValue(),
                    'type' => $question->type,
                ];
            }
        }

        return $result;
    }
}
