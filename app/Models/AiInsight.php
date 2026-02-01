<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiInsight extends Model
{
    protected $fillable = [
        'type',
        'subject_type',
        'subject_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }
}
