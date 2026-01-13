<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Normal müşteriler için bu kontrolü atla
        if (auth()->check() && (auth()->user()->isPlasiyer() || auth()->user()->isAdmin())) {
            // Plasiyer ve Admin için müşteri seçimi kontrolü
            if (!session()->has('selected_customer_id')) {
                // Müşteri seçim sayfasına yönlendir (bu sayfa hariç)
                if (!$request->is('plasiyer/select-customer') && !$request->is('plasiyer/set-customer') && !$request->is('admin/*')) {
                    return redirect()->route('plasiyer.selectCustomer');
                }
            }
        }
        
        return $next($request);
    }
}
