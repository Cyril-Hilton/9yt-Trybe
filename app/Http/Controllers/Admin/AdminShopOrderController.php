<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use Illuminate\Http\Request;

class AdminShopOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $paymentStatus = $request->get('payment_status', 'all');

        $query = ShopOrder::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        $orders = $query->paginate(20);

        return view('admin.shop-orders.index', compact('orders', 'status', 'paymentStatus'));
    }

    public function show(ShopOrder $order)
    {
        $order->load(['user', 'items.product']);

        return view('admin.shop-orders.show', compact('order'));
    }

    public function updateStatus(Request $request, ShopOrder $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
        ]);

        $order->update($validated);

        return back()->with('success', 'Order status updated successfully!');
    }

    public function updatePaymentStatus(Request $request, ShopOrder $order)
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'in:pending,paid,failed,refunded'],
        ]);

        $order->update($validated);

        return back()->with('success', 'Payment status updated successfully!');
    }

    public function addNotes(Request $request, ShopOrder $order)
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->update($validated);

        return back()->with('success', 'Notes updated successfully!');
    }

    public function destroy(ShopOrder $order)
    {
        // Delete order items first
        $order->items()->delete();

        // Delete order
        $order->delete();

        return redirect()->route('admin.shop-orders.index')->with('success', 'Order deleted successfully!');
    }
}
