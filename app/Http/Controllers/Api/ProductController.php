<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Ürün listesini getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filtreleme
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('ozel_liste')) {
            $query->where('ozel_liste', $request->boolean('ozel_liste'));
        }

        if ($request->has('marka')) {
            $query->where('marka', $request->marka);
        }

        if ($request->has('grup')) {
            $query->where('grup', $request->grup);
        }

        if ($request->has('muadil_kodu')) {
            $query->where('muadil_kodu', $request->muadil_kodu);
        }

        // Fiyat aralığı
        if ($request->has('min_price')) {
            $query->where('satis_fiyati', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('satis_fiyati', '<=', $request->max_price);
        }

        // Stok durumu
        if ($request->has('in_stock')) {
            if ($request->boolean('in_stock')) {
                $query->where('bakiye', '>', 0);
            } else {
                $query->where('bakiye', '<=', 0);
            }
        }

        // Arama
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('urun_adi', 'like', "%{$search}%")
                  ->orWhere('urun_kodu', 'like', "%{$search}%")
                  ->orWhere('barkod', 'like', "%{$search}%")
                  ->orWhere('marka', 'like', "%{$search}%");
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Sayfalama
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Yeni ürün oluştur
     * 
     * @param StoreProductRequest $request
     * @return ProductResource
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        
        // Varsayılan değerler
        $data['is_active'] = $data['is_active'] ?? true;
        $data['ozel_liste'] = $data['ozel_liste'] ?? false;
        $data['kdv_orani'] = $data['kdv_orani'] ?? 0;
        $data['kurum_iskonto'] = $data['kurum_iskonto'] ?? 0;
        $data['eczaci_kari'] = $data['eczaci_kari'] ?? 0;
        $data['ticari_iskonto'] = $data['ticari_iskonto'] ?? 0;
        $data['bakiye'] = $data['bakiye'] ?? 0;

        $product = Product::create($data);

        return new ProductResource($product);
    }

    /**
     * Belirli bir ürünü getir
     * 
     * @param Product $product
     * @return ProductResource
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Ürün bilgilerini güncelle
     * 
     * @param UpdateProductRequest $request
     * @param Product $product
     * @return ProductResource
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        
        $product->update($data);

        return new ProductResource($product->fresh());
    }

    /**
     * Ürünü sil
     * 
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Ürün başarıyla silindi'
        ], 200);
    }

    /**
     * Ürün koduna göre ürün getir
     * 
     * @param Request $request
     * @return ProductResource|\Illuminate\Http\JsonResponse
     */
    public function findByCode(Request $request)
    {
        $request->validate([
            'urun_kodu' => 'required|string'
        ]);

        $product = Product::where('urun_kodu', $request->urun_kodu)->first();

        if (!$product) {
            return response()->json([
                'message' => 'Ürün bulunamadı'
            ], 404);
        }

        return new ProductResource($product);
    }

    /**
     * Barkoda göre ürün getir
     * 
     * @param Request $request
     * @return ProductResource|\Illuminate\Http\JsonResponse
     */
    public function findByBarcode(Request $request)
    {
        $request->validate([
            'barkod' => 'required|string'
        ]);

        $product = Product::where('barkod', $request->barkod)->first();

        if (!$product) {
            return response()->json([
                'message' => 'Ürün bulunamadı'
            ], 404);
        }

        return new ProductResource($product);
    }

    /**
     * Muadil ürünleri getir
     * 
     * @param Product $product
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function equivalents(Product $product)
    {
        if (!$product->muadil_kodu) {
            return response()->json([
                'message' => 'Bu ürünün muadili bulunmamaktadır',
                'data' => []
            ]);
        }

        $equivalents = Product::where('muadil_kodu', $product->muadil_kodu)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->get();

        return ProductResource::collection($equivalents);
    }

    /**
     * Toplu ürün oluştur veya güncelle (Sync)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.urun_kodu' => 'required|string',
            'products.*.urun_adi' => 'required|string',
            'products.*.satis_fiyati' => 'required|numeric',
        ]);

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($request->products as $productData) {
            try {
                $product = Product::where('urun_kodu', $productData['urun_kodu'])->first();

                if ($product) {
                    // Güncelle
                    $product->update($productData);
                    $updated++;
                } else {
                    // Yeni oluştur
                    $createData = $productData;
                    $createData['is_active'] = $createData['is_active'] ?? true;
                    $createData['ozel_liste'] = $createData['ozel_liste'] ?? false;
                    $createData['kdv_orani'] = $createData['kdv_orani'] ?? 0;
                    $createData['kurum_iskonto'] = $createData['kurum_iskonto'] ?? 0;
                    $createData['eczaci_kari'] = $createData['eczaci_kari'] ?? 0;
                    $createData['ticari_iskonto'] = $createData['ticari_iskonto'] ?? 0;
                    $createData['bakiye'] = $createData['bakiye'] ?? 0;
                    Product::create($createData);
                    $created++;
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'urun_kodu' => $productData['urun_kodu'] ?? 'unknown',
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => 'Senkronizasyon tamamlandı',
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors
        ]);
    }

    /**
     * Stok güncelle
     * 
     * @param Request $request
     * @param Product $product
     * @return ProductResource
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'bakiye' => 'required|numeric'
        ]);

        $product->update([
            'bakiye' => $request->bakiye
        ]);

        return new ProductResource($product->fresh());
    }

    /**
     * Fiyat güncelle
     * 
     * @param Request $request
     * @param Product $product
     * @return ProductResource
     */
    public function updatePrice(Request $request, Product $product)
    {
        $request->validate([
            'satis_fiyati' => 'required|numeric|min:0',
            'kdv_orani' => 'nullable|numeric|min:0|max:100',
            'kurum_iskonto' => 'nullable|numeric|min:0|max:100',
            'eczaci_kari' => 'nullable|numeric|min:0|max:100',
            'ticari_iskonto' => 'nullable|numeric|min:0|max:100',
        ]);

        $product->update($request->only([
            'satis_fiyati',
            'kdv_orani',
            'kurum_iskonto',
            'eczaci_kari',
            'ticari_iskonto'
        ]));

        return new ProductResource($product->fresh());
    }
}
