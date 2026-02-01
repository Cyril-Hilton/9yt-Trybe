<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Company extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'password',
        'logo',
        'website',
        'description',
        'meta_title',
        'meta_description',
        'ai_tags',
        'ai_faqs',
        'is_suspended',
        'suspension_reason',
        'suspended_at',
        'email_verified_at',
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
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'ai_tags' => 'array',
            'ai_faqs' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->slug)) {
                $company->slug = static::generateUniqueSlug($company->name);
            }
        });

        static::updating(function ($company) {
            if ($company->isDirty('name') && empty($company->slug)) {
                $company->slug = static::generateUniqueSlug($company->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        if ($slug === '') {
            $slug = 'organizer';
        }

        $count = static::where('slug', 'LIKE', "{$slug}%")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function suspend(?string $reason = null): void
    {
        $this->update([
            'is_suspended' => true,
            'suspension_reason' => $reason,
            'suspended_at' => now(),
        ]);
    }

    public function unsuspend(): void
    {
        $this->update([
            'is_suspended' => false,
            'suspension_reason' => null,
            'suspended_at' => null,
        ]);
    }

    public function conferences()
    {
        return $this->hasMany(Conference::class);
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function registrations()
    {
        return $this->hasManyThrough(Registration::class, Conference::class);
    }

    public function smsCredit()
    {
        return $this->morphOne(SmsCredit::class, 'owner');
    }

    public function smsCampaigns()
    {
        return $this->morphMany(SmsCampaign::class, 'owner');
    }

    public function smsContacts()
    {
        return $this->morphMany(SmsContact::class, 'owner');
    }

    public function smsSenderIds()
    {
        return $this->morphMany(SmsSenderId::class, 'owner');
    }

    public function smsTransactions()
    {
        return $this->morphMany(SmsTransaction::class, 'owner');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function followers()
    {
        return $this->hasMany(OrganizationFollower::class);
    }

    public function paymentAccounts()
    {
        return $this->hasMany(OrganizationPaymentAccount::class);
    }

    public function payouts()
    {
        return $this->hasMany(EventPayout::class);
    }

    public function polls()
    {
        return $this->hasMany(Poll::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getFollowersCountAttribute()
    {
        return $this->followers()->count();
    }

    public function getTotalEventsAttribute()
    {
        return $this->events()->count();
    }

    public function getApprovedEventsAttribute()
    {
        return $this->events()->approved()->count();
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
            $this->save();
            return true;
        }
        return false;
    }
}
