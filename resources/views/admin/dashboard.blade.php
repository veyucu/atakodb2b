@extends('layouts.app')

@section('title', 'Admin Dashboard - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="fas fa-tachometer-alt"></i> Admin Dashboard
    </h2>

    <div class="row">
        <!-- Products Stats -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
                <div class="card text-white bg-primary" style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Toplam Ürün</h6>
                                <h2 class="mb-0">{{ $stats['total_products'] }}</h2>
                                <small>Aktif: {{ $stats['active_products'] }}</small>
                            </div>
                            <div>
                                <i class="fas fa-box fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Users Stats -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="card text-white bg-info" style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Toplam Kullanıcı</h6>
                                <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                                <small>Admin: {{ $stats['admin_users'] }} | Plasiyer: {{ $stats['plasiyer_users'] }}</small>
                            </div>
                            <div>
                                <i class="fas fa-user-circle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Orders Stats -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                <div class="card text-white bg-danger" style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Toplam Sipariş</h6>
                                <h2 class="mb-0">{{ $stats['total_orders'] }}</h2>
                                <small>Bekleyen: {{ $stats['pending_orders'] }}</small>
                            </div>
                            <div>
                                <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Second Row - Revenue -->
    <div class="row">
        <!-- Revenue Stats -->
        <div class="col-md-12 mb-4">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Toplam Ciro</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_revenue'], 2, ',', '.') }} ₺</h2>
                            <small>İptal edilmemiş siparişler</small>
                        </div>
                        <div>
                            <i class="fas fa-lira-sign fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Hızlı İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-images fa-2x d-block mb-2"></i>
                                Slider Yönetimi
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-shopping-cart fa-2x d-block mb-2"></i>
                                Sipariş Yönetimi
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('plasiyer.selectCustomer') }}" class="btn btn-outline-secondary w-100 py-3">
                                <i class="fas fa-user-check fa-2x d-block mb-2"></i>
                                Müşteri Seçimi
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-dark w-100 py-3">
                                <i class="fas fa-cog fa-2x d-block mb-2"></i>
                                Site Ayarları
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Activities Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Müşteri Aktiviteleri (Son 30 Gün)</h5>
                </div>
                <div class="card-body">
                    <!-- Activity Stats Row -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <h3 class="mb-0">{{ $activityStats['active_customers_count'] }}</h3>
                                    <small class="text-muted">Aktif Müşteri</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-search fa-2x text-info mb-2"></i>
                                    <h3 class="mb-0">{{ $activityStats['total_searches'] }}</h3>
                                    <small class="text-muted">Toplam Arama</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-eye fa-2x text-success mb-2"></i>
                                    <h3 class="mb-0">{{ $activityStats['total_product_views'] }}</h3>
                                    <small class="text-muted">Ürün Görüntüleme</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-sign-in-alt fa-2x text-warning mb-2"></i>
                                    <h3 class="mb-0">{{ $activityStats['total_logins'] }}</h3>
                                    <small class="text-muted">Toplam Giriş</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Top Searches -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-search me-2"></i>En Çok Aranan Terimler</h6>
                                </div>
                                <div class="card-body">
                                    @if($activityStats['top_searches']->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Arama Terimi</th>
                                                        <th class="text-end">Arama Sayısı</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($activityStats['top_searches'] as $search)
                                                        <tr>
                                                            <td>{{ $search['query'] }}</td>
                                                            <td class="text-end">
                                                                <span class="badge bg-primary">{{ $search['count'] }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">Son 30 günde arama yapılmadı.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Most Active Customers -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-fire me-2"></i>En Aktif Müşteriler</h6>
                                </div>
                                <div class="card-body">
                                    @if($activityStats['most_active_customers']->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Müşteri</th>
                                                        <th class="text-end">Aktivite Sayısı</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($activityStats['most_active_customers'] as $activity)
                                                        <tr>
                                                            <td>
                                                                @if($activity->user)
                                                                    <strong>{{ $activity->user->username }}</strong><br>
                                                                    <small class="text-muted">{{ $activity->user->name }}</small>
                                                                @else
                                                                    <span class="text-muted">Bilinmeyen Müşteri</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                <span class="badge bg-success">{{ $activity->activity_count }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">Son 30 günde aktivite bulunmamaktadır.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


