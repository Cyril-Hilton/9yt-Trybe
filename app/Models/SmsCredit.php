<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'balance',
        'total_purchased',
        'total_used',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'integer',
            'total_purchased' => 'integer',
            'total_used' => 'integer',
        ];
    }

    /**
     * Get the owning model (User or Company)
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * Legacy method for backward compatibility
     * @deprecated Use owner() instead
     */
    public function company()
    {
        return $this->owner();
    }

    public function addCredits(int $amount): void
    {
        $this->increment('balance', $amount);
        $this->increment('total_purchased', $amount);
    }

    public function deductCredits(int $amount): bool
    {
        if ($this->balance < $amount) {
            return false;
        }

        $this->decrement('balance', $amount);
        $this->increment('total_used', $amount);
        return true;
    }

    public function hasEnoughCredits(int $amount): bool
    {
        return $this->balance >= $amount;
    }
}
