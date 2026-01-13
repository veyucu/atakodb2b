<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            $siteSettings = null;

            if (Schema::hasTable('settings')) {
                $siteSettings = Cache::rememberForever('site_settings', function () {
                    return Setting::first();
                });
            }

            $view->with('siteSettings', $siteSettings);
            
            // Sepet bilgilerini view'a ekle (giriş yapmış kullanıcılar için)
            $cartCount = 0;
            $cartTotal = '0,00';
            
            if (Auth::check() && Schema::hasTable('carts')) {
                // Plasiyer veya Admin için seçili müşterinin sepetini, normal kullanıcı için kendi sepetini al
                $userId = (Auth::user()->isPlasiyer() || Auth::user()->isAdmin()) && session()->has('selected_customer_id') 
                    ? session('selected_customer_id') 
                    : Auth::id();
                    
                $cartItems = Cart::with('product')->where('user_id', $userId)->get();
                $cartCount = $cartItems->sum('quantity');
                $total = 0;

                foreach ($cartItems as $item) {
                    if ($item->product) {
                        $price = $item->product->net_fiyat_manuel ?? $item->product->depocu_fiyati ?? $item->product->satis_fiyati;
                        $total += $price * $item->quantity;
                    }
                }

                $cartTotal = number_format($total, 2, ',', '.');
            }
            
            $view->with('initialCartCount', $cartCount);
            $view->with('initialCartTotal', $cartTotal);
        });
    }
}


