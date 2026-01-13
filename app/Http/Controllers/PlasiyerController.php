<?php

namespace App\Http\Controllers;

use App\Models\CustomerActivity;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlasiyerController extends Controller
{
    /**
     * Show customer selection page for plasiyer and admin
     */
    public function selectCustomer()
    {
        if (!Auth::user()->isPlasiyer() && !Auth::user()->isAdmin()) {
            return redirect()->route('home');
        }
        
        // Admin ise tüm müşterileri, Plasiyer ise sadece kendi müşterilerini getir
        if (Auth::user()->isAdmin()) {
            $customers = \App\Models\User::where('user_type', 'musteri')
                ->where('is_active', true)
                ->get();
        } else {
            $customers = Auth::user()->customers()->get();
        }
        
        // Sepet bilgilerini ekle
        $customers = $customers->map(function($customer) {
            // Müşterinin sepetindeki ürün sayısı ve toplam tutarı hesapla
            $cartItems = \App\Models\Cart::with('product')->where('user_id', $customer->id)->get();
            $cartItemCount = $cartItems->count(); // Kaç kalem (farklı ürün)
            $cartQuantityTotal = $cartItems->sum('quantity'); // Toplam adet
            $cartTotal = 0;
            
            foreach ($cartItems as $item) {
                if ($item->product) {
                    $price = $item->product->net_fiyat_manuel ?? $item->product->depocu_fiyati ?? $item->product->satis_fiyati;
                    $cartTotal += $price * $item->quantity;
                }
            }
            
            $customer->cart_item_count = $cartItemCount; // Kalem sayısı
            $customer->cart_count = $cartQuantityTotal; // Toplam adet
            $customer->cart_total = $cartTotal; // Toplam tutar
            
            return $customer;
        });
        
        return view('plasiyer.select-customer', compact('customers'));
    }
    
    /**
     * Set selected customer for plasiyer and admin
     */
    public function setCustomer(Request $request)
    {
        if (!Auth::user()->isPlasiyer() && !Auth::user()->isAdmin()) {
            return redirect()->route('home');
        }
        
        $request->validate([
            'customer_id' => 'required|exists:users,id'
        ]);
        
        // Admin ise tüm müşteriler, Plasiyer ise sadece kendi müşterileri
        if (Auth::user()->isAdmin()) {
            $customer = \App\Models\User::where('user_type', 'musteri')
                ->where('is_active', true)
                ->find($request->customer_id);
        } else {
            $customer = Auth::user()->customers()->find($request->customer_id);
        }
        
        if (!$customer) {
            return back()->with('error', 'Geçersiz müşteri seçimi!');
        }
        
        // Session'da müşteriyi sakla
        session([
            'selected_customer_id' => $customer->id,
            'selected_customer_name' => $customer->name,
            'selected_customer_code' => $customer->username
        ]);
        
        return redirect()->route('home');
    }
    
    /**
     * Clear selected customer (return to customer selection)
     */
    public function clearCustomer()
    {
        if (!Auth::user()->isPlasiyer() && !Auth::user()->isAdmin()) {
            return redirect()->route('home');
        }
        
        // Session'ı silmeden müşteri seçim ekranına yönlendir
        // Yeni müşteri seçildiğinde session otomatik güncellenecek
        return redirect()->route('plasiyer.selectCustomer');
    }
    
    /**
     * Show plasiyer dashboard with statistics and charts
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Müşteri listesini getir
        if ($user->isAdmin()) {
            $customers = User::where('user_type', 'musteri')->where('is_active', true)->get();
        } else {
            $customers = $user->customers;
        }
        
        $customerIds = $customers->pluck('id');
        
        // Son 12 ayın istatistiklerini hazırla
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            // Ay için sipariş istatistikleri
            $ordersCount = Order::whereIn('user_id', $customerIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
                
            $ordersTotal = Order::whereIn('user_id', $customerIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total');
            
            // Ay için aktivite istatistikleri
            $activitiesCount = CustomerActivity::whereIn('user_id', $customerIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'orders_count' => $ordersCount,
                'orders_total' => $ordersTotal,
                'activities_count' => $activitiesCount,
            ];
        }
        
        // Özet istatistikler
        $stats = [
            'total_customers' => $customers->count(),
            'active_customers_this_month' => CustomerActivity::whereIn('user_id', $customerIds)
                ->where('created_at', '>=', now()->startOfMonth())
                ->distinct('user_id')
                ->count('user_id'),
            'orders_this_month' => Order::whereIn('user_id', $customerIds)
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
            'revenue_this_month' => Order::whereIn('user_id', $customerIds)
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('total'),
        ];
        
        return view('plasiyer.dashboard', compact('monthlyStats', 'stats'));
    }
    
}
