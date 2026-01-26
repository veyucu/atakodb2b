<?php

namespace App\Http\Controllers;

use App\Models\CustomerActivity;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Slider;
use Carbon\Carbon;
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

        // Filtre yoksa: önce resimli ürünleri, sonra resimsizleri getir
        if (!$stokta_olanlar && !$kampanyali && !$request->has('q')) {
            $query->orderByRaw('CASE WHEN urun_resmi IS NOT NULL AND urun_resmi != \'\' THEN 0 ELSE 1 END');
        }

        $products = $query->paginate(100);

        // Özel Kampanya ürünlerini çek (popup için)
        $specialCampaignProducts = Product::where('is_active', true)
            ->where('ozel_liste', true)
            ->get();

        // Kampanya popup'ı gösterildikten sonra session flag'ini temizle
        // (Bir sonraki sayfa yenilemesinde tekrar açılmasını önlemek için)
        $showCampaignPopup = session('show_campaign_popup', false);
        session()->forget('show_campaign_popup');

        // Son 7 günde en çok satan 10 ürün
        $topSellingProducts = OrderItem::select('product_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->whereHas('order', function ($q) {
                $q->where('created_at', '>=', Carbon::now()->subDays(7));
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->with('product')
            ->get()
            ->filter(fn($item) => $item->product && $item->product->is_active && $item->product->bakiye > 0)
            ->map(fn($item) => $item->product);

        // Eğer 10 ürün yoksa, stokta olan ve resmi olan random ürünlerle tamamla
        if ($topSellingProducts->count() < 10) {
            $existingIds = $topSellingProducts->pluck('id')->toArray();
            $needed = 10 - $topSellingProducts->count();

            $randomProducts = Product::where('is_active', true)
                ->where('bakiye', '>', 0)
                ->whereNotNull('urun_resmi')
                ->whereNotIn('id', $existingIds)
                ->inRandomOrder()
                ->limit($needed)
                ->get();

            $topSellingProducts = $topSellingProducts->concat($randomProducts);
        }

        return view('home', compact('sliders', 'products', 'viewType', 'stokta_olanlar', 'kampanyali', 'specialCampaignProducts', 'showCampaignPopup', 'topSellingProducts'));
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

        $products = $productsQuery->paginate(100);

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

        // Son 7 günde en çok satan 10 ürün
        $topSellingProducts = OrderItem::select('product_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->whereHas('order', function ($q) {
                $q->where('created_at', '>=', Carbon::now()->subDays(7));
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->with('product')
            ->get()
            ->filter(fn($item) => $item->product && $item->product->is_active && $item->product->bakiye > 0)
            ->map(fn($item) => $item->product);

        // Eğer 10 ürün yoksa, stokta olan ve resmi olan random ürünlerle tamamla
        if ($topSellingProducts->count() < 10) {
            $existingIds = $topSellingProducts->pluck('id')->toArray();
            $needed = 10 - $topSellingProducts->count();

            $randomProducts = Product::where('is_active', true)
                ->where('bakiye', '>', 0)
                ->whereNotNull('urun_resmi')
                ->whereNotIn('id', $existingIds)
                ->inRandomOrder()
                ->limit($needed)
                ->get();

            $topSellingProducts = $topSellingProducts->concat($randomProducts);
        }

        return view('home', compact('sliders', 'products', 'viewType', 'query', 'stokta_olanlar', 'kampanyali', 'specialCampaignProducts', 'topSellingProducts'));
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

        $products = $query->paginate(100);

        // Özel Kampanya ürünlerini çek (popup için)
        $specialCampaignProducts = Product::where('is_active', true)
            ->where('ozel_liste', true)
            ->get();

        // Son 7 günde en çok satan 10 ürün
        $topSellingProducts = OrderItem::select('product_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->whereHas('order', function ($q) {
                $q->where('created_at', '>=', Carbon::now()->subDays(7));
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->with('product')
            ->get()
            ->filter(fn($item) => $item->product && $item->product->is_active && $item->product->bakiye > 0)
            ->map(fn($item) => $item->product);

        // Eğer 10 ürün yoksa, stokta olan ve resmi olan random ürünlerle tamamla
        if ($topSellingProducts->count() < 10) {
            $existingIds = $topSellingProducts->pluck('id')->toArray();
            $needed = 10 - $topSellingProducts->count();

            $randomProducts = Product::where('is_active', true)
                ->where('bakiye', '>', 0)
                ->whereNotNull('urun_resmi')
                ->whereNotIn('id', $existingIds)
                ->inRandomOrder()
                ->limit($needed)
                ->get();

            $topSellingProducts = $topSellingProducts->concat($randomProducts);
        }

        return view('home', compact('sliders', 'products', 'viewType', 'stokta_olanlar', 'kampanyali', 'grup', 'specialCampaignProducts', 'topSellingProducts'));
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
            // Net fiyat: Önce net_fiyat1'i dene, yoksa net_price
            $netFiyat = $product->net_fiyat1 ?? $product->net_price;

            return [
                'id' => $product->id,
                'urun_kodu' => $product->urun_kodu,
                'urun_adi' => $product->urun_adi,
                'satis_fiyati' => $product->satis_fiyati,
                'satis_fiyati_formatted' => number_format($product->satis_fiyati, 2, ',', '.') . ' ₺',
                'depocu_fiyati' => $product->depocu_fiyati,
                'depocu_fiyati_formatted' => $product->depocu_fiyati ? number_format($product->depocu_fiyati, 2, ',', '.') . ' ₺' : null,
                'mf' => $product->mf1, // Backward compatibility
                'mf1' => $product->mf1,
                'mf2' => $product->mf2,
                'net_fiyat' => $netFiyat,
                'net_fiyat_formatted' => number_format($netFiyat, 2, ',', '.') . ' ₺',
                'net_fiyat1' => $product->net_fiyat1,
                'net_fiyat1_formatted' => $product->net_fiyat1 ? number_format($product->net_fiyat1, 2, ',', '.') . ' ₺' : null,
                'net_fiyat2' => $product->net_fiyat2,
                'net_fiyat2_formatted' => $product->net_fiyat2 ? number_format($product->net_fiyat2, 2, ',', '.') . ' ₺' : null,
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



