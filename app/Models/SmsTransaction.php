<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'sms_plan_id',
        'reference',
        'type',
        'amount',
        'credits',
        'status',
        'payment_method',
        'meta_data',
        'credited_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'credits' => 'integer',
            'meta_data' => 'array',
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

    public function plan()
    {
        return $this->belongsTo(SmsPlan::class, 'sms_plan_id');
    }

    public function creditedBy()
    {
        return $this->belongsTo(Admin::class, 'credited_by');
    }

    public function creditedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'credited_by');
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
