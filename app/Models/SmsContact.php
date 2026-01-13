<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'phone_number',
        'name',
        'email',
        'group',
        'notes',
    ];

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

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('phone_number', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public static function getUniqueGroups(int $ownerId, string $ownerType): array
    {
        return self::where('owner_id', $ownerId)
            ->where('owner_type', $ownerType)
            ->whereNotNull('group')
            ->distinct()
            ->pluck('group')
            ->toArray();
    }

    /**
     * Legacy method for backward compatibility
     * @deprecated Use getUniqueGroups($ownerId, $ownerType) instead
     */
    public static function getUniqueGroupsForCompany(int $companyId): array
    {
        return self::getUniqueGroups($companyId, 'App\\Models\\Company');
    }
}
