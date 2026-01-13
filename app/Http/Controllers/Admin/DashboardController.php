<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerActivity;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_users' => User::count(),
            'admin_users' => User::where('user_type', 'admin')->count(),
            'plasiyer_users' => User::where('user_type', 'plasiyer')->count(),
            'musteri_users' => User::where('user_type', 'musteri')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::whereNotIn('status', ['cancelled'])->sum('total'),
        ];

        // Tüm müşterilerin aktivite istatistikleri (son 30 gün)
        $last30Days = now()->subDays(30);
        
        $activityStats = [
            'total_searches' => CustomerActivity::where('activity_type', 'search')
                ->where('created_at', '>=', $last30Days)
                ->count(),
            
            'total_product_views' => CustomerActivity::where('activity_type', 'product_view')
                ->where('created_at', '>=', $last30Days)
                ->count(),
            
            'total_logins' => CustomerActivity::where('activity_type', 'login')
                ->where('created_at', '>=', $last30Days)
                ->count(),
            
            'active_customers_count' => CustomerActivity::where('created_at', '>=', $last30Days)
                ->distinct('user_id')
                ->count('user_id'),
            
            'top_searches' => CustomerActivity::where('activity_type', 'search')
                ->where('created_at', '>=', $last30Days)
                ->get()
                ->pluck('activity_data')
                ->filter()
                ->groupBy('query')
                ->map(function ($group) {
                    return [
                        'query' => $group[0]['query'],
                        'count' => $group->count(),
                    ];
                })
                ->sortByDesc('count')
                ->take(10)
                ->values(),
            
            'most_active_customers' => CustomerActivity::select('user_id', DB::raw('COUNT(*) as activity_count'))
                ->where('created_at', '>=', $last30Days)
                ->groupBy('user_id')
                ->orderByDesc('activity_count')
                ->take(10)
                ->with('user:id,name,username')
                ->get(),
        ];

        return view('admin.dashboard', compact('stats', 'activityStats'));
    }
}



