<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'icon_svg',
        'color',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_category')
            ->withTimestamps();
    }

    public function polls()
    {
        return $this->belongsToMany(Poll::class, 'poll_category')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    // Helper methods
    public function getIconHtml(): string
    {
        if ($this->icon_svg) {
            return $this->icon_svg;
        }

        // Fallback SVG icon
        return '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16z"/></svg>';
    }
}
