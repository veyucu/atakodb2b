@extends('layouts.app')

@section('title', 'Sipariş Detayı')

@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Sipariş Detayı: {{ $order->order_number }}
                        </h5>
                        <a href="{{ route('orders.history') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Geri
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Sipariş Bilgileri -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Sipariş Bilgileri</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width: 150px;">Sipariş No:</td>
                                    <td><strong>{{ $order->order_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Sipariş Tarihi:</td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Müşteri:</td>
                                    <td><strong>{{ $order->user->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kullanıcı Adı:</td>
                                    <td><span class="badge bg-secondary">{{ $order->user->username }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Durum:</td>
                                    <td>
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Beklemede</span>
                                                @break
                                            @case('approved')
                                            @case('confirmed')
                                                <span class="badge bg-success">Onaylandı</span>
                                                @break
                                            @case('processing')
                                                <span class="badge bg-info">Hazırlanıyor</span>
                                                @break
                                            @case('shipped')
                                                <span class="badge bg-primary">Kargoda</span>
                                                @break
                                            @case('delivered')
                                                <span class="badge bg-success">Teslim Edildi</span>
                                                @break
                                            @case('rejected')
                                            @case('cancelled')
                                                <span class="badge bg-danger">İptal</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $order->status }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Teslimat Bilgileri</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width: 150px;">Adres:</td>
                                    <td>{{ $order->user->adres ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">İlçe / İl:</td>
                                    <td>{{ $order->user->ilce ?? '-' }} / {{ $order->user->il ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Telefon:</td>
                                    <td>{{ $order->user->telefon ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Sipariş Kalemleri -->
                    <h6 class="fw-bold mb-3">Sipariş Kalemleri</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Ürün Kodu</th>
                                    <th>Ürün Adı</th>
                                    <th class="text-center">Miktar</th>
                                    <th class="text-end">Birim Fiyat</th>
                                    <th class="text-center">KDV %</th>
                                    <th class="text-end">Toplam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product_code }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td class="text-center"><strong>{{ $item->quantity }}</strong></td>
                                        <td class="text-end">{{ number_format($item->price, 2, ',', '.') }} ₺</td>
                                        <td class="text-center">{{ number_format($item->vat_rate, 0) }}%</td>
                                        <td class="text-end"><strong>{{ number_format($item->total, 2, ',', '.') }} ₺</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Ara Toplam (KDV Hariç):</th>
                                    <th class="text-end">{{ number_format($order->subtotal, 2, ',', '.') }} ₺</th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-end">KDV:</th>
                                    <th class="text-end">{{ number_format($order->vat, 2, ',', '.') }} ₺</th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-end">Genel Toplam:</th>
                                    <th class="text-end text-success fs-5">{{ number_format($order->total, 2, ',', '.') }} ₺</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($order->notes)
                        <div class="mt-3">
                            <h6 class="fw-bold">Not:</h6>
                            <p class="bg-light p-3 rounded">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

