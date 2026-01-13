<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'vat',
        'total',
        'notes',
        'shipping_address',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'vat' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber()
    {
        $date = date('Ymd');
        $lastOrder = self::whereDate('created_at', today())->latest()->first();
        $number = $lastOrder ? (int)substr($lastOrder->order_number, -4) + 1 : 1;
        return $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Beklemede</span>',
            'confirmed' => '<span class="badge bg-info">Onaylandı</span>',
            'processing' => '<span class="badge bg-primary">Hazırlanıyor</span>',
            'shipped' => '<span class="badge bg-secondary">Kargoda</span>',
            'delivered' => '<span class="badge bg-success">Teslim Edildi</span>',
            'cancelled' => '<span class="badge bg-danger">İptal</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Bilinmiyor</span>';
    }
}






