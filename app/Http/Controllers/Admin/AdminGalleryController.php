<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Models\MagazineImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminGalleryController extends Controller
{
    // Gallery Images Management
    public function galleryIndex(Request $request)
    {
        $category = $request->get('category', 'all');
        $status = $request->get('status', 'all');

        $query = GalleryImage::query()->orderBy('order')->orderBy('created_at', 'desc');

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        if ($status !== 'all') {
            $isActive = $status === 'active';
            $query->where('is_active', $isActive);
        }

        $images = $query->paginate(20);

        return view('admin.gallery.index', compact('images', 'category', 'status'));
    }

    public function galleryCreate()
    {
        return view('admin.gallery.create');
    }

    public function galleryStore(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
            'category' => ['required', 'in:new,old'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('gallery', 'public');
            $validated['image_path'] = $imagePath;
        }

        unset($validated['image']);

        GalleryImage::create($validated);

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery image added successfully!');
    }

    public function galleryEdit(GalleryImage $image)
    {
        return view('admin.gallery.edit', compact('image'));
    }

    public function galleryUpdate(Request $request, GalleryImage $image)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'category' => ['required', 'in:new,old'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }

            $imagePath = $request->file('image')->store('gallery', 'public');
            $validated['image_path'] = $imagePath;
        }

        unset($validated['image']);

        $image->update($validated);

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery image updated successfully!');
    }

    public function galleryToggleActive(GalleryImage $image)
    {
        $image->update(['is_active' => !$image->is_active]);

        return back()->with('success', 'Gallery image status updated!');
    }

    public function galleryDestroy(GalleryImage $image)
    {
        if ($image->image_path) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Gallery image deleted successfully!');
    }

    // Magazine Images Management
    public function magazineIndex(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = MagazineImage::query()->orderBy('order')->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $isActive = $status === 'active';
            $query->where('is_active', $isActive);
        }

        $images = $query->paginate(20);

        return view('admin.magazine.index', compact('images', 'status'));
    }

    public function magazineCreate()
    {
        return view('admin.magazine.create');
    }

    public function magazineStore(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('magazine', 'public');
            $validated['image_path'] = $imagePath;
        }

        unset($validated['image']);

        MagazineImage::create($validated);

        return redirect()->route('admin.magazine.index')->with('success', 'Magazine image added successfully!');
    }

    public function magazineEdit(MagazineImage $image)
    {
        return view('admin.magazine.edit', compact('image'));
    }

    public function magazineUpdate(Request $request, MagazineImage $image)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }

            $imagePath = $request->file('image')->store('magazine', 'public');
            $validated['image_path'] = $imagePath;
        }

        unset($validated['image']);

        $image->update($validated);

        return redirect()->route('admin.magazine.index')->with('success', 'Magazine image updated successfully!');
    }

    public function magazineToggleActive(MagazineImage $image)
    {
        $image->update(['is_active' => !$image->is_active]);

        return back()->with('success', 'Magazine image status updated!');
    }

    public function magazineDestroy(MagazineImage $image)
    {
        if ($image->image_path) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Magazine image deleted successfully!');
    }
}
