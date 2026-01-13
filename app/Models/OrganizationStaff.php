<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationStaff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'organization_staff';

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'event_ids',
        'status',
        'otp_code',
        'otp_expires_at',
        'last_login_at',
    ];

    protected $hidden = [
        'otp_code',
    ];

    protected $casts = [
        'event_ids' => 'array',
        'otp_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function checkedInTickets()
    {
        return $this->hasMany(EventTicket::class, 'checked_in_by_staff');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    // Helper methods
    public function canAccessEvent($eventId)
    {
        // Staff can access events in their event_ids array or events from their company
        if ($this->event_ids && in_array($eventId, $this->event_ids)) {
            return true;
        }

        return Event::where('id', $eventId)
                    ->where('company_id', $this->company_id)
                    ->exists();
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function generateOTP()
    {
        $this->otp_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_expires_at = now()->addMinutes(10);
        $this->save();

        return $this->otp_code;
    }

    public function verifyOTP($code)
    {
        if ($this->otp_code === $code && $this->otp_expires_at && $this->otp_expires_at->isFuture()) {
            $this->otp_code = null;
            $this->otp_expires_at = null;
            $this->last_login_at = now();
            $this->save();
            return true;
        }
        return false;
    }
}
