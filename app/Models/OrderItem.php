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
        'quantity',
        'price',
        'vat_rate',
        'total',
        'net_fiyat',
        'mal_fazlasi',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'total' => 'decimal:2',
        'net_fiyat' => 'decimal:2',
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
