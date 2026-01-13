<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'payout_number',
        'company_id',
        'event_id',
        'payment_account_id',
        'gross_amount',
        'platform_fees',
        'net_amount',
        'currency',
        'status',
        'failure_reason',
        'admin_notes',
        'payout_method',
        'payout_reference',
        'processed_at',
        'completed_at',
        'congratulatory_email_sent_at',
        'payment_confirmation_email_sent_at',
        'total_tickets_sold',
        'total_attendees',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'platform_fees' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'congratulatory_email_sent_at' => 'datetime',
        'payment_confirmation_email_sent_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payout) {
            if (empty($payout->payout_number)) {
                $payout->payout_number = 'PAYOUT-' . strtoupper(Str::random(10));
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function paymentAccount()
    {
        return $this->belongsTo(OrganizationPaymentAccount::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRequested(): bool
    {
        return $this->status === 'requested';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function getFormattedNetAmountAttribute()
    {
        return 'GH₵ ' . number_format($this->net_amount, 2);
    }

    public function congratulatoryEmailSent(): bool
    {
        return !is_null($this->congratulatory_email_sent_at);
    }

    public function paymentConfirmationEmailSent(): bool
    {
        return !is_null($this->payment_confirmation_email_sent_at);
    }

    public function needsPaymentAccountSetup(): bool
    {
        return is_null($this->payment_account_id) && $this->isPending();
    }

    public function canBeProcessed(): bool
    {
        return ($this->isPending() || $this->isRequested()) && !is_null($this->payment_account_id);
    }

    public function canRequestPayout(): bool
    {
        return $this->isPending() && !is_null($this->payment_account_id);
    }

    public function markCongratulatoryEmailSent(): void
    {
        $this->update(['congratulatory_email_sent_at' => now()]);
    }

    public function markPaymentConfirmationEmailSent(): void
    {
        $this->update(['payment_confirmation_email_sent_at' => now()]);
    }
}
