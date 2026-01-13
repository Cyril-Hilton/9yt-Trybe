<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contestant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'poll_id',
        'contestant_number',
        'name',
        'bio',
        'photo',
        'video_url',
        'social_media',
        'status',
        'order',
        'total_votes',
        'total_revenue',
    ];

    protected $casts = [
        'social_media' => 'array',
        'total_revenue' => 'decimal:2',
    ];

    // Relationships
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helper methods
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/default-contestant.jpg');
    }

    public function getVotePercentage(): float
    {
        if ($this->poll->total_votes == 0) {
            return 0;
        }

        return ($this->total_votes / $this->poll->total_votes) * 100;
    }

    public function incrementVotes(int $count = 1, float $revenue = 0)
    {
        $this->increment('total_votes', $count);
        $this->increment('total_revenue', $revenue);

        // Also update poll totals
        $this->poll->increment('total_votes', $count);
        $this->poll->increment('total_revenue', $revenue);
    }

    public function getRank(): int
    {
        return $this->poll->contestants()
            ->where('status', 'active')
            ->where('total_votes', '>', $this->total_votes)
            ->count() + 1;
    }
}
