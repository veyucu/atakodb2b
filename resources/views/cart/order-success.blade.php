@extends('layouts.app')

@section('title', 'Sipariş Başarılı - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-success">
                    <div class="card-header bg-success text-white text-center">
                        <h3 class="mb-0">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block"></i>
                            Siparişiniz Başarıyla Oluşturuldu!
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h5><i class="fas fa-info-circle"></i> Sipariş Bilgileri</h5>
                            <hr>
                            <p class="mb-1"><strong>Sipariş Numarası:</strong> <span
                                    class="badge bg-primary fs-5">{{ $order->order_number }}</span></p>
                            <p class="mb-1"><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}
                            </p>
                            <p class="mb-1"><strong>Durum:</strong> {!! $order->status_badge !!}</p>
                            @if($order->gonderim_sekli)
                                <p class="mb-1"><strong>Gönderim Şekli:</strong> <span
                                        class="badge bg-info">{{ $order->gonderim_sekli }}</span></p>
                            @endif
                            @if($order->notes)
                                <p class="mb-0"><strong>Sipariş Notu:</strong> {{ $order->notes }}</p>
                            @endif
                        </div>

                        <h5 class="mt-4 mb-3"><i class="fas fa-box"></i> Sipariş Özeti</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ürün</th>
                                        <th>MF</th>
                                        <th>Net Fiyat</th>
                                        <th>Miktar</th>
                                        <th class="text-end">Toplam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <small class="text-muted">{{ $item->product->urun_kodu ?? '-' }}</small><br>
                                                <strong>{{ $item->product->urun_adi ?? 'Ürün bulunamadı' }}</strong>
                                            </td>
                                            <td>
                                                @if($item->mal_fazlasi)
                                                    <span class="badge bg-success">{{ $item->mal_fazlasi }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($item->net_fiyat, 2, ',', '.') }} ₺</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td class="text-end">{{ number_format($item->total, 2, ',', '.') }} ₺</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Ara Toplam:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($order->subtotal, 2, ',', '.')}}
                                                ₺</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>KDV:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($order->vat, 2, ',', '.') }}
                                                ₺</strong></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td colspan="4" class="text-end"><strong>Genel Toplam:</strong></td>
                                        <td class="text-end"><strong
                                                class="fs-5">{{ number_format($order->total, 2, ',', '.') }} ₺</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle"></i> Siparişiniz başarıyla alındı. En kısa sürede işleme
                            alınacaktır.
                            Sipariş durumunuzu takip edebilirsiniz.
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-bag"></i> Alışverişe Devam Et
                            </a>
                            <button onclick="window.print()" class="btn btn-outline-secondary">
                                <i class="fas fa-print"></i> Siparişi Yazdır
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {

            .navbar,
            .btn,
            footer {
                display: none !important;
            }
        }
    </style>
@endsection