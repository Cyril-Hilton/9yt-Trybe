<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'sender_id',
        'name',
        'message',
        'type',
        'status',
        'total_recipients',
        'total_sent',
        'total_delivered',
        'total_failed',
        'total_pending',
        'credits_used',
        'scheduled_at',
        'sent_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_recipients' => 'integer',
            'total_sent' => 'integer',
            'total_delivered' => 'integer',
            'total_failed' => 'integer',
            'total_pending' => 'integer',
            'credits_used' => 'integer',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'completed_at' => 'datetime',
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

    public function messages()
    {
        return $this->hasMany(SmsMessage::class);
    }

    /**
     * Get the SmsSenderId record for this campaign's sender_id (if it exists)
     */
    public function getSenderIdRecordAttribute()
    {
        if (!$this->sender_id) {
            return null;
        }

        return SmsSenderId::where('owner_id', $this->owner_id)
            ->where('owner_type', $this->owner_type)
            ->where('sender_id', $this->sender_id)
            ->where('status', 'approved')
            ->first();
    }

    public function getDeliveryRateAttribute(): float
    {
        if ($this->total_sent === 0) {
            return 0;
        }

        return round(($this->total_delivered / $this->total_sent) * 100, 2);
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_at !== null;
    }

    public function canBeSent(): bool
    {
        return in_array($this->status, ['draft', 'scheduled']);
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'sent_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now());
    }
}
