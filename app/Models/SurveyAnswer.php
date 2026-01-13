<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SurveyAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_response_id',
        'survey_question_id',
        'answer_text',
        'answer_array',
        'answer_number',
        'answer_date',
        'answer_time',
        'answer_file_path',
    ];

    protected $casts = [
        'answer_array' => 'array',
        'answer_number' => 'integer',
        'answer_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function response()
    {
        return $this->belongsTo(SurveyResponse::class, 'survey_response_id');
    }

    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_question_id');
    }

    /**
     * Get the display value of the answer
     */
    public function getDisplayValue(): string
    {
        if ($this->answer_text !== null) {
            return $this->answer_text;
        }

        if ($this->answer_array !== null) {
            return implode(', ', $this->answer_array);
        }

        if ($this->answer_number !== null) {
            return (string) $this->answer_number;
        }

        if ($this->answer_date !== null) {
            return $this->answer_date->format('Y-m-d');
        }

        if ($this->answer_time !== null) {
            return $this->answer_time;
        }

        if ($this->answer_file_path !== null) {
            return basename($this->answer_file_path);
        }

        return 'No answer';
    }

    /**
     * Get the raw value of the answer
     */
    public function getValue()
    {
        if ($this->answer_text !== null) {
            return $this->answer_text;
        }

        if ($this->answer_array !== null) {
            return $this->answer_array;
        }

        if ($this->answer_number !== null) {
            return $this->answer_number;
        }

        if ($this->answer_date !== null) {
            return $this->answer_date;
        }

        if ($this->answer_time !== null) {
            return $this->answer_time;
        }

        if ($this->answer_file_path !== null) {
            return $this->answer_file_path;
        }

        return null;
    }

    /**
     * Check if answer has a file
     */
    public function hasFile(): bool
    {
        return !empty($this->answer_file_path) && Storage::exists($this->answer_file_path);
    }

    /**
     * Get file URL
     */
    public function getFileUrl(): ?string
    {
        if ($this->hasFile()) {
            return Storage::url($this->answer_file_path);
        }

        return null;
    }

    /**
     * Get file size
     */
    public function getFileSize(): ?int
    {
        if ($this->hasFile()) {
            return Storage::size($this->answer_file_path);
        }

        return null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSize(): ?string
    {
        $size = $this->getFileSize();

        if (!$size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;

        return number_format($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }
}
