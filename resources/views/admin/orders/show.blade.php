@extends('layouts.app')

@section('title', 'Sipariş Detayı - ' . $order->order_number . ' - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-invoice"></i> Sipariş Detayı: {{ $order->order_number }}</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Siparişlere Dön
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Order Info -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Sipariş Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Sipariş No:</strong> {{ $order->order_number }}</p>
                            <p><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
                            <p><strong>Durum:</strong> {!! $order->status_badge !!}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Müşteri:</strong> {{ $order->user->name ?? '-' }}</p>
                            <p><strong>Email:</strong> {{ $order->user->email }}</p>
                        </div>
                    </div>

                    @if($order->notes)
                        <hr>
                        <p><strong>Notlar:</strong></p>
                        <p class="text-muted">{{ $order->notes }}</p>
                    @endif

                    @if($order->shipping_address)
                        <hr>
                        <p><strong>Teslimat Adresi:</strong></p>
                        <p class="text-muted">{{ $order->shipping_address }}</p>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-box"></i> Sipariş Ürünleri</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ürün Kodu</th>
                                    <th>Ürün Adı</th>
                                    <th>PSF</th>
                                    <th>MF</th>
                                    <th>Miktar</th>
                                    <th>KDV</th>
                                    <th class="text-end">Toplam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product_code }}</td>
                                        <td>
                                            {{ $item->product_name }}
                                            @if($item->campaign_name)
                                                <br><span class="badge bg-success"><i class="fas fa-gift"></i> {{ $item->campaign_name }}</span>
                                            @endif
                                            @if($item->mal_fazlasi > 0)
                                                <br><span class="badge bg-info">+{{ $item->mal_fazlasi }} Bedava</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ number_format($item->price, 2, ',', '.') }} ₺
                                            @if($item->net_price && $item->net_price != $item->price)
                                                <br><small class="text-success">Net: {{ number_format($item->net_price, 2, ',', '.') }} ₺</small>
                                            @endif
                                            @if($item->birim_maliyet && $item->mal_fazlasi > 0)
                                                <br><small class="text-primary"><strong>Birim: {{ number_format($item->birim_maliyet, 2, ',', '.') }} ₺</strong></small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->mal_fazlasi > 0)
                                                <span class="badge bg-success">{{ $item->quantity }}+{{ $item->mal_fazlasi }}</span>
                                            @elseif($item->product && $item->product->mf)
                                                <span class="badge bg-info">{{ $item->product->mf }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>%{{ number_format($item->vat_rate, 0) }}</td>
                                        <td class="text-end"><strong>{{ number_format($item->total, 2, ',', '.') }} ₺</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Ara Toplam:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($order->subtotal, 2, ',', '.') }} ₺</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>KDV:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($order->vat, 2, ',', '.') }} ₺</strong></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="6" class="text-end"><strong>GENEL TOPLAM:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($order->total, 2, ',', '.') }} ₺</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-tasks"></i> Durum Güncelle</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="status" class="form-label">Sipariş Durumu</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Beklemede</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Onaylandı</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Hazırlanıyor</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Kargoda</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>İptal</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Durumu Güncelle
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-print"></i> İşlemler</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-info w-100 mb-2" onclick="window.print()">
                        <i class="fas fa-print"></i> Yazdır
                    </button>
                    <form action="{{ route('admin.orders.destroy', $order) }}" 
                          method="POST"
                          onsubmit="return confirm('Bu siparişi silmek istediğinize emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Siparişi Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

