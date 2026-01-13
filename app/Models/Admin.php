<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        'last_login_at',
        'oauth_provider',
        'oauth_id',
        'otp_code',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'otp_expires_at' => 'datetime',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Generate a 6-digit OTP code
     */
    public function generateOTP()
    {
        $this->otp_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_expires_at = now()->addMinutes(10);
        $this->save();
        return $this->otp_code;
    }

    /**
     * Verify the OTP code
     */
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
