<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'price',
        'net_price',
        'mal_fazlasi',
        'birim_maliyet',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'net_price' => 'decimal:2',
        'mal_fazlasi' => 'integer',
        'birim_maliyet' => 'decimal:2',
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
     * Get the total price for this cart item (KDV Dahil).
     */
    public function getTotalAttribute(): float
    {
        // Önce ürün kartındaki net fiyatı kontrol et (zaten KDV dahil)
        $netFiyat = $this->product->net_fiyat_manuel ?? $this->product->net_price ?? null;
        
        if ($netFiyat) {
            return $this->quantity * $netFiyat;
        }
        
        // Eğer cart'taki net fiyat varsa onu kullan
        if ($this->net_price) {
            return $this->quantity * $this->net_price;
        }
        
        // Yoksa normal fiyat
        return $this->quantity * $this->price;
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



