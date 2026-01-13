<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Survey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'slug',
        'status',
        'allow_anonymous',
        'allow_multiple_responses',
        'show_progress_bar',
        'randomize_questions',
        'response_limit',
        'start_date',
        'end_date',
        'theme_color',
        'button_text',
        'thank_you_message',
        'redirect_url',
        'views_count',
        'responses_count',
        'completion_rate',
        'average_time_seconds',
    ];

    protected $casts = [
        'allow_anonymous' => 'boolean',
        'allow_multiple_responses' => 'boolean',
        'show_progress_bar' => 'boolean',
        'randomize_questions' => 'boolean',
        'response_limit' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'views_count' => 'integer',
        'responses_count' => 'integer',
        'completion_rate' => 'decimal:2',
        'average_time_seconds' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($survey) {
            if (empty($survey->slug)) {
                $survey->slug = Str::slug($survey->title) . '-' . Str::random(8);
            }
        });
    }

    /**
     * Relationships
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function completedResponses()
    {
        return $this->hasMany(SurveyResponse::class)->where('is_completed', true);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Helper Methods
     */
    public function getPublicUrlAttribute(): string
    {
        return url('/survey/' . $this->slug);
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        if ($this->response_limit > 0 && $this->responses_count >= $this->response_limit) {
            return false;
        }

        return true;
    }

    public function isAcceptingResponses(): bool
    {
        return $this->isActive();
    }

    public function incrementViewsCount(): void
    {
        $this->increment('views_count');
    }

    public function incrementResponsesCount(): void
    {
        $this->increment('responses_count');
        $this->updateCompletionRate();
    }

    public function updateCompletionRate(): void
    {
        if ($this->views_count > 0) {
            $this->completion_rate = round(($this->responses_count / $this->views_count) * 100, 2);
            $this->save();
        }
    }

    public function updateAverageTime(): void
    {
        $avgTime = $this->completedResponses()
            ->whereNotNull('time_taken_seconds')
            ->avg('time_taken_seconds');

        if ($avgTime) {
            $this->average_time_seconds = round($avgTime);
            $this->save();
        }
    }

    public function getAverageTimeFormatted(): string
    {
        if ($this->average_time_seconds == 0) {
            return 'N/A';
        }

        $minutes = floor($this->average_time_seconds / 60);
        $seconds = $this->average_time_seconds % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }

    public function getResponsesRemainingAttribute(): ?int
    {
        if ($this->response_limit == 0) {
            return null;
        }

        return max(0, $this->response_limit - $this->responses_count);
    }

    public function getCompletionRatePercentageAttribute(): string
    {
        return number_format($this->completion_rate, 1) . '%';
    }
}
