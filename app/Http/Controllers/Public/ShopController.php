<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use App\Models\CartItem;
use App\Services\SEO\AiTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index()
    {
        $products = ShopProduct::where('status', 'approved')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('public.shop.index', compact('products'));
    }

    public function show(Request $request, ShopProduct $product)
    {
        if (!$product->isApproved() || !$product->is_active) {
            abort(404);
        }

        // Get related products (same category or random if no category)
        $relatedProducts = ShopProduct::where('status', 'approved')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $metaOverrides = null;
        $translator = app(AiTranslationService::class);
        $lang = $translator->resolveLanguage($request->query('lang'));
        if ($lang !== 'en') {
            $baseTitle = $product->meta_title ?: ($product->name . ' - Shop');
            $baseDescription = $product->meta_description ?: Str::limit(strip_tags($product->description ?? ''), 155);
            $metaOverrides = $translator->translateMeta($baseTitle, $baseDescription, $lang);
        }

        return view('public.shop.show', compact('product', 'relatedProducts', 'metaOverrides'));
    }

    public function cart()
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        $cartItems = CartItem::with('product')
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get();

        $total = $cartItems->sum('subtotal');

        return view('public.shop.cart', compact('cartItems', 'total'));
    }

    public function addToCart(Request $request, ShopProduct $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $sessionId = session()->getId();
        $userId = auth()->id();

        CartItem::create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'size' => $request->size,
            'color' => $request->color,
        ]);

        return redirect()->route('shop.cart')->with('success', 'Product added to cart successfully!');
    }

    public function updateCartItem(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        // Verify ownership
        $sessionId = session()->getId();
        $userId = auth()->id();

        if ($userId && $cartItem->user_id !== $userId) {
            abort(403);
        }

        if (!$userId && $cartItem->session_id !== $sessionId) {
            abort(403);
        }

        // Check stock availability
        if ($request->quantity > $cartItem->product->stock) {
            return back()->with('error', 'Only ' . $cartItem->product->stock . ' items available in stock.');
        }

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Cart updated successfully!');
    }

    public function removeFromCart(CartItem $cartItem)
    {
        // Verify ownership
        $sessionId = session()->getId();
        $userId = auth()->id();

        if ($userId && $cartItem->user_id !== $userId) {
            abort(403);
        }

        if (!$userId && $cartItem->session_id !== $sessionId) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }
}
