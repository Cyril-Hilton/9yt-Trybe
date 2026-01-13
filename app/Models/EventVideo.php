<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'platform',
        'video_url',
        'video_id',
        'title',
        'order',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getEmbedUrlAttribute()
    {
        if ($this->platform === 'youtube') {
            return 'https://www.youtube.com/embed/' . $this->video_id;
        }

        if ($this->platform === 'vimeo') {
            return 'https://player.vimeo.com/video/' . $this->video_id;
        }

        return null;
    }

    public static function extractVideoId($url, $platform)
    {
        if ($platform === 'youtube') {
            // Handle YouTube live URLs: youtube.com/live/VIDEO_ID
            if (preg_match('/youtube\.com\/live\/([^"&?\/\s]+)/', $url, $matches)) {
                return $matches[1];
            }

            // Handle various other YouTube URL formats
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
            return $matches[1] ?? null;
        }

        if ($platform === 'vimeo') {
            // Handle Vimeo URL formats
            preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/', $url, $matches);
            return $matches[3] ?? null;
        }

        return null;
    }

    public static function detectPlatform($url)
    {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'youtube';
        }

        if (strpos($url, 'vimeo.com') !== false) {
            return 'vimeo';
        }

        return null;
    }
}
