<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_section_id',
        'name',
        'description',
        'type',
        'price',
        'min_donation',
        'quantity',
        'sold',
        'min_per_order',
        'max_per_order',
        'sales_start',
        'sales_end',
        'is_active',
        'is_hidden',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'min_donation' => 'decimal:2',
        'quantity' => 'integer',
        'sold' => 'integer',
        'min_per_order' => 'integer',
        'max_per_order' => 'integer',
        'sales_start' => 'datetime',
        'sales_end' => 'datetime',
        'is_active' => 'boolean',
        'is_hidden' => 'boolean',
        'order' => 'integer',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function section()
    {
        return $this->belongsTo(EventSection::class, 'event_section_id');
    }

    public function attendees()
    {
        return $this->hasMany(EventAttendee::class);
    }

    // Helper methods
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check if sales have started
        if ($this->sales_start && now()->lt($this->sales_start)) {
            return false;
        }

        // Check if sales have ended
        if ($this->sales_end && now()->gt($this->sales_end)) {
            return false;
        }

        // Check if sold out
        if ($this->quantity !== null && $this->sold >= $this->quantity) {
            return false;
        }

        // Check section capacity if ticket belongs to a section
        if ($this->section) {
            return $this->section->hasCapacity();
        }

        return true;
    }

    public function isFree(): bool
    {
        return $this->type === 'free';
    }

    public function isPaid(): bool
    {
        return $this->type === 'paid';
    }

    public function isDonation(): bool
    {
        return $this->type === 'donation';
    }

    public function isSoldOut(): bool
    {
        if ($this->quantity === null) {
            return false; // Unlimited tickets
        }

        return $this->sold >= $this->quantity;
    }

    public function getRemainingAttribute()
    {
        if ($this->quantity === null) {
            return 'Unlimited';
        }

        return max(0, $this->quantity - $this->sold);
    }

    public function incrementSold($quantity = 1)
    {
        $this->increment('sold', $quantity);

        // Also increment section sold if ticket belongs to a section
        if ($this->section) {
            $this->section->increment('sold', $quantity);
        }

        // Update event tickets_sold count
        $this->event->increment('tickets_sold', $quantity);
    }

    public function decrementSold($quantity = 1)
    {
        $this->decrement('sold', $quantity);

        // Also decrement section sold if ticket belongs to a section
        if ($this->section) {
            $this->section->decrement('sold', $quantity);
        }

        // Update event tickets_sold count
        $this->event->decrement('tickets_sold', $quantity);
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->isFree()) {
            return 'Free';
        }

        if ($this->isDonation()) {
            return $this->min_donation ? 'From GH₵ ' . number_format($this->min_donation, 2) : 'Pay what you want';
        }

        return 'GH₵ ' . number_format($this->price, 2);
    }
}
