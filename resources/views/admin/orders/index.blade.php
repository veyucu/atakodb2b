@extends('layouts.app')

@section('title', 'Sipariş Yönetimi - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shopping-cart"></i> Sipariş Yönetimi</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(isset($selectedCustomer) && $selectedCustomer)
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-user-tag fs-5 me-3"></i>
            <div>
                <strong>Seçili Müşteri:</strong> {{ $selectedCustomer->name }}
                <span class="badge bg-secondary ms-2">{{ $selectedCustomer->username }}</span>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light ms-auto">
                <i class="fas fa-times me-1"></i>Filtreyi Temizle
            </a>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                @if(request('customer_id'))
                    <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
                @endif
                <div class="col-md-4">
                    <label for="search" class="form-label">Ara</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Sipariş No veya Müşteri Adı">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Durum</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tümü</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Onaylandı</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Hazırlanıyor</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Kargoda</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrele
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Temizle
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sipariş No</th>
                                <th>Müşteri</th>
                                <th>Sipariş Veren</th>
                                <th>Ürün Sayısı</th>
                                <th>Toplam</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $order->user->name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $order->user->name }}
                                        <br>
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $order->items->count() }} Ürün</span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($order->total, 2, ',', '.') }} ₺</strong>
                                    </td>
                                    <td>
                                        {!! $order->status_badge !!}
                                    </td>
                                    <td>
                                        {{ $order->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="btn btn-sm btn-info"
                                           title="Detay">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.orders.destroy', $order) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Bu siparişi silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3">Sipariş Bulunamadı</h4>
                    <p class="text-muted">Henüz hiç sipariş oluşturulmamış.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

