<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Poll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'event_id',
        'slug',
        'title',
        'description',
        'banner_image',
        'poll_type',
        'voting_type',
        'vote_price',
        'votes_per_transaction',
        'allow_multiple_votes',
        'max_votes_per_user',
        'status',
        'start_date',
        'end_date',
        'show_results',
        'require_login',
        'total_votes',
        'total_revenue',
        'unique_voters',
        'views_count',
        'meta_title',
        'meta_description',
        'ai_tags',
        'ai_faqs',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'vote_price' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'allow_multiple_votes' => 'boolean',
        'show_results' => 'boolean',
        'require_login' => 'boolean',
        'ai_tags' => 'array',
        'ai_faqs' => 'array',
    ];

    // Boot method to auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($poll) {
            if (empty($poll->slug)) {
                $poll->slug = Str::slug($poll->title) . '-' . Str::random(8);
            }
        });
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function contestants()
    {
        return $this->hasMany(Contestant::class)->orderBy('order');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed')
            ->orWhere('end_date', '<', now());
    }

    // Helper methods
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();

        if ($this->start_date && $this->start_date > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        return true;
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed' || ($this->end_date && $this->end_date < now());
    }

    public function isPaid(): bool
    {
        return $this->voting_type === 'paid' && $this->vote_price > 0;
    }

    public function getBannerUrlAttribute()
    {
        if (!$this->banner_image) {
            return asset('images/default-poll-banner.jpg');
        }

        if (str_starts_with($this->banner_image, 'http://') || str_starts_with($this->banner_image, 'https://')) {
            return $this->banner_image;
        }

        return asset('storage/' . $this->banner_image);
    }

    public function getPublicUrlAttribute()
    {
        return url('/polls/' . $this->slug);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getLeader()
    {
        return $this->contestants()
            ->where('status', 'active')
            ->orderBy('total_votes', 'desc')
            ->first();
    }

    public function getContestantRankings()
    {
        return $this->contestants()
            ->where('status', 'active')
            ->orderBy('total_votes', 'desc')
            ->get()
            ->map(function ($contestant, $index) {
                $contestant->rank = $index + 1;
                return $contestant;
            });
    }

    public function canUserVote($user = null): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->require_login && !$user) {
            return false;
        }

        if ($user && $this->max_votes_per_user) {
            $userVotesCount = $this->votes()
                ->where('user_id', $user->id)
                ->sum('votes_count');

            if ($userVotesCount >= $this->max_votes_per_user) {
                return false;
            }
        }

        return true;
    }

    public function getUserVotesCount($user): int
    {
        if (!$user) {
            return 0;
        }

        return $this->votes()
            ->where('user_id', $user->id)
            ->sum('votes_count');
    }
}
