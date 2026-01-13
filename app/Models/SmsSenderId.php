<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSenderId extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'sender_id',
        'status',
        'purpose',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'reviewed_at' => 'datetime',
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

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    // Alias for reviewer relationship (for backward compatibility)
    public function reviewedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function approve(Admin $admin): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject(Admin $admin, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function setAsDefault(): void
    {
        // Remove default from other sender IDs for this owner
        self::where('owner_id', $this->owner_id)
            ->where('owner_type', $this->owner_type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
