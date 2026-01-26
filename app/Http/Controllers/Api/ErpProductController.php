<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ErpProductController extends Controller
{
    /**
     * API Key doğrulama
     */
    private function validateApiKey(Request $request): bool
    {
        $apiKey = $request->header('X-API-Key');
        $expectedKey = config('services.erp.api_key');
        return $apiKey === $expectedKey;
    }

    /**
     * Yeni ürün oluştur (Insert)
     */
    public function store(Request $request)
    {
        if (!$this->validateApiKey($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('ERP Product Store', ['data' => $request->all()]);

        $validated = $request->validate([
            'UrunKodu' => 'required|string|max:255',
            'UrunAdi' => 'required|string|max:255',
            'Barkod' => 'nullable|string|max:255',
            'Grup' => 'nullable|string|max:255',
            'Marka' => 'nullable|string|max:255',
            'KdvOrani' => 'nullable|numeric',
            'MuadilKodu' => 'nullable|string|max:255',
            'EtkenMadde' => 'nullable|string|max:255',
            'SatisFiyati' => 'nullable|numeric',
            'EczaciKari' => 'nullable|numeric',
            'KurumIskonto' => 'nullable|numeric',
            'TicariIskonto' => 'nullable|numeric',
            'DepocuFiyati' => 'nullable|numeric',
            'Mf1' => 'nullable|string|max:255',
            'NetFiyat1' => 'nullable|numeric',
            'Mf2' => 'nullable|string|max:255',
            'NetFiyat2' => 'nullable|numeric',
        ]);

        // Ürün zaten var mı kontrol et
        $existingProduct = Product::where('urun_kodu', $validated['UrunKodu'])->first();

        if ($existingProduct) {
            // Varsa güncelle
            $existingProduct->update([
                'urun_adi' => $validated['UrunAdi'],
                'barkod' => $validated['Barkod'] ?? $existingProduct->barkod,
                'grup' => $validated['Grup'] ?? $existingProduct->grup,
                'marka' => $validated['Marka'] ?? $existingProduct->marka,
                'kdv_orani' => $validated['KdvOrani'] ?? $existingProduct->kdv_orani,
                'muadil_kodu' => $validated['MuadilKodu'] ?? $existingProduct->muadil_kodu,
                'etken_madde' => $validated['EtkenMadde'] ?? $existingProduct->etken_madde,
                'satis_fiyati' => $validated['SatisFiyati'] ?? $existingProduct->satis_fiyati,
                'eczaci_kari' => $validated['EczaciKari'] ?? $existingProduct->eczaci_kari,
                'kurum_iskonto' => $validated['KurumIskonto'] ?? $existingProduct->kurum_iskonto,
                'ticari_iskonto' => $validated['TicariIskonto'] ?? $existingProduct->ticari_iskonto,
                'depocu_fiyati' => $validated['DepocuFiyati'] ?? $existingProduct->depocu_fiyati,
                'mf1' => $validated['Mf1'] ?? $existingProduct->mf1,
                'net_fiyat1' => $validated['NetFiyat1'] ?? $existingProduct->net_fiyat1,
                'mf2' => $validated['Mf2'] ?? $existingProduct->mf2,
                'net_fiyat2' => $validated['NetFiyat2'] ?? $existingProduct->net_fiyat2,
            ]);

            Log::info('ERP Product Updated (via store)', ['urun_kodu' => $validated['UrunKodu']]);
            return response()->json(['message' => 'Ürün güncellendi', 'urun_kodu' => $validated['UrunKodu']]);
        }

        // Yeni ürün oluştur
        $product = Product::create([
            'urun_kodu' => $validated['UrunKodu'],
            'urun_adi' => $validated['UrunAdi'],
            'barkod' => $validated['Barkod'] ?? null,
            'grup' => $validated['Grup'] ?? null,
            'marka' => $validated['Marka'] ?? null,
            'kdv_orani' => $validated['KdvOrani'] ?? 0,
            'muadil_kodu' => $validated['MuadilKodu'] ?? null,
            'etken_madde' => $validated['EtkenMadde'] ?? null,
            'satis_fiyati' => $validated['SatisFiyati'] ?? 0,
            'eczaci_kari' => $validated['EczaciKari'] ?? 0,
            'kurum_iskonto' => $validated['KurumIskonto'] ?? 0,
            'ticari_iskonto' => $validated['TicariIskonto'] ?? 0,
            'depocu_fiyati' => $validated['DepocuFiyati'] ?? 0,
            'mf1' => $validated['Mf1'] ?? null,
            'net_fiyat1' => $validated['NetFiyat1'] ?? null,
            'mf2' => $validated['Mf2'] ?? null,
            'net_fiyat2' => $validated['NetFiyat2'] ?? null,
            'is_active' => true,
        ]);

        Log::info('ERP Product Created', ['urun_kodu' => $product->urun_kodu]);
        return response()->json(['message' => 'Ürün oluşturuldu', 'id' => $product->id], 201);
    }

    /**
     * Ürün güncelle (Update)
     */
    public function update(Request $request, string $urunKodu)
    {
        if (!$this->validateApiKey($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('ERP Product Update', ['urun_kodu' => $urunKodu, 'data' => $request->all()]);

        $validated = $request->validate([
            'UrunKodu' => 'nullable|string|max:255',
            'UrunAdi' => 'required|string|max:255',
            'Barkod' => 'nullable|string|max:255',
            'Grup' => 'nullable|string|max:255',
            'Marka' => 'nullable|string|max:255',
            'KdvOrani' => 'nullable|numeric',
            'MuadilKodu' => 'nullable|string|max:255',
            'EtkenMadde' => 'nullable|string|max:255',
            'SatisFiyati' => 'nullable|numeric',
            'EczaciKari' => 'nullable|numeric',
            'KurumIskonto' => 'nullable|numeric',
            'TicariIskonto' => 'nullable|numeric',
            'DepocuFiyati' => 'nullable|numeric',
            'Mf1' => 'nullable|string|max:255',
            'NetFiyat1' => 'nullable|numeric',
            'Mf2' => 'nullable|string|max:255',
            'NetFiyat2' => 'nullable|numeric',
        ]);

        $product = Product::where('urun_kodu', $urunKodu)->first();

        if (!$product) {
            Log::warning('ERP Product not found for update', ['urun_kodu' => $urunKodu]);
            return response()->json(['error' => 'Ürün bulunamadı'], 404);
        }

        $product->update([
            'urun_adi' => $validated['UrunAdi'],
            'barkod' => $validated['Barkod'] ?? $product->barkod,
            'grup' => $validated['Grup'] ?? $product->grup,
            'marka' => $validated['Marka'] ?? $product->marka,
            'kdv_orani' => $validated['KdvOrani'] ?? $product->kdv_orani,
            'muadil_kodu' => $validated['MuadilKodu'] ?? $product->muadil_kodu,
            'etken_madde' => $validated['EtkenMadde'] ?? $product->etken_madde,
            'satis_fiyati' => $validated['SatisFiyati'] ?? $product->satis_fiyati,
            'eczaci_kari' => $validated['EczaciKari'] ?? $product->eczaci_kari,
            'kurum_iskonto' => $validated['KurumIskonto'] ?? $product->kurum_iskonto,
            'ticari_iskonto' => $validated['TicariIskonto'] ?? $product->ticari_iskonto,
            'depocu_fiyati' => $validated['DepocuFiyati'] ?? $product->depocu_fiyati,
            'mf1' => $validated['Mf1'] ?? $product->mf1,
            'net_fiyat1' => $validated['NetFiyat1'] ?? $product->net_fiyat1,
            'mf2' => $validated['Mf2'] ?? $product->mf2,
            'net_fiyat2' => $validated['NetFiyat2'] ?? $product->net_fiyat2,
        ]);

        Log::info('ERP Product Updated', ['urun_kodu' => $urunKodu]);
        return response()->json(['message' => 'Ürün güncellendi', 'urun_kodu' => $urunKodu]);
    }

    /**
     * Ürün resmi yükle (sadece mevcut ürünler için)
     */
    public function uploadImage(Request $request, string $urunKodu)
    {
        if (!$this->validateApiKey($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('ERP Product Image Upload', ['urun_kodu' => $urunKodu]);

        // Ürün var mı kontrol et
        $product = Product::where('urun_kodu', $urunKodu)->first();

        if (!$product) {
            Log::warning('ERP Product not found for image upload', ['urun_kodu' => $urunKodu]);
            return response()->json(['error' => 'Ürün bulunamadı'], 404);
        }

        // Resim dosyası kontrolü
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'Resim dosyası bulunamadı'], 400);
        }

        $image = $request->file('image');

        // Dosya uzantısını belirle
        $extension = $image->getClientOriginalExtension() ?: 'jpg';

        // Dosya adını oluştur (stok kodu + timestamp)
        // Stok kodundaki özel karakterleri temizle
        $safeUrunKodu = preg_replace('/[^a-zA-Z0-9_-]/', '_', $urunKodu);
        $fileName = $safeUrunKodu . '_' . time() . '.' . $extension;

        // public/products klasörünü oluştur (yoksa)
        $uploadPath = public_path('storage/products');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Eski resmi sil (varsa)
        if ($product->urun_resmi) {
            $oldPath = public_path('storage/' . $product->urun_resmi);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Resmi public/storage/products klasörüne kaydet
        $image->move($uploadPath, $fileName);
        $path = 'products/' . $fileName;

        // Ürün kaydını güncelle
        $product->update(['urun_resmi' => $path]);

        Log::info('ERP Product Image Updated', [
            'urun_kodu' => $urunKodu,
            'path' => $path
        ]);

        return response()->json([
            'message' => 'Resim güncellendi',
            'urun_kodu' => $urunKodu,
            'path' => $path
        ]);
    }

    /**
     * Ürün bakiyesi güncelle (sadece mevcut ürünler için)
     */
    public function updateBakiye(Request $request, string $urunKodu)
    {
        if (!$this->validateApiKey($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'Bakiye' => 'required|numeric',
        ]);

        // Ürün var mı kontrol et
        $product = Product::where('urun_kodu', $urunKodu)->first();

        if (!$product) {
            return response()->json(['error' => 'Ürün bulunamadı'], 404);
        }

        // Bakiyeyi güncelle
        $product->update(['bakiye' => $validated['Bakiye']]);

        Log::info('ERP Product Bakiye Updated', [
            'urun_kodu' => $urunKodu,
            'bakiye' => $validated['Bakiye']
        ]);

        return response()->json([
            'message' => 'Bakiye güncellendi',
            'urun_kodu' => $urunKodu,
            'bakiye' => $validated['Bakiye']
        ]);
    }
}
