<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'contestant_id',
        'user_id',
        'votes_count',
        'amount_paid',
        'payment_reference',
        'payment_status',
        'voter_name',
        'voter_email',
        'voter_phone',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'votes_count' => 'integer',
    ];

    // Relationships
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function contestant()
    {
        return $this->belongsTo(Contestant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForContestant($query, $contestantId)
    {
        return $query->where('contestant_id', $contestantId);
    }

    // Helper methods
    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }
}
