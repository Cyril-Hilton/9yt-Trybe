<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\EventImage;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'slug',
        'title',
        'summary',
        'overview',
        'event_type',
        'is_holiday',
        'holiday_name',
        'holiday_country',
        'start_date',
        'end_date',
        'timezone',
        'recurrence_pattern',
        'recurrence_config',
        'recurrence_end_date',
        'location_type',
        'region',
        'venue_name',
        'venue_address',
        'venue_latitude',
        'venue_longitude',
        'online_platform',
        'online_link',
        'online_meeting_details',
        'banner_image',
        'age_restriction',
        'door_time',
        'parking_info',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'fee_bearer',
        'views_count',
        'likes_count',
        'tickets_sold',
        'total_revenue',
        'meta_title',
        'meta_description',
        'ai_tags',
        'ai_faqs',
        'is_external',
        'external_ticket_url',
        'external_ussd_code',
        'external_reservation_phone',
        'external_description',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'recurrence_end_date' => 'date',
        'recurrence_config' => 'array',
        'approved_at' => 'datetime',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'tickets_sold' => 'integer',
        'total_revenue' => 'decimal:2',
        'venue_latitude' => 'decimal:8',
        'venue_longitude' => 'decimal:8',
        'is_external' => 'boolean',
        'is_holiday' => 'boolean',
        'ai_tags' => 'array',
        'ai_faqs' => 'array',
    ];

    // Boot method to auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title) . '-' . Str::random(8);
            }
        });
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tickets()
    {
        return $this->hasMany(EventTicket::class)->orderBy('order');
    }

    public function sections()
    {
        return $this->hasMany(EventSection::class)->orderBy('order');
    }

    public function images()
    {
        return $this->hasMany(EventImage::class)->orderBy('order');
    }

    public function videos()
    {
        return $this->hasMany(EventVideo::class)->orderBy('order');
    }

    public function faqs()
    {
        return $this->hasMany(EventFaq::class)->orderBy('order');
    }

    public function orders()
    {
        return $this->hasMany(EventOrder::class);
    }

    public function attendees()
    {
        return $this->hasMany(EventAttendee::class);
    }

    public function likes()
    {
        return $this->hasMany(EventLike::class);
    }

    public function views()
    {
        return $this->hasMany(EventView::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'event_category')
            ->withTimestamps();
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('end_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeTrending($query)
    {
        return $query->approved()
            ->upcoming()
            ->orderBy('views_count', 'desc')
            ->orderBy('tickets_sold', 'desc');
    }

    // Helper methods
    public function getBannerUrlAttribute()
    {
        $banner = $this->banner_image;
        if (!$banner) {
            return asset('images/default-event-banner.jpg');
        }

        if (str_starts_with($banner, 'http://') || str_starts_with($banner, 'https://')) {
            return $banner;
        }

        if (preg_match('/^\d+$/', $banner)) {
            $image = EventImage::find((int) $banner);
            if (!$image) {
                $image = $this->images()->orderBy('order')->first();
            }
            if ($image && $image->image_path) {
                return asset('storage/' . $image->image_path);
            }

            return asset('images/default-event-banner.jpg');
        }

        if (!str_contains($banner, '/')) {
            return asset('storage/events/banners/' . $banner);
        }

        return asset('storage/' . $banner);
    }

    public function getFlierPathAttribute(): ?string
    {
        $banner = $this->banner_image;
        if (!$banner) {
            return null;
        }

        if (preg_match('/^\d+$/', $banner)) {
            $image = EventImage::find((int) $banner);
            if (!$image) {
                $image = $this->images()->orderBy('order')->first();
            }
            return $image?->image_path;
        }

        if (!str_contains($banner, '/')) {
            return 'events/banners/' . $banner;
        }

        return $banner;
    }

    public function getFlierUrlAttribute(): string
    {
        $path = $this->flier_path;

        if (!$path) {
            return asset('ui/logo/9yt-trybe-logo-light.png');
        }

        if ($this->isRemotePath($path)) {
            return $path;
        }

        return asset('storage/' . $path);
    }

    public function hasLocalFlier(): bool
    {
        $path = $this->flier_path;
        if (!$path) {
            return false;
        }

        return !$this->isRemotePath($path);
    }

    private function isRemotePath(string $path): bool
    {
        return Str::startsWith($path, ['http://', 'https://']);
    }

    public function getPublicUrlAttribute()
    {
        return route('events.show', $this->slug);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSoldOut(): bool
    {
        $activeTickets = $this->tickets()->where('is_active', true)->get();

        foreach ($activeTickets as $ticket) {
            if ($ticket->isAvailable()) {
                return false;
            }
        }

        return true;
    }

    public function hasActiveTickets(): bool
    {
        return $this->tickets()->where('is_active', true)->exists();
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    public function decrementLikes()
    {
        $this->decrement('likes_count');
    }

    public function getFormattedDateAttribute()
    {
        return $this->start_date->format('l, F j, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->start_date->format('g:i A') . ' - ' . $this->end_date->format('g:i A');
    }

    public function getCheapestTicketPriceAttribute()
    {
        $cheapest = $this->tickets()
            ->where('type', 'paid')
            ->where('is_active', true)
            ->min('price');

        return $cheapest ?? 0;
    }

    public function hasFreeTickets(): bool
    {
        return $this->tickets()
            ->where('type', 'free')
            ->where('is_active', true)
            ->exists();
    }

    public function getTotalCapacityAttribute()
    {
        if ($this->sections()->exists()) {
            return $this->sections()->sum('capacity');
        }

        return $this->tickets()->sum('quantity');
    }

    public function getTotalSoldAttribute()
    {
        return $this->tickets()->sum('sold');
    }

    public function getAvailableTicketsAttribute()
    {
        $total = $this->total_capacity;
        $sold = $this->total_sold;

        return max(0, $total - $sold);
    }

    /**
     * Scope to filter by category (supports multiple categories)
     */
    public function scopeInCategory($query, $categorySlug)
    {
        return $query->whereHas('categories', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    /**
     * Scope to filter events on holidays
     */
    public function scopeHolidays($query)
    {
        return $query->where('is_holiday', true);
    }

    /**
     * Check if event has a specific category
     */
    public function hasCategory(string $categorySlug): bool
    {
        return $this->categories()->where('slug', $categorySlug)->exists();
    }

    /**
     * Get category names as comma-separated string
     */
    public function getCategoryNamesAttribute(): string
    {
        return $this->categories->pluck('name')->join(', ');
    }
}
