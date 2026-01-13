<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'capacity',
        'sold',
        'order',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'sold' => 'integer',
        'order' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(EventTicket::class);
    }

    public function hasCapacity($quantity = 1): bool
    {
        return ($this->sold + $quantity) <= $this->capacity;
    }

    public function getRemainingAttribute()
    {
        return max(0, $this->capacity - $this->sold);
    }

    public function getPercentageSoldAttribute()
    {
        if ($this->capacity === 0) {
            return 0;
        }

        return round(($this->sold / $this->capacity) * 100, 2);
    }
}
