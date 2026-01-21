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
        'gonderim_sekli',
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
        $year = date('Y');

        // Bu yıla ait YENİ formatta (YYYY + 6 hane) olan son siparişi bul
        $lastOrder = self::where('order_number', 'LIKE', $year . '%')
            ->where('order_number', 'REGEXP', '^' . $year . '[0-9]{6}$')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder && strlen($lastOrder->order_number) == 10) {
            // Son sipariş numarasından sayacı çıkar (2026000001 -> 1)
            $number = (int) substr($lastOrder->order_number, 4) + 1;
        } else {
            // Yeni format yok, 1'den başla
            $number = 1;
        }

        // Format: 2026000001 (toplam 10 hane: 4 yıl + 6 sayaç)
        return $year . str_pad($number, 6, '0', STR_PAD_LEFT);
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






