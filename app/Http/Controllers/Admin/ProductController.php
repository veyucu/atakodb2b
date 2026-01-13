<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Arama (seçilen tip ve değere göre)
        if ($request->filled('search')) {
            $search = $request->search;
            $searchType = $request->get('search_type', 'all');
            
            if ($searchType === 'all') {
                // Muadil ürünlerle birlikte ara
                $query->searchWithEquivalents($search);
            } else {
                // Spesifik alana göre arama
                $query->whereRaw("{$searchType} COLLATE utf8mb4_turkish_ci LIKE ?", ["%{$search}%"]);
            }
        }

        // Marka filtresi
        if ($request->filled('marka')) {
            $query->where('marka', $request->marka);
        }

        // Grup filtresi
        if ($request->filled('grup')) {
            $query->where('grup', $request->grup);
        }

        // Aktif/Pasif filtresi
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Özel Liste filtresi
        if ($request->filled('ozel_liste')) {
            $query->where('ozel_liste', $request->ozel_liste);
        }

        // Sıralama (field_direction formatında)
        $sort = $request->get('sort', 'created_at_desc');
        
        // Sort parametresini parse et
        $sortParts = explode('_', $sort);
        if (count($sortParts) >= 2) {
            $sortDirection = array_pop($sortParts); // Son eleman direction (asc/desc)
            $sortField = implode('_', $sortParts); // Geri kalanlar field adı
            
            // Geçerli alanlar ve yönler
            $validFields = ['urun_kodu', 'urun_adi', 'created_at', 'updated_at'];
            $validDirections = ['asc', 'desc'];
            
            if (in_array($sortField, $validFields) && in_array($sortDirection, $validDirections)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20)->appends($request->all());
        
        // Marka ve grup listelerini getir
        $markalar = Product::whereNotNull('marka')->distinct()->pluck('marka');
        $gruplar = Product::whereNotNull('grup')->distinct()->pluck('grup');

        return view('admin.products.index', compact('products', 'markalar', 'gruplar'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'urun_kodu' => 'required|string|max:255|unique:products',
            'urun_adi' => 'required|string|max:255',
            'barkod' => 'nullable|string|max:255',
            'muadil_kodu' => 'nullable|string|max:255',
            'satis_fiyati' => 'required|numeric|min:0',
            'kdv_orani' => 'required|numeric|min:0|max:100',
            'kurum_iskonto' => 'nullable|numeric|min:0|max:100',
            'eczaci_kari' => 'nullable|numeric|min:0|max:100',
            'ticari_iskonto' => 'nullable|numeric|min:0|max:100',
            'mf' => 'nullable|string|max:255',
            'depocu_fiyati' => 'nullable|numeric|min:0',
            'net_fiyat_manuel' => 'nullable|numeric|min:0',
            'bakiye' => 'nullable|numeric|min:0',
            'marka' => 'nullable|string|max:255',
            'grup' => 'nullable|string|max:255',
            'kod1' => 'nullable|string|max:255',
            'kod2' => 'nullable|string|max:255',
            'kod3' => 'nullable|string|max:255',
            'kod4' => 'nullable|string|max:255',
            'kod5' => 'nullable|string|max:255',
            'urun_resmi' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'is_active' => 'boolean',
            'ozel_liste' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['ozel_liste'] = $request->has('ozel_liste');

        if ($request->hasFile('urun_resmi')) {
            $data['urun_resmi'] = $request->file('urun_resmi')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    /**
     * Show the form for editing the product.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the product.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'urun_kodu' => 'required|string|max:255|unique:products,urun_kodu,' . $product->id,
            'urun_adi' => 'required|string|max:255',
            'barkod' => 'nullable|string|max:255',
            'muadil_kodu' => 'nullable|string|max:255',
            'satis_fiyati' => 'required|numeric|min:0',
            'kdv_orani' => 'required|numeric|min:0|max:100',
            'kurum_iskonto' => 'nullable|numeric|min:0|max:100',
            'eczaci_kari' => 'nullable|numeric|min:0|max:100',
            'ticari_iskonto' => 'nullable|numeric|min:0|max:100',
            'mf' => 'nullable|string|max:255',
            'depocu_fiyati' => 'nullable|numeric|min:0',
            'net_fiyat_manuel' => 'nullable|numeric|min:0',
            'bakiye' => 'nullable|numeric|min:0',
            'marka' => 'nullable|string|max:255',
            'grup' => 'nullable|string|max:255',
            'kod1' => 'nullable|string|max:255',
            'kod2' => 'nullable|string|max:255',
            'kod3' => 'nullable|string|max:255',
            'kod4' => 'nullable|string|max:255',
            'kod5' => 'nullable|string|max:255',
            'urun_resmi' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'is_active' => 'boolean',
            'ozel_liste' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['ozel_liste'] = $request->has('ozel_liste');

        if ($request->hasFile('urun_resmi')) {
            // Delete old image
            if ($product->urun_resmi) {
                Storage::disk('public')->delete($product->urun_resmi);
            }
            $data['urun_resmi'] = $request->file('urun_resmi')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Ürün başarıyla güncellendi.');
    }

    /**
     * Remove the product.
     */
    public function destroy(Product $product)
    {
        if ($product->urun_resmi) {
            Storage::disk('public')->delete($product->urun_resmi);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Ürün başarıyla silindi.');
    }

    /**
     * Quick update product from list view (AJAX).
     */
    public function quickUpdate(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'urun_adi' => 'required|string|max:255',
                'muadil_kodu' => 'nullable|string|max:255',
                'satis_fiyati' => 'required|numeric|min:0',
                'kdv_orani' => 'required|numeric|min:0|max:100',
                'kurum_iskonto' => 'nullable|numeric|min:0|max:100',
                'eczaci_kari' => 'nullable|numeric|min:0|max:100',
                'ticari_iskonto' => 'nullable|numeric|min:0|max:100',
                'mf' => 'nullable|string|max:255',
                'depocu_fiyati' => 'nullable|numeric|min:0',
                'net_fiyat_manuel' => 'nullable|numeric|min:0',
                'bakiye' => 'nullable|numeric|min:0',
                'marka' => 'nullable|string|max:255',
                'grup' => 'nullable|string|max:255',
                'is_active' => 'nullable|boolean',
                'ozel_liste' => 'nullable|boolean',
            ]);

            // Boolean değerleri düzelt
            $validated['is_active'] = $request->has('is_active') && $request->input('is_active') == 1;
            $validated['ozel_liste'] = $request->has('ozel_liste') && $request->input('ozel_liste') == 1;

            // Boş değerleri null yap
            foreach (['kurum_iskonto', 'eczaci_kari', 'ticari_iskonto', 'depocu_fiyati', 'net_fiyat_manuel', 'bakiye'] as $field) {
                if (isset($validated[$field]) && $validated[$field] === '') {
                    $validated[$field] = null;
                }
            }

            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla güncellendi.',
                'product' => $product->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası: ' . implode(', ', array_map(function($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete product image only.
     */
    public function deleteImage(Product $product)
    {
        if ($product->urun_resmi) {
            // Sadece local storage'daki dosyaları sil, URL'leri silme
            if (!filter_var($product->urun_resmi, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->urun_resmi);
            }
            
            $product->update(['urun_resmi' => null]);
            
            return redirect()->route('admin.products.edit', $product)
                ->with('success', 'Ürün resmi başarıyla silindi.');
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('error', 'Silinecek resim bulunamadı.');
    }
}


