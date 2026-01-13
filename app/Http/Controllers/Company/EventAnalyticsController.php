<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventAnalyticsController extends Controller
{
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        // Load relationships
        $event->load(['tickets', 'orders', 'attendees', 'views', 'likes']);

        // Overall stats
        $stats = [
            'total_views' => $event->views_count,
            'unique_viewers' => $event->views()->distinct('user_id')->count('user_id'),
            'total_likes' => $event->likes_count,
            'total_tickets_sold' => $event->tickets_sold,
            'total_revenue' => $event->total_revenue,
            'total_orders' => $event->orders()->where('payment_status', 'completed')->count(),
            'pending_orders' => $event->orders()->where('payment_status', 'pending')->count(),
            'failed_orders' => $event->orders()->where('payment_status', 'failed')->count(),
            'checked_in' => $event->attendees()->where('checked_in', true)->count(),
            'not_checked_in' => $event->attendees()->where('checked_in', false)->count(),
        ];

        // Ticket sales breakdown
        $ticketSales = $event->tickets()->withCount('attendees')->get()->map(function ($ticket) {
            return [
                'name' => $ticket->name,
                'type' => $ticket->type,
                'price' => $ticket->price,
                'sold' => $ticket->sold,
                'total_quantity' => $ticket->quantity ?? 'Unlimited',
                'revenue' => $ticket->sold * $ticket->price,
            ];
        });

        // Daily sales for chart (last 30 days)
        $dailySales = $event->orders()
            ->where('payment_status', 'completed')
            ->where('paid_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(paid_at) as date, COUNT(*) as orders, SUM(subtotal) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Hourly sales for chart (last 24 hours)
        $hourlySales = $event->orders()
            ->where('payment_status', 'completed')
            ->where('paid_at', '>=', now()->subDay())
            ->selectRaw('HOUR(paid_at) as hour, COUNT(*) as orders')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Traffic sources (views by referrer)
        $viewSources = $event->views()
            ->selectRaw('SUBSTRING_INDEX(user_agent, " ", 1) as source, COUNT(*) as views')
            ->groupBy('source')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        return view('company.events.analytics', compact(
            'event',
            'stats',
            'ticketSales',
            'dailySales',
            'hourlySales',
            'viewSources'
        ));
    }
}
