<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get the current user ID (for plasiyer/admin: selected customer, for others: auth user)
     */
    protected function getCurrentUserId()
    {
        if ((Auth::user()->isPlasiyer() || Auth::user()->isAdmin()) && session()->has('selected_customer_id')) {
            return session('selected_customer_id');
        }
        return Auth::id();
    }

    /**
     * Display cart items.
     */
    public function index(Request $request)
    {
        $cartItems = Cart::with(['product'])
            ->where('user_id', $this->getCurrentUserId())
            ->get();

        // Net fiyatlar zaten KDV dahil
        $totalWithVat = $cartItems->sum(fn($item) => $item->total); // KDV Dahil Toplam
        $totalWithoutVat = $cartItems->sum(fn($item) => $item->total_without_vat); // KDV Hariç Toplam

        // KDV'leri oranlarına göre grupla
        $vatByRate = [];
        foreach ($cartItems as $item) {
            $vatRate = $item->product->kdv_orani ?? 0;
            if (!isset($vatByRate[$vatRate])) {
                $vatByRate[$vatRate] = 0;
            }
            $vatByRate[$vatRate] += $item->vat_amount;
        }

        // Oranları küçükten büyüğe sırala
        ksort($vatByRate);

        $totalVat = array_sum($vatByRate);

        // If AJAX request, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'subtotal' => $totalWithoutVat,
                'vat_by_rate' => $vatByRate,
                'total_vat' => $totalVat,
                'total' => $totalWithVat,
            ]);
        }

        $siteSettings = \App\Models\SiteSetting::getSettings();

        return view('cart.index', compact('cartItems', 'totalWithoutVat', 'vatByRate', 'totalVat', 'totalWithVat', 'siteSettings'));
    }

    /**
     * Add product to cart.
     * Sadece miktar kaydedilir, fiyatlar dinamik hesaplanır
     */
    public function add(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:0|max:999999',
            ], [
                'product_id.required' => 'Ürün seçilmedi',
                'product_id.exists' => 'Geçersiz ürün',
                'quantity.required' => 'Miktar belirtilmedi',
                'quantity.integer' => 'Miktar sayı olmalı',
                'quantity.min' => 'Miktar en az 0 olmalı',
                'quantity.max' => 'Miktar en fazla 999.999 olabilir',
            ]);

            $product = Product::findOrFail($request->product_id);

            // Stok kontrolü - Stokta yok mu?
            if ($product->bakiye <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu ürün stokta yok!'
                ], 400);
            }

            // Sepetteki mevcut miktarı kontrol et
            $cartItem = Cart::where('user_id', $this->getCurrentUserId())
                ->where('product_id', $product->id)
                ->first();

            $mevcutSepetMiktari = $cartItem ? $cartItem->quantity : 0;
            $toplamSiparisMiktari = $mevcutSepetMiktari + $request->quantity;

            // Stok kontrolü
            if ($toplamSiparisMiktari > $product->bakiye) {
                $kalanStok = $product->bakiye - $mevcutSepetMiktari;
                if ($kalanStok > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok miktarı yetersiz! Maksimum ' . number_format((float) $kalanStok, 0) . ' adet sipariş verebilirsiniz. (Stok: ' . number_format((float) $product->bakiye, 0) . ')'
                    ], 400);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bu ürün sepetinizde zaten mevcut ve stok miktarına ulaşıldı!'
                    ], 400);
                }
            }

            if ($cartItem) {
                // Mevcut kaydı güncelle - sadece miktar
                $cartItem->quantity = $toplamSiparisMiktari;
                $cartItem->save();
            } else {
                // Yeni kayıt - sadece user_id, product_id, quantity
                Cart::create([
                    'user_id' => $this->getCurrentUserId(),
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ürün sepete eklendi',
                'cart_count' => Cart::where('user_id', $this->getCurrentUserId())->sum('quantity'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart add error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ürün sepete eklenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update cart item quantity.
     * Sadece miktar güncellenir, fiyatlar dinamik hesaplanır
     */
    public function update(Request $request, $id)
    {
        try {
            $cart = Cart::where('id', $id)
                ->where('user_id', $this->getCurrentUserId())
                ->first();

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün bulunamadı veya size ait değil',
                ], 404);
            }

            $request->validate([
                'quantity' => 'required|integer|min:1|max:999999',
            ], [
                'quantity.required' => 'Miktar belirtilmedi',
                'quantity.integer' => 'Miktar sayı olmalı',
                'quantity.min' => 'Miktar en az 1 olmalı',
                'quantity.max' => 'Miktar en fazla 999.999 olabilir',
            ]);

            // Stok kontrolü
            $product = $cart->product;

            if ($request->quantity > $product->bakiye) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok miktarı yetersiz! Stokta ' . number_format($product->bakiye, 0, ',', '.') . ' adet var.',
                ], 400);
            }

            $cart->quantity = $request->quantity;
            $cart->save();

            // Refresh to get updated calculated values
            $cart->refresh();
            $cart->load('product');

            return response()->json([
                'success' => true,
                'message' => 'Sepet güncellendi',
                'total' => $cart->total, // KDV Dahil
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sepet güncellenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove item from cart.
     */
    public function remove($id)
    {
        try {
            \Log::info('Attempting to delete cart item', [
                'requested_id' => $id,
                'user_id' => $this->getCurrentUserId(),
                'all_user_carts' => Cart::where('user_id', $this->getCurrentUserId())->pluck('id')->toArray(),
                'all_carts_db' => Cart::pluck('id', 'user_id')->toArray()
            ]);

            $cart = Cart::where('id', $id)
                ->where('user_id', $this->getCurrentUserId())
                ->first();

            if (!$cart) {
                \Log::warning('Cart item not found', [
                    'requested_id' => $id,
                    'user_id' => $this->getCurrentUserId(),
                    'exists_any_user' => Cart::where('id', $id)->exists(),
                    'user_cart_ids' => Cart::where('user_id', $this->getCurrentUserId())->pluck('id')->toArray()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün bulunamadı veya size ait değil (ID: ' . $id . ', User: ' . $this->getCurrentUserId() . ')',
                ], 404);
            }

            $cart->delete();
            \Log::info('Cart item deleted successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Ürün sepetten çıkarıldı',
                'cart_count' => Cart::where('user_id', $this->getCurrentUserId())->sum('quantity'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart delete error', [
                'id' => $id,
                'user_id' => $this->getCurrentUserId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ürün silinirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear entire cart.
     */
    public function clear()
    {
        try {
            Cart::where('user_id', $this->getCurrentUserId())->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sepet temizlendi',
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart clear error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sepet temizlenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get cart count and total.
     */
    public function count()
    {
        $cartItems = Cart::with('product')->where('user_id', $this->getCurrentUserId())->get();
        $count = $cartItems->sum('quantity');
        $total = 0;

        foreach ($cartItems as $item) {
            if ($item->product) {
                // Net fiyatı kullan, yoksa depocu fiyatı, yoksa satış fiyatı
                $price = $item->product->net_fiyat_manuel ?? $item->product->depocu_fiyati ?? $item->product->satis_fiyati;
                $total += $price * $item->quantity;
            }
        }

        return response()->json([
            'count' => $count,
            'total' => number_format($total, 2, ',', '.'),
        ]);
    }

    /**
     * Checkout - Create order from cart.
     */
    public function checkout(Request $request)
    {
        $userId = $this->getCurrentUserId();

        $cartItems = Cart::with('product')
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->count() == 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Sepetiniz boş!');
        }

        try {
            \DB::beginTransaction();

            // Calculate totals (net fiyatlar zaten KDV dahil)
            $totalWithVat = $cartItems->sum(fn($item) => $item->total); // KDV Dahil Toplam
            $totalWithoutVat = $cartItems->sum(fn($item) => $item->total_without_vat); // KDV Hariç Toplam
            $vat = $cartItems->sum(fn($item) => $item->vat_amount); // KDV Tutarı

            // Create order
            $order = \App\Models\Order::create([
                'order_number' => \App\Models\Order::generateOrderNumber(),
                'user_id' => $userId, // Plasiyer için seçili müşteri, normal kullanıcı için kendisi
                'customer_id' => null,
                'status' => 'pending',
                'subtotal' => $totalWithoutVat,
                'vat' => $vat,
                'total' => $totalWithVat,
                'notes' => $request->notes,
                'gonderim_sekli' => $request->gonderim_sekli,
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                // Seçili bonus opsiyonuna göre MF pattern'i al
                $bonusOptions = $cartItem->product->bonus_options ?? [];
                $selectedOption = $cartItem->bonus_option;
                $mfPattern = null;

                $option = collect($bonusOptions)->firstWhere('option', $selectedOption);
                if ($option && isset($option['label']) && !empty($option['label'])) {
                    $mfPattern = $option['label']; // Örn: "10+1" or "15+5"
                }

                \Log::info('OrderItem Create', [
                    'product_id' => $cartItem->product_id,
                    'bonus_option' => $selectedOption,
                    'mf_pattern' => $mfPattern,
                    'net_price' => $cartItem->net_price,
                ]);

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->net_price, // Net fiyatı price'a da yazıyoruz
                    'vat_rate' => $cartItem->product->kdv_orani,
                    'total' => $cartItem->total, // KDV Dahil
                    'net_fiyat' => $cartItem->net_price,
                    'mal_fazlasi' => $mfPattern,
                ]);
            }

            // Clear cart
            Cart::where('user_id', $userId)->delete();

            \DB::commit();

            return redirect()->route('order.success', $order)
                ->with('success', 'Siparişiniz başarıyla oluşturuldu!');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->route('cart.index')
                ->with('error', 'Sipariş oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Order success page.
     */
    public function orderSuccess(\App\Models\Order $order)
    {
        // Ensure the order belongs to the current user (or selected customer for plasiyer)
        if ($order->user_id !== $this->getCurrentUserId()) {
            abort(403);
        }

        $order->load(['items.product']);
        return view('cart.order-success', compact('order'));
    }
}



