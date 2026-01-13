<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id', 
        'name', 
        'email', 
        'phone', 
        'attendance_type',
        'unique_id', 
        'attended', 
        'attended_at', 
        'additional_fields',
        'custom_data',  // ADD THIS
        'ip_address', 
        'user_agent',
    ];

    protected $casts = [
        'attended' => 'boolean',
        'attended_at' => 'datetime',
        'additional_fields' => 'array',
        'custom_data' => 'array',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function markAsAttended(): void
    {
        $this->update([
            'attended' => true,
            'attended_at' => now(),
        ]);
    }

    public function isInPerson(): bool
    {
        return $this->attendance_type === 'in_person';
    }

    public function isOnline(): bool
    {
        return $this->attendance_type === 'online';
    }

    public function scopeInPerson($query)
    {
        return $query->where('attendance_type', 'in_person');
    }

    public function scopeOnline($query)
    {
        return $query->where('attendance_type', 'online');
    }

    // Helper method to get custom field value
    public function getCustomField(string $fieldName)
    {
        return $this->custom_data[$fieldName] ?? null;
    }

    public function scopeAttended($query)
    {
        return $query->where('attended', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('unique_id', 'like', "%{$search}%");
        });
    }
}