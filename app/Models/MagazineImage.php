<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MagazineImage extends Model
{
    protected $fillable = [
        'title',
        'image_path',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute()
    {
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }
        return asset('storage/' . $this->image_path);
    }
}
