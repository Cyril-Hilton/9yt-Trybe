<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopProduct;
use App\Models\Admin;
use App\Services\Sms\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShopOrderConfirmation;
use App\Services\NotificationService;

class ShopCheckoutController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function show()
    {
        // AUTHENTICATION REQUIRED - Users must login/register to purchase
        // This builds user database and enables better customer tracking
        $userId = auth()->id();

        // Get cart items for authenticated user only
        $cartItems = CartItem::with('product')
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('shop.cart')
                ->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum('subtotal');
        $shippingFee = $this->calculateShipping($subtotal);
        $total = $subtotal + $shippingFee;

        return view('public.shop.checkout', compact('cartItems', 'subtotal', 'shippingFee', 'total'));
    }

    public function process(Request $request)
    {
        // AUTHENTICATION REQUIRED - Only logged-in users can purchase
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['required', 'in:paystack'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $userId = auth()->id();

        // Get cart items for authenticated user only
        $cartItems = CartItem::with('product')
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('shop.cart')
                ->with('error', 'Your cart is empty.');
        }

        // Validate stock availability
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->withErrors([
                    'stock' => "Sorry, {$item->product->name} has insufficient stock."
                ])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $subtotal = $cartItems->sum('subtotal');
            $shippingFee = $this->calculateShipping($subtotal);
            $total = $subtotal + $shippingFee;

            // Create order (always linked to authenticated user)
            $order = ShopOrder::create([
                'user_id' => $userId, // Always has user ID (no guest checkout)
                'session_id' => null, // No session-based orders anymore
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'city' => $validated['city'],
                'region' => $validated['region'] ?? null,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'payment_method' => 'paystack',
                'payment_status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items and update stock
            foreach ($cartItems as $item) {
                ShopOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_image' => $item->product->image_path,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'size' => $item->size,
                    'color' => $item->color,
                    'subtotal' => $item->subtotal,
                ]);

                // Update product stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Clear cart
            $cartItems->each->delete();

            DB::commit();

            // Initialize Paystack payment
            return $this->initializePayment($order);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shop checkout error: ' . $e->getMessage());

            // Show detailed error in development
            $errorMessage = config('app.debug')
                ? 'Error: ' . $e->getMessage()
                : 'An error occurred. Please try again.';

            return back()->withErrors(['error' => $errorMessage])->withInput();
        }
    }

    private function initializePayment(ShopOrder $order)
    {
        $paystackSecretKey = config('services.paystack.secret_key');

        if (!$paystackSecretKey || strlen($paystackSecretKey) < 10) {
            $errorMessage = config('app.debug')
                ? 'Payment system is not configured. Please set PAYSTACK_SECRET_KEY in .env file with a valid Paystack secret key.'
                : 'Payment system is not configured. Please contact support.';

            return back()->withErrors(['error' => $errorMessage])->withInput();
        }

        try {
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecretKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $order->customer_email,
                'amount' => $order->total * 100, // Convert to pesewas/kobo
                'reference' => $order->order_number,
                'callback_url' => route('shop.payment.callback'),
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                ],
            ]);

            if ($response->successful() && $response->json('status')) {
                $authorizationUrl = $response->json('data.authorization_url');
                $order->update(['payment_reference' => $order->order_number]);

                return redirect($authorizationUrl);
            }

            $errorMessage = config('app.debug')
                ? 'Payment initialization failed. Response: ' . $response->body()
                : 'Payment initialization failed. Please try again.';

            return back()->withErrors(['error' => $errorMessage])->withInput();
        } catch (\Exception $e) {
            Log::error('Paystack initialization error: ' . $e->getMessage());

            $errorMessage = config('app.debug')
                ? 'Payment system error: ' . $e->getMessage()
                : 'Payment system error. Please try again.';

            return back()->withErrors(['error' => $errorMessage])->withInput();
        }
    }

    public function paymentCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('shop.cart')
                ->with('error', 'Invalid payment reference.');
        }

        $order = ShopOrder::where('payment_reference', $reference)->first();

        if (!$order) {
            return redirect()->route('shop.cart')
                ->with('error', 'Order not found.');
        }

        // Verify payment with Paystack
        $paystackSecretKey = config('services.paystack.secret_key');

        try {
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecretKey,
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if ($response->successful() && $response->json('data.status') === 'success') {
                $order->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'status' => 'processing',
                ]);

                if ($order->user_id && (!Auth::check() || Auth::id() != $order->user_id)) {
                    Auth::loginUsingId($order->user_id);
                    $request->session()->regenerate();
                }

                // Send confirmation email AND SMS
                try {
                    $result = $this->notificationService->sendShopOrderNotifications($order);

                    // Log results
                    if (!$result['email_sent']) {
                        Log::warning("Shop order email not sent for order {$order->order_number}");
                    }
                    if (!$result['sms_sent']) {
                        Log::warning("Shop order SMS not sent for order {$order->order_number}");
                    }
                    if (!empty($result['errors'])) {
                        Log::error("Shop order notification errors for {$order->order_number}: " . implode(', ', $result['errors']));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send order notifications: ' . $e->getMessage());
                }

                // Send SMS (from platform balance)
                if (!empty($order->customer_phone)) {
                    try {
                        $smsService = app(SmsService::class);
                        $platformAdmin = Admin::where('role', 'super_admin')->first();

                        if ($platformAdmin) {
                            $itemCount = $order->items->count();
                            $loginUrl = url('/login');

                            $smsMessage = "🛍️ Shop Order Confirmed!\n\n"
                                . "Order #: {$order->order_number}\n"
                                . "Items: {$itemCount}\n"
                                . "Total: GHS " . number_format($order->total, 2) . "\n"
                                . "Status: {$order->status}\n\n"
                                . "Login to track order: {$loginUrl}\n\n"
                                . "Check your email for full details.\n\n"
                                . "- 9yt !Trybe";

                            $smsService->sendSingleSms(
                                $platformAdmin,
                                $order->customer_phone,
                                $smsMessage,
                                null
                            );
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send shop order SMS', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                return redirect()->route('shop.order.confirmation', $order->order_number)
                    ->with('success', 'Payment successful! Your order has been confirmed. Check your email and SMS for details.');
            } else {
                $order->update(['payment_status' => 'failed']);

                return redirect()->route('shop.cart')
                    ->with('error', 'Payment verification failed.');
            }
        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage());
            return redirect()->route('shop.cart')
                ->with('error', 'Payment verification error.');
        }
    }

    public function confirmation($orderNumber)
    {
        $order = ShopOrder::with('items.product')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Verify ownership - require authentication for all orders for security
        // Guest orders must have session_id matching current session
        $sessionId = session()->getId();
        $userId = auth()->id();

        if ($order->user_id) {
            // Logged-in user's order - prompt login if session expired
            if (!$userId) {
                return redirect()->route('user.login', [
                    'redirect' => route('shop.order.confirmation', $order->order_number),
                ])->with('error', 'Please sign in to view your order.');
            }

            if ($order->user_id != $userId) {
                auth()->logout();
                return redirect()->route('user.login', [
                    'redirect' => route('shop.order.confirmation', $order->order_number),
                ])->with('error', 'Please sign in with the account used for this order.');
            }
        } else {
            // Guest order - must match session ID
            if ($order->session_id !== $sessionId) {
                return redirect()->route('shop.cart')
                    ->with('error', 'Please return to your cart to access this order confirmation.');
            }
        }

        return view('public.shop.order-confirmation', compact('order'));
    }

    private function calculateShipping($subtotal): float
    {
        // Free shipping for orders over GH₵200
        if ($subtotal >= 200) {
            return 0;
        }

        // Flat rate shipping
        return 20.00;
    }
}
