<?php

namespace App\Http\Controllers;

use App\Models\CustomerActivity;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the home page.
     */
    public function index(Request $request)
    {
        $sliders = Slider::active()->ordered()->get();

        $viewType = $request->get('view', 'catalog'); // catalog or list
        $stokta_olanlar = $request->get('stokta_olanlar', false);
        $kampanyali = $request->get('kampanyali', false);

        $query = Product::where('is_active', true);

        // Sadece stokta olanlar
        if ($stokta_olanlar) {
            $query->where('bakiye', '>', 0);
        }

        // Sadece kampanyalı ürünler (ozel_liste = true olanlar)
        if ($kampanyali) {
            $query->where('ozel_liste', true);
        }

        $products = $query->paginate(20);

        // Özel Kampanya ürünlerini çek (popup için)
        $specialCampaignProducts = Product::where('is_active', true)
            ->where('ozel_liste', true)
            ->get();

        // Kampanya popup'ı gösterildikten sonra session flag'ini temizle
        // (Bir sonraki sayfa yenilemesinde tekrar açılmasını önlemek için)
        $showCampaignPopup = session('show_campaign_popup', false);
        session()->forget('show_campaign_popup');

        return view('home', compact('sliders', 'products', 'viewType', 'stokta_olanlar', 'kampanyali', 'specialCampaignProducts', 'showCampaignPopup'));
    }

    /**
     * Search products.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $viewType = $request->get('view', 'catalog');
        $stokta_olanlar = $request->get('stokta_olanlar', false);
        $kampanyali = $request->get('kampanyali', false);

        $productsQuery = Product::where('is_active', true)
            ->searchWithEquivalents($query);

        // Sadece stokta olanlar
        if ($stokta_olanlar) {
            $productsQuery->where('bakiye', '>', 0);
        }

        // Sadece kampanyalı ürünler
        if ($kampanyali) {
            $productsQuery->where('ozel_liste', true);
        }

        $products = $productsQuery->paginate(20);

        // Arama aktivitesini kaydet
        if (Auth::check() && !empty($query)) {
            $userId = $this->getActivityUserId();
            CustomerActivity::logSearch($userId, $query, $products->total());
        }

        $sliders = Slider::active()->ordered()->get();

        // Özel Kampanya ürünlerini çek (popup için)
        $specialCampaignProducts = Product::where('is_active', true)
            ->where('ozel_liste', true)
            ->get();

        return view('home', compact('sliders', 'products', 'viewType', 'query', 'stokta_olanlar', 'kampanyali', 'specialCampaignProducts'));
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

    /**
     * Filter products by group.
     */
    public function filterByGroup(Request $request, $grup)
    {
        $sliders = Slider::active()->ordered()->get();
        $viewType = $request->get('view', 'catalog');
        $stokta_olanlar = $request->get('stokta_olanlar', false);
        $kampanyali = $request->get('kampanyali', false);

        $query = Product::where('is_active', true)
            ->where('grup', $grup);

        // Sadece stokta olanlar
        if ($stokta_olanlar) {
            $query->where('bakiye', '>', 0);
        }

        // Sadece kampanyalı ürünler
        if ($kampanyali) {
            $query->where('ozel_liste', true);
        }

        $products = $query->paginate(20);

        // Özel Kampanya ürünlerini çek (popup için)
        $specialCampaignProducts = Product::where('is_active', true)
            ->where('ozel_liste', true)
            ->get();

        return view('home', compact('sliders', 'products', 'viewType', 'stokta_olanlar', 'kampanyali', 'grup', 'specialCampaignProducts'));
    }

    /**
     * Get muadil (equivalent) products by muadil code.
     */
    public function getMuadilProducts(Request $request, $muadilKodu)
    {
        $excludeId = $request->query('exclude'); // Ana ürünün ID'si

        $query = Product::where('muadil_kodu', $muadilKodu)
            ->where('is_active', true);

        // Ana ürünü hariç tut
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $products = $query->get()->map(function ($product) {
            // Net fiyat: Manuel girilmişse onu kullan, yoksa hesaplanmış net_price
            $netFiyat = $product->net_fiyat_manuel ?? $product->net_price;

            return [
                'id' => $product->id,
                'urun_kodu' => $product->urun_kodu,
                'urun_adi' => $product->urun_adi,
                'satis_fiyati' => $product->satis_fiyati,
                'satis_fiyati_formatted' => number_format($product->satis_fiyati, 2, ',', '.') . ' ₺',
                'depocu_fiyati' => $product->depocu_fiyati,
                'depocu_fiyati_formatted' => $product->depocu_fiyati ? number_format($product->depocu_fiyati, 2, ',', '.') . ' ₺' : null,
                'mf' => $product->mf,
                'net_fiyat' => $netFiyat,
                'net_fiyat_formatted' => number_format($netFiyat, 2, ',', '.') . ' ₺',
                'bakiye' => $product->bakiye,
                'stokta' => $product->bakiye > 0,
                'kampanyali' => $product->ozel_liste ? true : false,
                'image_url' => $product->image_url,
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
}



