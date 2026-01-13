@extends('layouts.app')

@section('title', 'Müşteri Aktiviteleri')

@push('styles')
<style>
    .activity-card {
        border-left: 4px solid #0d6efd;
        transition: transform 0.2s;
    }
    
    .activity-card:hover {
        transform: translateX(4px);
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .activity-icon.search {
        background: #e3f2fd;
        color: #1976d2;
    }
    
    .activity-icon.view {
        background: #e8f5e9;
        color: #388e3c;
    }
    
    .activity-icon.modal {
        background: #fff3e0;
        color: #f57c00;
    }
    
    .stat-card {
        border-radius: 8px;
        padding: 1.25rem;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        height: 100%;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .customer-selector {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">
                <i class="fas fa-chart-line me-2"></i>Müşteri Aktiviteleri
            </h4>
        </div>
    </div>
    
    <!-- Müşteri Seçim -->
    <div class="customer-selector">
        <form method="GET" action="{{ route('plasiyer.customerActivities') }}">
            <div class="row align-items-end">
                <div class="col-md-8">
                    <label for="customer_id" class="form-label fw-bold">Müşteri Seçin</label>
                    <select name="customer_id" id="customer_id" class="form-select" required>
                        <option value="">-- Müşteri Seçin --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ ($selectedCustomer && $selectedCustomer->id == $customer->id) ? 'selected' : '' }}>
                                {{ $customer->username }} - {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Görüntüle
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    @if($selectedCustomer)
        <!-- Müşteri Bilgileri -->
        <div class="alert alert-info mb-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>{{ $selectedCustomer->name }}
                    </h5>
                    <small class="text-muted">{{ $selectedCustomer->username }} | {{ $selectedCustomer->ilce }} / {{ $selectedCustomer->il }}</small>
                </div>
                <div class="col-md-4 text-end">
                    @if($selectedCustomer->last_login_at)
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>Son Giriş: {{ $selectedCustomer->last_login_at->format('d.m.Y H:i') }}
                        </small>
                    @endif
                </div>
            </div>
        </div>
        
        @if($stats)
            <!-- İstatistikler -->
            <div class="row mb-4">
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="activity-icon search me-2">
                                <i class="fas fa-search"></i>
                            </div>
                            <div>
                                <div class="stat-number text-primary" style="font-size: 1.8rem;">{{ $stats['total_searches'] }}</div>
                                <small class="text-muted">Arama</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="activity-icon view me-2">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <div class="stat-number text-success" style="font-size: 1.8rem;">{{ $stats['total_product_views'] }}</div>
                                <small class="text-muted">Ürün Detay</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="activity-icon modal me-2">
                                <i class="fas fa-window-restore"></i>
                            </div>
                            <div>
                                <div class="stat-number text-warning" style="font-size: 1.8rem;">{{ $stats['total_modal_views'] }}</div>
                                <small class="text-muted">Ürün Popup</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="activity-icon me-2" style="background: #fef3c7; color: #f59e0b;">
                                <i class="fas fa-gift"></i>
                            </div>
                            <div>
                                <div class="stat-number" style="font-size: 1.8rem; color: #f59e0b;">{{ $stats['total_campaign_views'] }}</div>
                                <small class="text-muted">Kampanya</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="activity-icon me-2" style="background: #ddd6fe; color: #7c3aed;">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div>
                                <div class="stat-number" style="font-size: 1.8rem; color: #7c3aed;">{{ $stats['total_logins'] }}</div>
                                <small class="text-muted">Giriş</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="activity-icon view me-2">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <div class="stat-number text-info" style="font-size: 1.8rem;">{{ $stats['total_product_views'] + $stats['total_modal_views'] + $stats['total_campaign_views'] }}</div>
                                <small class="text-muted">Toplam</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Login Tarihleri -->
            @if($stats['login_dates']->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <i class="fas fa-calendar-alt me-2"></i>Son Giriş Tarihleri (Son 30 Gün)
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($stats['login_dates'] as $date)
                                    <span class="badge bg-purple text-white" style="background-color: #7c3aed !important; font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                                        <i class="fas fa-clock me-1"></i>{{ $date->format('d.m.Y H:i') }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- En Çok Arananlar ve En Çok Bakılanlar -->
            <div class="row mb-4">
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-search me-2"></i>En Çok Aranan 10 Kelime
                        </div>
                        <div class="card-body">
                            @if($stats['top_searches']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Arama</th>
                                                <th class="text-center">Arama Sayısı</th>
                                                <th class="text-center">Ort. Sonuç</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stats['top_searches'] as $search)
                                                <tr>
                                                    <td><strong>{{ $search['query'] }}</strong></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary">{{ $search['count'] }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary">{{ $search['avg_results'] }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">Henüz arama yapmamış.</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-eye me-2"></i>En Çok Bakılan 10 Ürün
                        </div>
                        <div class="card-body">
                            @if($stats['top_viewed_products']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Ürün Kodu</th>
                                                <th>Ürün Adı</th>
                                                <th class="text-center">Görüntüleme</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stats['top_viewed_products'] as $product)
                                                <tr>
                                                    <td><code>{{ $product['product_code'] }}</code></td>
                                                    <td><small>{{ Str::limit($product['product_name'], 40) }}</small></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success">{{ $product['view_count'] }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">Henüz ürün görüntülememiş.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Aktivite Listesi -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Son 30 Günün Aktiviteleri
                </h5>
            </div>
            <div class="card-body">
                @if($activities->count() > 0)
                    @foreach($activities as $activity)
                        <div class="card activity-card mb-2">
                            <div class="card-body py-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="activity-icon 
                                            {{ $activity->activity_type == 'search' ? 'search' : '' }}
                                            {{ $activity->activity_type == 'product_view' ? 'view' : '' }}
                                            {{ $activity->activity_type == 'product_modal_view' ? 'modal' : '' }}
                                            {{ $activity->activity_type == 'campaign_popup' ? '' : '' }}
                                            {{ $activity->activity_type == 'login' ? '' : '' }}"
                                            style="{{ $activity->activity_type == 'campaign_popup' ? 'background: #fef3c7; color: #f59e0b;' : '' }}
                                                   {{ $activity->activity_type == 'login' ? 'background: #ddd6fe; color: #7c3aed;' : '' }}">
                                            @if($activity->activity_type == 'search')
                                                <i class="fas fa-search"></i>
                                            @elseif($activity->activity_type == 'product_view')
                                                <i class="fas fa-eye"></i>
                                            @elseif($activity->activity_type == 'product_modal_view')
                                                <i class="fas fa-window-restore"></i>
                                            @elseif($activity->activity_type == 'campaign_popup')
                                                <i class="fas fa-gift"></i>
                                            @elseif($activity->activity_type == 'login')
                                                <i class="fas fa-sign-in-alt"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col">
                                        @if($activity->activity_type == 'search')
                                            <strong>Arama:</strong> "{{ $activity->activity_data['query'] ?? '-' }}"
                                            <span class="badge bg-secondary ms-2">{{ $activity->activity_data['result_count'] ?? 0 }} sonuç</span>
                                        @elseif($activity->activity_type == 'product_view')
                                            <strong>Ürün Görüntüledi:</strong> {{ $activity->activity_data['product_name'] ?? '-' }}
                                            <code class="ms-2">{{ $activity->activity_data['product_code'] ?? '-' }}</code>
                                        @elseif($activity->activity_type == 'product_modal_view')
                                            <strong>Ürün Popup:</strong> {{ $activity->activity_data['product_name'] ?? '-' }}
                                            <code class="ms-2">{{ $activity->activity_data['product_code'] ?? '-' }}</code>
                                        @elseif($activity->activity_type == 'campaign_popup')
                                            <strong>Kampanya Popup Görüntüledi</strong>
                                        @elseif($activity->activity_type == 'login')
                                            <strong>Sisteme Giriş Yaptı</strong>
                                            <span class="badge bg-success ms-2">{{ $activity->ip_address }}</span>
                                        @endif
                                    </div>
                                    <div class="col-auto text-end">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $activity->created_at->format('d.m.Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-3">
                        {{ $activities->appends(['customer_id' => request('customer_id')])->links() }}
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>Son 30 günde aktivite kaydı bulunmamaktadır.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

