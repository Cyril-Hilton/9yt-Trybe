<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'news_articles';

    protected $fillable = [
        'title',
        'slug',
        'type',
        'category',
        'description',
        'content',
        'image_path',
        'source_name',
        'source_url',
        'author',
        'meta_title',
        'meta_description',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getImageUrlAttribute()
    {
        return MediaUrl::fromPath($this->image_path);
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/blog/' . $this->slug);
    }
}
