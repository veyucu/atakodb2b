<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    /**
     * Display order history
     */
    public function index(Request $request)
    {
        // Plasiyer veya Admin için seçili müşterinin siparişlerini göster
        // Normal müşteri için kendi siparişlerini göster
        if ((Auth::user()->isPlasiyer() || Auth::user()->isAdmin()) && session()->has('selected_customer_id')) {
            $userId = session('selected_customer_id');
            $customer = User::find($userId);

            if (!$customer) {
                return redirect()->route('home')->with('error', 'Müşteri bulunamadı');
            }
        } else {
            $userId = Auth::id();
            $customer = Auth::user();
        }

        // Siparişleri getir
        $query = Order::with(['user', 'items.product'])
            ->where('user_id', $userId);

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tarih filtresi
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20)->appends($request->except('page'));

        return view('orders.history', compact('orders', 'customer'));
    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {
        $user = Auth::user();

        // Admin ve Plasiyer tüm siparişleri görebilir
        // Normal kullanıcılar sadece kendi siparişlerini görebilir
        if (!$user->isAdmin() && !$user->isPlasiyer()) {
            if ((int) $order->user_id !== (int) Auth::id()) {
                abort(403, 'Bu siparişi görme yetkiniz yok.');
            }
        }

        $order->load(['user', 'items.product']);
        return view('orders.show', compact('order'));
    }
}
