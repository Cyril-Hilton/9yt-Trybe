<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ComplementaryTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'issued_by',
        'recipient_name',
        'recipient_email',
        'recipient_phone',
        'ticket_type',
        'original_price',
        'quantity',
        'purpose',
        'notes',
        'qr_code',
        'ticket_reference',
        'status',
        'used_at',
        'scanned_by',
        'visible_to_organizer',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'quantity' => 'integer',
        'visible_to_organizer' => 'boolean',
        'used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->qr_code)) {
                $ticket->qr_code = 'COMP-' . strtoupper(Str::random(12));
            }
            if (empty($ticket->ticket_reference)) {
                $ticket->ticket_reference = 'CT-' . strtoupper(Str::random(10)) . '-' . time();
            }
        });
    }

    /**
     * Get the event that the complementary ticket belongs to
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the admin who issued the ticket
     */
    public function issuedBy()
    {
        return $this->belongsTo(Admin::class, 'issued_by');
    }

    /**
     * Get the admin who scanned the ticket
     */
    public function scannedBy()
    {
        return $this->belongsTo(Admin::class, 'scanned_by');
    }

    /**
     * Mark ticket as used
     */
    public function markAsUsed($scannedBy = null)
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
            'scanned_by' => $scannedBy,
        ]);
    }

    /**
     * Cancel the ticket
     */
    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Check if ticket is valid (active and not used)
     */
    public function isValid(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope to get active tickets only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get used tickets only
     */
    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    /**
     * Scope to get cancelled tickets only
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope to get tickets for a specific event
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope to get tickets visible to organizer
     */
    public function scopeVisibleToOrganizer($query)
    {
        return $query->where('visible_to_organizer', true);
    }
}
