<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'urun_kodu',
        'urun_adi',
        'barkod',
        'muadil_kodu',
        'satis_fiyati',
        'kdv_orani',
        'kurum_iskonto',
        'eczaci_kari',
        'ticari_iskonto',
        'mf',
        'depocu_fiyati',
        'net_fiyat_manuel',
        'bakiye',
        'marka',
        'grup',
        'kod1',
        'kod2',
        'kod3',
        'kod4',
        'kod5',
        'urun_resmi',
        'is_active',
        'ozel_liste',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'satis_fiyati' => 'decimal:2',
        'kdv_orani' => 'decimal:2',
        'kurum_iskonto' => 'decimal:2',
        'eczaci_kari' => 'decimal:2',
        'ticari_iskonto' => 'decimal:2',
        'depocu_fiyati' => 'decimal:2',
        'net_fiyat_manuel' => 'decimal:2',
        'bakiye' => 'decimal:2',
        'is_active' => 'boolean',
        'ozel_liste' => 'boolean',
    ];

    /**
     * Get the product image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->urun_resmi) {
            // Eğer URL ise direkt döndür
            if (filter_var($this->urun_resmi, FILTER_VALIDATE_URL)) {
                return $this->urun_resmi;
            }
            // Değilse storage'dan al
            return asset('storage/' . $this->urun_resmi);
        }
        return null;
    }

    /**
     * Check if product has image.
     */
    public function hasImage(): bool
    {
        return !empty($this->urun_resmi);
    }

    /**
     * Get cart items for this product.
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Calculate net price after all discounts
     * Liste Fiyatı - Kurum İskontosu - Eczacı Karı - Ticari İskonto
     */
    public function getNetPriceAttribute(): float
    {
        $price = $this->satis_fiyati;
        
        // Kurum iskontosu düş
        if ($this->kurum_iskonto > 0) {
            $price = $price - ($price * $this->kurum_iskonto / 100);
        }
        
        // Eczacı karı düş
        if ($this->eczaci_kari > 0) {
            $price = $price - ($price * $this->eczaci_kari / 100);
        }
        
        // Ticari iskonto düş
        if ($this->ticari_iskonto > 0) {
            $price = $price - ($price * $this->ticari_iskonto / 100);
        }
        
        return round($price, 2);
    }

    /**
     * Get total discount percentage
     */
    public function getTotalDiscountAttribute(): float
    {
        return $this->kurum_iskonto + $this->eczaci_kari + $this->ticari_iskonto;
    }

    /**
     * Scope to search including equivalent products
     * Muadil ürünleri de aramaya dahil eder
     */
    public function scopeSearchWithEquivalents($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            // Normal arama - kendi alanlarında ara
            $q->whereRaw('urun_adi COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('urun_kodu COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('barkod COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('marka COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('muadil_kodu COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$searchTerm}%"])
              // Muadil ürünleri de dahil et: arama terimiyle eşleşen ürünlerin muadil kodlarını al
              // ve bu kodlara sahip diğer ürünleri de getir
              ->orWhereIn('muadil_kodu', function($subQuery) use ($searchTerm) {
                  $subQuery->select('muadil_kodu')
                      ->from('products')
                      ->whereNotNull('muadil_kodu')
                      ->where('muadil_kodu', '!=', '')
                      ->where(function($sq) use ($searchTerm) {
                          $sq->whereRaw('urun_adi COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$searchTerm}%"])
                             ->orWhereRaw('urun_kodu COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$searchTerm}%"])
                             ->orWhereRaw('barkod COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$searchTerm}%"])
                             ->orWhereRaw('marka COLLATE utf8mb4_turkish_ci LIKE ?', ["%{$searchTerm}%"]);
                      });
              });
        });
    }
}



