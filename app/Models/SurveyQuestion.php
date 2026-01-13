<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'question_text',
        'description',
        'type',
        'required',
        'order',
        'options',
        'validation_rules',
        'scale_min',
        'scale_max',
        'scale_min_label',
        'scale_max_label',
        'conditional_logic',
        'allowed_file_types',
        'max_file_size',
    ];

    protected $casts = [
        'required' => 'boolean',
        'order' => 'integer',
        'options' => 'array',
        'validation_rules' => 'array',
        'scale_min' => 'integer',
        'scale_max' => 'integer',
        'conditional_logic' => 'array',
        'max_file_size' => 'integer',
    ];

    /**
     * Question types available
     */
    public const TYPES = [
        'short_text' => 'Short Text',
        'long_text' => 'Long Text',
        'single_choice' => 'Single Choice',
        'multiple_choice' => 'Multiple Choice',
        'dropdown' => 'Dropdown',
        'linear_scale' => 'Linear Scale',
        'rating' => 'Star Rating',
        'date' => 'Date',
        'time' => 'Time',
        'email' => 'Email',
        'phone' => 'Phone Number',
        'number' => 'Number',
        'file_upload' => 'File Upload',
        'yes_no' => 'Yes/No',
    ];

    /**
     * Relationships
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    /**
     * Helper Methods
     */
    public function isChoiceType(): bool
    {
        return in_array($this->type, ['single_choice', 'multiple_choice', 'dropdown']);
    }

    public function isScaleType(): bool
    {
        return in_array($this->type, ['linear_scale', 'rating']);
    }

    public function isTextType(): bool
    {
        return in_array($this->type, ['short_text', 'long_text']);
    }

    public function getTypeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function hasOptions(): bool
    {
        return !empty($this->options) && is_array($this->options);
    }

    public function getOptionsArray(): array
    {
        return $this->options ?? [];
    }

    public function hasConditionalLogic(): bool
    {
        return !empty($this->conditional_logic);
    }

    /**
     * Get answer statistics for this question
     */
    public function getAnswerStats(): array
    {
        $stats = [
            'total_answers' => 0,
            'unique_answers' => 0,
            'most_common' => null,
        ];

        if ($this->isChoiceType()) {
            $answers = $this->answers()
                ->whereNotNull('answer_text')
                ->get();

            $stats['total_answers'] = $answers->count();

            if ($this->type === 'multiple_choice') {
                // For multiple choice, count each option separately
                $allChoices = [];
                foreach ($answers as $answer) {
                    if ($answer->answer_array) {
                        $allChoices = array_merge($allChoices, $answer->answer_array);
                    }
                }
                $stats['choice_distribution'] = array_count_values($allChoices);
            } else {
                // For single choice/dropdown
                $distribution = $answers->groupBy('answer_text')
                    ->map->count()
                    ->sortDesc();
                $stats['choice_distribution'] = $distribution->toArray();
                $stats['most_common'] = $distribution->keys()->first();
            }
        } elseif ($this->isScaleType()) {
            $answers = $this->answers()
                ->whereNotNull('answer_number')
                ->get();

            $stats['total_answers'] = $answers->count();
            $stats['average'] = $answers->avg('answer_number');
            $stats['min'] = $answers->min('answer_number');
            $stats['max'] = $answers->max('answer_number');
            $stats['distribution'] = $answers->groupBy('answer_number')
                ->map->count()
                ->toArray();
        } else {
            $stats['total_answers'] = $this->answers()->count();
        }

        return $stats;
    }

    /**
     * Validation
     */
    public function getValidationRulesForSubmission(): array
    {
        $rules = [];

        if ($this->required) {
            $rules[] = 'required';
        }

        switch ($this->type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'phone':
                $rules[] = 'string';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'time':
                $rules[] = 'date_format:H:i';
                break;
            case 'file_upload':
                $rules[] = 'file';
                if ($this->allowed_file_types) {
                    $rules[] = 'mimes:' . $this->allowed_file_types;
                }
                break;
            case 'multiple_choice':
                $rules[] = 'array';
                break;
        }

        // Add custom validation rules
        if ($this->validation_rules) {
            foreach ($this->validation_rules as $rule) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }
}
