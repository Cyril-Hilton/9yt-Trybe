<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use Illuminate\Http\Request;

class AdminShopController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = ShopProduct::query()->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $products = $query->paginate(20);

        return view('admin.shop.index', compact('products', 'status'));
    }

    public function approve(ShopProduct $product)
    {
        $product->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Product approved successfully!');
    }

    public function reject(Request $request, ShopProduct $product)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $product->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', 'Product rejected.');
    }

    public function toggleActive(ShopProduct $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        return redirect()->back()->with('success', 'Product status updated.');
    }

    public function create()
    {
        return view('admin.shop.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('shop-products', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Remove the image field from validated data as it's not a database column
        unset($validated['image']);

        // Admin-created products are automatically approved and active
        $validated['status'] = 'approved';
        $validated['is_active'] = true;

        ShopProduct::create($validated);

        return redirect()->route('admin.shop.index')->with('success', 'Product created successfully!');
    }

    public function edit(ShopProduct $product)
    {
        return view('admin.shop.edit', compact('product'));
    }

    public function update(Request $request, ShopProduct $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path && \Storage::disk('public')->exists($product->image_path)) {
                \Storage::disk('public')->delete($product->image_path);
            }
            $imagePath = $request->file('image')->store('shop-products', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Remove the image field from validated data as it's not a database column
        unset($validated['image']);

        $product->update($validated);

        return redirect()->route('admin.shop.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(ShopProduct $product)
    {
        // Delete image if exists
        if ($product->image_path && \Storage::disk('public')->exists($product->image_path)) {
            \Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully.');
    }
}
