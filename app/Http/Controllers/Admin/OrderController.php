<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);
        
        // Filter by customer (for plasiyer/admin customer selection)
        $selectedCustomer = null;
        if ($request->filled('customer_id')) {
            $customerId = $request->customer_id;
            $query->where('user_id', $customerId);
            $selectedCustomer = \App\Models\User::find($customerId);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw('order_number COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$search}%"])
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->whereRaw('name COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('email COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        $orders = $query->latest()->paginate(20)->appends($request->except('page'));

        return view('admin.orders.index', compact('orders', 'selectedCustomer'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Sipariş durumu güncellendi.');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Sipariş silindi.');
    }
}








