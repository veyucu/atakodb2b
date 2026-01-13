<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_code',
        'quantity',
        'price',
        'vat_rate',
        'total',
        'net_price',
        'mal_fazlasi',
        'birim_maliyet',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'total' => 'decimal:2',
        'net_price' => 'decimal:2',
        'mal_fazlasi' => 'integer',
        'birim_maliyet' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

