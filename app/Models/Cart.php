<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Fiyatlar artık dinamik hesaplanıyor
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the user that owns the cart item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product for the cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Miktara göre en uygun bonus opsiyonunu belirle
     */
    public function getBonusOptionAttribute(): int
    {
        $bonusOptions = $this->product->bonus_options ?? [];
        $quantity = $this->quantity;

        $option2 = collect($bonusOptions)->firstWhere('option', 2);
        $option1 = collect($bonusOptions)->firstWhere('option', 1);

        // Opsiyon 2'nin minimum miktarına ulaştı mı?
        if ($option2 && $option2['buy'] > 0 && $quantity >= $option2['buy']) {
            return 2;
        }

        return 1;
    }

    /**
     * Miktara göre mal fazlasını hesapla
     */
    public function getMalFazlasiAttribute(): int
    {
        $bonusOptions = $this->product->bonus_options ?? [];
        $quantity = $this->quantity;
        $bonusOption = $this->bonus_option;

        $option = collect($bonusOptions)->firstWhere('option', $bonusOption);

        if ($option && $option['buy'] > 0 && $quantity >= $option['buy']) {
            return floor($quantity / $option['buy']) * $option['get'];
        }

        return 0;
    }

    /**
     * Güncel net fiyatı product'tan al
     */
    public function getNetPriceAttribute(): float
    {
        $bonusOption = $this->bonus_option;

        if ($bonusOption == 2 && $this->product->net_fiyat2) {
            return $this->product->net_fiyat2;
        } elseif ($this->product->net_fiyat1) {
            return $this->product->net_fiyat1;
        }

        return $this->product->net_price ?? $this->product->satis_fiyati ?? 0;
    }

    /**
     * Liste fiyatını product'tan al
     */
    public function getPriceAttribute(): float
    {
        return $this->product->satis_fiyati ?? 0;
    }

    /**
     * Birim maliyet hesapla (mal fazlası dahil)
     */
    public function getBirimMaliyetAttribute(): float
    {
        $toplamAdet = $this->quantity + $this->mal_fazlasi;
        $netPrice = $this->net_price;

        if ($toplamAdet > 0) {
            return ($netPrice * $this->quantity) / $toplamAdet;
        }

        return $netPrice;
    }

    /**
     * Get the total price for this cart item (KDV Dahil).
     */
    public function getTotalAttribute(): float
    {
        return $this->quantity * $this->net_price;
    }

    /**
     * Get the total without VAT.
     */
    public function getTotalWithoutVatAttribute(): float
    {
        $vatRate = $this->product->kdv_orani ?? 0;
        return $this->total / (1 + ($vatRate / 100));
    }

    /**
     * Get the VAT amount.
     */
    public function getVatAmountAttribute(): float
    {
        return $this->total - $this->total_without_vat;
    }
}
