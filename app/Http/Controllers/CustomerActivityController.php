<?php

namespace App\Http\Controllers;

use App\Models\CustomerActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerActivityController extends Controller
{
    /**
     * Show customer activities for plasiyer/admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $customersQuery = User::where('user_type', 'musteri')->where('is_active', true);
        
        // Plasiyer için sadece kendi müşterilerini göster
        if ($user->isPlasiyer()) {
            $customersQuery->where('plasiyer_kodu', $user->plasiyer_kodu);
        }
        
        $customers = $customersQuery->orderBy('name')->get();
        
        // Eğer request'te customer_id yoksa ama session'da varsa, session'dakini kullan
        $customerId = $request->get('customer_id');
        if (!$customerId && session()->has('selected_customer_id')) {
            $customerId = session('selected_customer_id');
        }
        
        // Eğer müşteri seçiliyse aktivitelerini göster
        $selectedCustomer = null;
        $activities = collect();
        $stats = null;
        
        if ($customerId) {
            $selectedCustomer = User::find($customerId);
            
            if ($selectedCustomer) {
                // Son 30 günün aktivitelerini getir
                $activities = CustomerActivity::where('user_id', $selectedCustomer->id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(50);
                
                // İstatistikleri hesapla
                $stats = $this->calculateStats($selectedCustomer->id);
            }
        }
        
        return view('plasiyer.customer-activities', compact('customers', 'selectedCustomer', 'activities', 'stats'));
    }
    
    /**
     * Log campaign popup view
     */
    public function logCampaignView(Request $request)
    {
        $userId = Auth::id();
        
        // Plasiyer/Admin ise seçili müşteri için kaydet
        if ((Auth::user()->isPlasiyer() || Auth::user()->isAdmin()) && session()->has('selected_customer_id')) {
            $userId = session('selected_customer_id');
        }
        
        CustomerActivity::logCampaignPopup($userId);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Calculate activity statistics for a customer
     */
    protected function calculateStats($userId)
    {
        $last30Days = now()->subDays(30);
        
        return [
            'total_searches' => CustomerActivity::where('user_id', $userId)
                ->where('activity_type', 'search')
                ->where('created_at', '>=', $last30Days)
                ->count(),
            
            'total_product_views' => CustomerActivity::where('user_id', $userId)
                ->where('activity_type', 'product_view')
                ->where('created_at', '>=', $last30Days)
                ->count(),
            
            'total_modal_views' => CustomerActivity::where('user_id', $userId)
                ->where('activity_type', 'product_modal_view')
                ->where('created_at', '>=', $last30Days)
                ->count(),
            
            'total_campaign_views' => CustomerActivity::where('user_id', $userId)
                ->where('activity_type', 'campaign_popup')
                ->where('created_at', '>=', $last30Days)
                ->count(),
            
            'total_logins' => CustomerActivity::where('user_id', $userId)
                ->where('activity_type', 'login')
                ->where('created_at', '>=', $last30Days)
                ->count(),
            
            'login_dates' => CustomerActivity::where('user_id', $userId)
                ->where('activity_type', 'login')
                ->where('created_at', '>=', $last30Days)
                ->orderBy('created_at', 'desc')
                ->get()
                ->pluck('created_at'),
            
            'top_searches' => CustomerActivity::where('user_id', $userId)
                ->where('activity_type', 'search')
                ->where('created_at', '>=', $last30Days)
                ->get()
                ->pluck('activity_data')
                ->filter()
                ->groupBy('query')
                ->map(function ($group) {
                    return [
                        'query' => $group[0]['query'],
                        'count' => $group->count(),
                        'avg_results' => round($group->avg('result_count'), 1),
                    ];
                })
                ->sortByDesc('count')
                ->take(10)
                ->values(),
            
            'top_viewed_products' => CustomerActivity::where('user_id', $userId)
                ->whereIn('activity_type', ['product_view', 'product_modal_view'])
                ->where('created_at', '>=', $last30Days)
                ->get()
                ->pluck('activity_data')
                ->filter()
                ->groupBy('product_id')
                ->map(function ($group) {
                    return [
                        'product_id' => $group[0]['product_id'],
                        'product_name' => $group[0]['product_name'],
                        'product_code' => $group[0]['product_code'],
                        'view_count' => $group->count(),
                    ];
                })
                ->sortByDesc('view_count')
                ->take(10)
                ->values(),
                
            'last_login' => User::find($userId)->last_login_at,
        ];
    }
}
