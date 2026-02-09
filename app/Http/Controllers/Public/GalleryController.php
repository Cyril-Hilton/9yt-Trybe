<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Models\MagazineImage;

class GalleryController extends Controller
{
    public function index()
    {
        $newEvents = GalleryImage::where('is_active', true)
            ->where('category', 'new')
            ->orderBy('order')
            ->get();

        $oldEvents = GalleryImage::where('is_active', true)
            ->where('category', 'old')
            ->orderBy('order')
            ->get();

        $magazineImages = MagazineImage::where('is_active', true)
            ->orderBy('order')
            ->get();

        // Flag for SEO - prevent indexing empty pages
        $isEmpty = $newEvents->isEmpty() && $oldEvents->isEmpty() && $magazineImages->isEmpty();

        return view('public.gallery.index', compact('newEvents', 'oldEvents', 'magazineImages', 'isEmpty'));
    }
}
