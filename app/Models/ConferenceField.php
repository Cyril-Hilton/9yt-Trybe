<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConferenceField extends Model
{
    protected $fillable = [
        'conference_id',
        'label',
        'field_name',
        'type',
        'options',
        'required',
        'placeholder',
        'help_text',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
        'order' => 'integer',
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    // Get options as array for select/radio/checkbox
    public function getOptionsArray(): array
    {
        if (is_string($this->options)) {
            return json_decode($this->options, true) ?? [];
        }
        return $this->options ?? [];
    }
}