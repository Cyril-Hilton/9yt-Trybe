<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EventOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get user's recent orders
        $recentOrders = EventOrder::where('user_id', $user->id)
            ->with(['event'])
            ->latest()
            ->take(5)
            ->get();

        // Count statistics
        $stats = [
            'total_orders' => EventOrder::where('user_id', $user->id)->count(),
            'upcoming_events' => EventOrder::where('user_id', $user->id)
                ->whereHas('event', function($q) {
                    $q->where('start_date', '>=', now());
                })
                ->count(),
            'past_events' => EventOrder::where('user_id', $user->id)
                ->whereHas('event', function($q) {
                    $q->where('start_date', '<', now());
                })
                ->count(),
        ];

        return view('user.dashboard', compact('user', 'recentOrders', 'stats'));
    }

    public function tickets()
    {
        $user = Auth::user();

        // Get all orders with events
        $orders = EventOrder::where('user_id', $user->id)
            ->with(['event', 'attendees.ticket'])
            ->latest()
            ->paginate(10);

        return view('user.tickets', compact('orders'));
    }

    public function ticketDetails(EventOrder $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['event', 'attendees.ticket']);

        return view('user.ticket-details', compact('order'));
    }
}
