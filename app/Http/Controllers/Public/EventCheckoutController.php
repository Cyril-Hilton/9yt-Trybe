<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventOrder;
use App\Services\EventTicketService;
use App\Services\FeeCalculatorService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EventCheckoutController extends Controller
{
    protected EventTicketService $ticketService;
    protected FeeCalculatorService $feeCalculator;
    protected PaystackService $paystackService;

    public function __construct(
        EventTicketService $ticketService,
        FeeCalculatorService $feeCalculator,
        PaystackService $paystackService
    ) {
        $this->ticketService = $ticketService;
        $this->feeCalculator = $feeCalculator;
        $this->paystackService = $paystackService;
    }

    public function show(string $slug)
    {
        // AUTHENTICATION REQUIRED - Users must login/register to purchase tickets
        // This builds user database and enables better customer tracking

        $event = Event::where('slug', $slug)
            ->approved()
            ->with(['company', 'tickets' => function ($q) {
                $q->where('is_active', true)->orderBy('order');
            }])
            ->firstOrFail();

        // Get fee summary for display
        $feeSummary = $this->feeCalculator->getFeeSummary();

        // Get authenticated user (guaranteed to exist due to auth middleware)
        $user = Auth::user();

        // Pre-fill form with user data
        $customerData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
        ];

        return view('public.events.checkout', compact('event', 'feeSummary', 'user', 'customerData'));
    }

    public function processOrder(Request $request, string $slug)
    {
        // AUTHENTICATION REQUIRED - Only logged-in users can purchase

        $event = Event::where('slug', $slug)->approved()->firstOrFail();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'tickets' => 'required|array|min:1',
            'tickets.*' => 'integer|min:1',
            'attendees' => 'required|array|min:1',
            'attendees.*.name' => 'required|string|max:255',
            'attendees.*.email' => 'required|email',
            'attendees.*.ticket_id' => 'required|integer|exists:event_tickets,id',
            'payment_method' => 'nullable|string|in:card,mobile_money,all',
        ]);

        // Default to 'all' if not specified
        $paymentMethod = $validated['payment_method'] ?? 'all';

        // Check ticket availability
        $availabilityCheck = $this->ticketService->checkTicketAvailability($event, $validated['tickets']);

        if (!$availabilityCheck['available']) {
            return back()
                ->withInput()
                ->with('error', implode(', ', $availabilityCheck['errors']));
        }

        // Calculate order total
        $orderCalculation = $this->ticketService->calculateOrderTotal($event, $validated['tickets']);

        // If total is 0 (all free tickets), complete order immediately
        if ($orderCalculation['total'] == 0) {
            return $this->processFreeOrder($event, $validated, $orderCalculation);
        }

        // Get authenticated user ID (guaranteed to exist due to auth middleware)
        $userId = Auth::id();

        // Create pending order (linked to authenticated user)
        $orderData = [
            'user_id' => $userId, // Always has user ID (no guest checkout)
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'subtotal' => $orderCalculation['subtotal'],
            'service_fee' => $orderCalculation['service_fee'],
            'processing_fee' => $orderCalculation['processing_fee'],
            'platform_fee' => $orderCalculation['platform_fee'],
            'total' => $orderCalculation['attendee_pays'], // Amount customer actually pays
            'fee_bearer' => $event->fee_bearer,
        ];

        $order = $this->ticketService->createOrder($event, $orderData, $validated['tickets'], $validated['attendees']);

        // Initialize Paystack payment with selected payment method
        return $this->initializePaystackPayment($order, $paymentMethod);
    }

    protected function processFreeOrder(Event $event, array $validated, array $orderCalculation)
    {
        // Get authenticated user ID (guaranteed to exist due to auth middleware)
        $userId = Auth::id();

        $orderData = [
            'user_id' => $userId, // Always has user ID (no guest checkout)
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'subtotal' => 0,
            'service_fee' => 0,
            'processing_fee' => 0,
            'platform_fee' => 0,
            'total' => 0,
            'fee_bearer' => $event->fee_bearer,
        ];

        $order = $this->ticketService->createOrder($event, $orderData, $validated['tickets'], $validated['attendees']);

        // Complete order immediately for free tickets
        $this->ticketService->completeOrder($order, [
            'payment_method' => 'free',
            'reference' => 'FREE-' . $order->order_number,
        ]);

        return redirect()->route('events.order.confirmation', $order->order_number)
            ->with('success', 'Your free tickets have been confirmed!');
    }

    /**
     * Initialize Paystack payment with mobile money support
     * COMPETITIVE ADVANTAGE: Mobile money = 3x conversions in Ghana!
     *
     * @param EventOrder $order
     * @param string $paymentMethod 'card', 'mobile_money', or 'all'
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function initializePaystackPayment(EventOrder $order, string $paymentMethod = 'all')
    {
        if (!config('services.paystack.secret_key')) {
            return back()->with('error', 'Payment gateway not configured. Please contact support.');
        }

        try {
            $result = $this->paystackService->initializeTicketPayment(
                email: $order->customer_email,
                amount: $order->total,
                reference: $order->order_number,
                metadata: [
                    'order_number' => $order->order_number,
                    'event_title' => $order->event->title,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'ticket_count' => $order->attendees_count,
                ],
                callbackUrl: route('events.payment.callback'),
                paymentMethod: $paymentMethod
            );

            if ($result['success']) {
                // Store payment reference
                $order->update([
                    'payment_reference' => $result['reference'],
                ]);

                // Log payment method for analytics
                \Log::info('Payment initialized', [
                    'order' => $order->order_number,
                    'method' => $paymentMethod,
                    'amount' => $order->total,
                ]);

                // Redirect to Paystack payment page
                return redirect($result['authorization_url']);
            } else {
                \Log::error('Paystack initialization failed', ['error' => $result['error']]);
                return back()->with('error', 'Payment initialization failed. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('Paystack error: ' . $e->getMessage());
            return back()->with('error', 'Payment system error. Please try again.');
        }
    }

    public function paymentCallback(Request $request)
    {
        $reference = $request->input('reference');

        if (!$reference) {
            return redirect()->route('events.index')
                ->with('error', 'Invalid payment reference.');
        }

        // Find order by reference
        $order = EventOrder::where('payment_reference', $reference)
            ->orWhere('order_number', $reference)
            ->first();

        if (!$order) {
            return redirect()->route('events.index')
                ->with('error', 'Order not found.');
        }

        // Verify payment with Paystack
        $paystackSecretKey = config('services.paystack.secret_key');

        try {
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecretKey,
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            $data = $response->json();

            if ($response->successful() && $data['status'] && $data['data']['status'] === 'success') {
                // Payment successful - complete order
                $this->ticketService->completeOrder($order, [
                    'payment_method' => 'paystack',
                    'reference' => $reference,
                    'transaction_data' => $data['data'],
                ]);

                return redirect()->route('events.order.confirmation', $order->order_number)
                    ->with('success', 'Payment successful! Your tickets have been sent to your email.');
            } else {
                // Payment failed
                $order->update([
                    'payment_status' => 'failed',
                    'payment_response' => json_encode($data),
                ]);

                return redirect()->route('events.show', $order->event->slug)
                    ->with('error', 'Payment verification failed. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('Payment verification error: ' . $e->getMessage());

            return redirect()->route('events.show', $order->event->slug)
                ->with('error', 'Payment verification error. Please contact support.');
        }
    }

    public function confirmation(string $orderNumber)
    {
        $order = EventOrder::where('order_number', $orderNumber)
            ->with(['event', 'attendees.ticket'])
            ->firstOrFail();

        // Get authenticated user ID from either guard
        $currentUserId = Auth::check() ? Auth::id() : (Auth::guard('company')->check() ? Auth::guard('company')->id() : null);

        // Ensure user can view this order
        if (!$currentUserId) {
            return redirect()->route('user.login', [
                'redirect' => route('events.order.confirmation', $order->order_number),
            ])->with('error', 'Please sign in to view your tickets.');
        }

        if ($order->user_id != $currentUserId) {
            abort(403, 'Unauthorized access to this order.');
        }

        return view('public.events.order-confirmation', compact('order'));
    }
}
