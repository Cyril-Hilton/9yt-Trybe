<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'conference_id',
        'ip_address',
        'user_agent',
        'referrer',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($view) {
            $view->created_at = now();
        });
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}