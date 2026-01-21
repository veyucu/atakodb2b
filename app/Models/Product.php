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
        'etken_madde',
        'satis_fiyati',
        'kdv_orani',
        'kurum_iskonto',
        'eczaci_kari',
        'ticari_iskonto',
        'mf1',
        'mf2',
        'depocu_fiyati',
        'net_fiyat1',
        'net_fiyat2',
        'bakiye',
        'marka',
        'grup',
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
        'net_fiyat1' => 'decimal:2',
        'net_fiyat2' => 'decimal:2',
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
     * Get bonus options as array
     * Returns parsed bonus options with buy/get quantities and prices
     * Example: mf1="10+1", net_fiyat1=100 => [['label'=>'10+1', 'buy'=>10, 'get'=>1, 'price'=>100]]
     */
    public function getBonusOptionsAttribute(): array
    {
        $options = [];

        // Option 1
        if (!empty($this->mf1)) {
            $parsed = $this->parseBonusString($this->mf1);
            if ($parsed) {
                $options[] = [
                    'label' => $this->mf1,
                    'buy' => $parsed['buy'],
                    'get' => $parsed['get'],
                    'price' => $this->net_fiyat1,
                    'option' => 1,
                ];
            }
        }

        // Option 2
        if (!empty($this->mf2)) {
            $parsed = $this->parseBonusString($this->mf2);
            if ($parsed) {
                $options[] = [
                    'label' => $this->mf2,
                    'buy' => $parsed['buy'],
                    'get' => $parsed['get'],
                    'price' => $this->net_fiyat2,
                    'option' => 2,
                ];
            }
        }

        return $options;
    }

    /**
     * Parse bonus string like "10+1" into buy and get values
     */
    protected function parseBonusString(?string $str): ?array
    {
        if (empty($str))
            return null;

        // Match pattern like "10+1", "15+5", etc.
        if (preg_match('/(\d+)\s*\+\s*(\d+)/', $str, $matches)) {
            return [
                'buy' => (int) $matches[1],
                'get' => (int) $matches[2],
            ];
        }

        return null;
    }

    /**
     * Normalize Turkish characters to ASCII equivalents
     */
    protected static function normalizeTurkish(string $text): string
    {
        $turkishChars = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'];
        $asciiChars = ['c', 'g', 'i', 'o', 's', 'u', 'C', 'G', 'I', 'O', 'S', 'U'];
        return str_replace($turkishChars, $asciiChars, $text);
    }

    /**
     * Convert ASCII to Turkish equivalent for reverse search
     */
    protected static function denormalizeTurkish(string $text): string
    {
        // This creates a pattern where each ASCII char can match its Turkish equivalent
        $asciiChars = ['c', 'g', 'i', 'o', 's', 'u', 'C', 'G', 'I', 'O', 'S', 'U'];
        $turkishChars = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'];
        return str_replace($asciiChars, $turkishChars, $text);
    }

    /**
     * Scope to search including equivalent products
     * Muadil ürünleri de aramaya dahil eder
     * Türkçe karakter normalizasyonu ile hem AĞAÇ hem AGAC bulunur
     */
    public function scopeSearchWithEquivalents($query, $searchTerm)
    {
        // Null veya boş arama terimi için sorguyu olduğu gibi döndür
        if (empty($searchTerm)) {
            return $query;
        }

        // Normalize search term to ASCII
        $normalizedTerm = self::normalizeTurkish($searchTerm);
        // Also get Turkish version if user typed ASCII
        $turkishTerm = self::denormalizeTurkish($searchTerm);

        // Make terms uppercase for case-insensitive matching
        $searchTermUpper = mb_strtoupper($searchTerm, 'UTF-8');
        $normalizedTermUpper = mb_strtoupper($normalizedTerm, 'UTF-8');
        $turkishTermUpper = mb_strtoupper($turkishTerm, 'UTF-8');

        return $query->where(function ($q) use ($searchTerm, $normalizedTerm, $turkishTerm, $searchTermUpper, $normalizedTermUpper, $turkishTermUpper) {
            // Search with original term (case insensitive)
            $q->whereRaw('UPPER(urun_adi) LIKE ?', ["%{$searchTermUpper}%"])
                ->orWhereRaw('UPPER(urun_kodu) LIKE ?', ["%{$searchTermUpper}%"])
                ->orWhereRaw('UPPER(barkod) LIKE ?', ["%{$searchTermUpper}%"])
                ->orWhereRaw('UPPER(marka) LIKE ?', ["%{$searchTermUpper}%"])
                ->orWhereRaw('UPPER(muadil_kodu) LIKE ?', ["%{$searchTermUpper}%"]);

            // Also search with normalized ASCII version
            if ($normalizedTermUpper !== $searchTermUpper) {
                $q->orWhereRaw('UPPER(urun_adi) LIKE ?', ["%{$normalizedTermUpper}%"])
                    ->orWhereRaw('UPPER(urun_kodu) LIKE ?', ["%{$normalizedTermUpper}%"])
                    ->orWhereRaw('UPPER(barkod) LIKE ?', ["%{$normalizedTermUpper}%"])
                    ->orWhereRaw('UPPER(marka) LIKE ?', ["%{$normalizedTermUpper}%"]);
            }

            // Also search with Turkish version (if user typed ASCII)
            if ($turkishTermUpper !== $searchTermUpper && $turkishTermUpper !== $normalizedTermUpper) {
                $q->orWhereRaw('UPPER(urun_adi) LIKE ?', ["%{$turkishTermUpper}%"])
                    ->orWhereRaw('UPPER(urun_kodu) LIKE ?', ["%{$turkishTermUpper}%"])
                    ->orWhereRaw('UPPER(barkod) LIKE ?', ["%{$turkishTermUpper}%"])
                    ->orWhereRaw('UPPER(marka) LIKE ?', ["%{$turkishTermUpper}%"]);
            }

            // Muadil ürünleri de dahil et
            $q->orWhereIn('muadil_kodu', function ($subQuery) use ($searchTermUpper, $normalizedTermUpper, $turkishTermUpper) {
                $subQuery->select('muadil_kodu')
                    ->from('products')
                    ->whereNotNull('muadil_kodu')
                    ->where('muadil_kodu', '!=', '')
                    ->where(function ($sq) use ($searchTermUpper, $normalizedTermUpper, $turkishTermUpper) {
                        $sq->whereRaw('UPPER(urun_adi) LIKE ?', ["%{$searchTermUpper}%"])
                            ->orWhereRaw('UPPER(urun_kodu) LIKE ?', ["%{$searchTermUpper}%"])
                            ->orWhereRaw('UPPER(barkod) LIKE ?', ["%{$searchTermUpper}%"])
                            ->orWhereRaw('UPPER(marka) LIKE ?', ["%{$searchTermUpper}%"]);

                        if ($normalizedTermUpper !== $searchTermUpper) {
                            $sq->orWhereRaw('UPPER(urun_adi) LIKE ?', ["%{$normalizedTermUpper}%"])
                                ->orWhereRaw('UPPER(urun_kodu) LIKE ?', ["%{$normalizedTermUpper}%"]);
                        }

                        if ($turkishTermUpper !== $searchTermUpper && $turkishTermUpper !== $normalizedTermUpper) {
                            $sq->orWhereRaw('UPPER(urun_adi) LIKE ?', ["%{$turkishTermUpper}%"])
                                ->orWhereRaw('UPPER(urun_kodu) LIKE ?', ["%{$turkishTermUpper}%"]);
                        }
                    });
            });
        });
    }
}



