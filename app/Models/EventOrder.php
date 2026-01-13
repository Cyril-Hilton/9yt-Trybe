<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'event_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'subtotal',
        'service_fee',
        'processing_fee',
        'payment_gateway_fee',
        'vat',
        'platform_fee',
        'total',
        'fee_bearer',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_response',
        'paid_at',
        'status',
        'cancellation_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'payment_gateway_fee' => 'decimal:2',
        'vat' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(10));
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'order_number';
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendees()
    {
        return $this->hasMany(EventAttendee::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->payment_status === 'refunded';
    }

    public function getFormattedTotalAttribute()
    {
        return 'GH₵ ' . number_format($this->total, 2);
    }

    public function getAttendeesCountAttribute()
    {
        return $this->attendees()->count();
    }
}
