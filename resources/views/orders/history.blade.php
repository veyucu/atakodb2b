@extends('layouts.app')

@section('title', 'Siparişlerim')

@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Siparişlerim
                        </h5>
                        @if((auth()->user()->isPlasiyer() || auth()->user()->isAdmin()) && session()->has('selected_customer_name'))
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-user me-1"></i>
                                {{ $customer->name }} ({{ $customer->username }})
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtreler -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Durum</label>
                            <select class="form-select" name="status">
                                <option value="">Tümü</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Onaylandı</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Onaylandı</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Hazırlanıyor</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Kargoda</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Reddedildi</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> Filtrele
                            </button>
                            <a href="{{ route('orders.history') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Sıfırla
                            </a>
                        </div>
                    </form>

                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Tarih</th>
                                        <th class="text-center">Kalem</th>
                                        <th class="text-center">Miktar</th>
                                        <th class="text-end">Ara Toplam</th>
                                        <th class="text-end">KDV</th>
                                        <th class="text-end">Toplam</th>
                                        <th>Durum</th>
                                        <th class="text-center">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td><strong>{{ $order->order_number }}</strong></td>
                                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $order->items->count() }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $order->items->sum('quantity') }}</span>
                                            </td>
                                            <td class="text-end">{{ number_format($order->subtotal, 2, ',', '.') }} ₺</td>
                                            <td class="text-end">{{ number_format($order->vat, 2, ',', '.') }} ₺</td>
                                            <td class="text-end">
                                                <strong class="text-success">{{ number_format($order->total, 2, ',', '.') }} ₺</strong>
                                            </td>
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
                                            <td class="text-center">
                                                <a href="{{ route('orders.detail', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            Henüz sipariş bulunmamaktadır.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

