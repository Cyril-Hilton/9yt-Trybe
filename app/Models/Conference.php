<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\ConferenceField;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'title', 'description', 'meta_title', 'meta_description', 'slug', 'logo', 'header_image', 'venue',
        'start_date', 'end_date', 'online_limit', 'in_person_limit',
        'online_count', 'in_person_count', 'status', 'form_fields', 'views_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'form_fields' => 'array',
        'online_limit' => 'integer',
        'in_person_limit' => 'integer',
        'online_count' => 'integer',
        'in_person_count' => 'integer',
        'views_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($conference) {
            if (empty($conference->slug)) {
                $conference->slug = Str::slug($conference->title) . '-' . Str::random(6);
            }
        });
    }
// Image helper methods
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    public function getHeaderImageUrlAttribute(): ?string
    {
        return $this->header_image ? Storage::url($this->header_image) : null;
    }

    public function hasLogo(): bool
    {
        return !empty($this->logo) && Storage::exists($this->logo);
    }

    public function hasHeaderImage(): bool
    {
        return !empty($this->header_image) && Storage::exists($this->header_image);
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function views()
    {
        return $this->hasMany(ConferenceView::class);
    }

    public function isOnlineAvailable(): bool
    {
        if ($this->status !== 'active') return false;
        if ($this->online_limit == 0) return true;
        return $this->online_count < $this->online_limit;
    }

    public function isInPersonAvailable(): bool
    {
        if ($this->status !== 'active') return false;
        if ($this->in_person_limit == 0) return true;
        return $this->in_person_count < $this->in_person_limit;
    }

    public function incrementCount(string $type): void
    {
        if ($type === 'online') {
            $this->increment('online_count');
        } elseif ($type === 'in_person') {
            $this->increment('in_person_count');
        }
    }

    public function decrementCount(string $type): void
    {
        if ($type === 'online' && $this->online_count > 0) {
            $this->decrement('online_count');
        } elseif ($type === 'in_person' && $this->in_person_count > 0) {
            $this->decrement('in_person_count');
        }
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/register/' . $this->slug);
    }

    public function onlineRegistrations()
    {
        return $this->registrations()->where('attendance_type', 'online');
    }

    public function inPersonRegistrations()
    {
        return $this->registrations()->where('attendance_type', 'in_person');
    }

    public function attendedRegistrations()
    {
        return $this->registrations()->where('attended', true);
    }

    public function getConversionRateAttribute(): float
    {
        if ($this->views_count == 0) return 0;
        return round(($this->registrations()->count() / $this->views_count) * 100, 2);
    }

    public function getAttendanceRateAttribute(): float
    {
        $inPersonCount = $this->inPersonRegistrations()->count();
        if ($inPersonCount == 0) return 0;
        $attendedCount = $this->inPersonRegistrations()->where('attended', true)->count();
        return round(($attendedCount / $inPersonCount) * 100, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }
    public function customFields()
{
    return $this->hasMany(ConferenceField::class)->orderBy('order');
}
}
