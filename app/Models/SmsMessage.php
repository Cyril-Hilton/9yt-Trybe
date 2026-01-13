<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'sms_campaign_id',
        'recipient',
        'message',
        'sender_id',
        'status',
        'external_id',
        'credits_used',
        'error_message',
        'sent_at',
        'delivered_at',
        'api_response',
    ];

    protected function casts(): array
    {
        return [
            'credits_used' => 'integer',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'api_response' => 'array',
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

    public function campaign()
    {
        return $this->belongsTo(SmsCampaign::class, 'sms_campaign_id');
    }

    public function markAsSent(?string $externalId = null): void
    {
        $this->update([
            'status' => 'submitted',
            'external_id' => $externalId,
            'sent_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
