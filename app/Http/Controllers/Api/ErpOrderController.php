<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ErpOrderController extends Controller
{
    /**
     * ERP'ye aktarılmamış siparişleri listele
     */
    public function pending(Request $request)
    {
        $orders = Order::with(['user', 'items.product'])
            ->where('erp_synced', false)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'cari_kodu' => $order->user->cari_kodu ?? null,
                    'tarih' => $order->created_at->format('Y-m-d'),
                    'gonderim_sekli' => $order->gonderim_sekli,
                    'notes' => $order->notes,
                    'subtotal' => $order->subtotal,
                    'vat' => $order->vat,
                    'total' => $order->total,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'urun_kodu' => $item->product->urun_kodu ?? null,
                            'urun_adi' => $item->product->urun_adi ?? null,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'net_fiyat' => $item->net_fiyat,
                            'vat_rate' => $item->vat_rate,
                            'total' => $item->total,
                            'mal_fazlasi' => $item->mal_fazlasi,
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $orders->count(),
            'orders' => $orders,
        ]);
    }

    /**
     * Siparişi ERP'ye senkronize edildi olarak işaretle
     */
    public function markSynced(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'erp_synced' => true,
            'erp_synced_at' => now(),
            'erp_order_number' => $request->erp_order_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş senkronize edildi olarak işaretlendi',
            'order_id' => $order->id,
            'erp_order_number' => $request->erp_order_number,
        ]);
    }

    /**
     * Senkronize edilmiş siparişleri listele
     */
    public function synced(Request $request)
    {
        $orders = Order::where('erp_synced', true)
            ->orderBy('erp_synced_at', 'desc')
            ->limit(50)
            ->get(['id', 'order_number', 'erp_order_number', 'erp_synced_at', 'total']);

        return response()->json([
            'success' => true,
            'count' => $orders->count(),
            'orders' => $orders,
        ]);
    }
}
