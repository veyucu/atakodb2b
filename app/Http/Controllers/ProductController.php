<?php

namespace App\Http\Controllers;

use App\Models\CustomerActivity;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display the product detail page.
     */
    public function show($id)
    {
        $product = Product::where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();
        
        // Ürün görüntüleme aktivitesini kaydet
        if (Auth::check()) {
            $userId = $this->getActivityUserId();
            CustomerActivity::logProductView($userId, $product->id, $product->urun_adi, $product->urun_kodu);
        }
        
        // Muadil ürünleri getir
        $muadilProducts = collect();
        if ($product->muadil_kodu) {
            $muadilProducts = Product::where('muadil_kodu', $product->muadil_kodu)
                ->where('is_active', true)
                ->where('id', '!=', $product->id)
                ->get();
        }
        
        return view('products.show', compact('product', 'muadilProducts'));
    }

    /**
     * Display the product detail in modal (AJAX).
     */
    public function showModal($id)
    {
        $product = Product::where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();
        
        // Ürün modal görüntüleme aktivitesini kaydet
        if (Auth::check()) {
            $userId = $this->getActivityUserId();
            CustomerActivity::logProductModalView($userId, $product->id, $product->urun_adi, $product->urun_kodu);
        }
        
        // Muadil ürünleri getir
        $muadilProducts = collect();
        if ($product->muadil_kodu) {
            $muadilProducts = Product::where('muadil_kodu', $product->muadil_kodu)
                ->where('is_active', true)
                ->where('id', '!=', $product->id)
                ->get();
        }
        
        return view('products.modal', compact('product', 'muadilProducts'));
    }
    
    /**
     * Get user ID for activity logging (selected customer for plasiyer/admin, auth user for others)
     */
    protected function getActivityUserId()
    {
        if ((Auth::user()->isPlasiyer() || Auth::user()->isAdmin()) && session()->has('selected_customer_id')) {
            return session('selected_customer_id');
        }
        return Auth::id();
    }
}



