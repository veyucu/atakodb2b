@extends('layouts.app')

@section('title', 'Plasiyer Panel')

@push('styles')
<style>
    .stats-card {
        border-radius: 10px;
        padding: 1.5rem;
        color: white;
        height: 100%;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-card.blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stats-card.green {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stats-card.orange {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stats-card.purple {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .stats-icon {
        font-size: 3rem;
        opacity: 0.8;
    }
    
    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }
    
    .chart-container {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .chart-wrapper {
        position: relative;
        height: 250px;
        width: 100%;
        max-height: 250px;
    }
    
    .chart-wrapper.tall {
        height: 150px;
        max-height: 150px;
    }
    
    .chart-wrapper canvas {
        max-height: 100%;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>Plasiyer Dashboard
            </h3>
            <p class="text-muted">Müşterilerinizin istatistikleri ve aktiviteleri</p>
        </div>
    </div>
    
    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card blue">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-uppercase small mb-1">Toplam Müşteri</div>
                        <div class="stats-number">{{ $stats['total_customers'] }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card green">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-uppercase small mb-1">Bu Ay Aktif</div>
                        <div class="stats-number">{{ $stats['active_customers_this_month'] }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card orange">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-uppercase small mb-1">Bu Ay Sipariş</div>
                        <div class="stats-number">{{ $stats['orders_this_month'] }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card purple">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-uppercase small mb-1">Bu Ay Ciro</div>
                        <div class="stats-number">{{ number_format($stats['revenue_this_month'], 0, ',', '.') }} ₺</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-lira-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafikler -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Son 12 Ay Sipariş Sayısı</h5>
                <div class="chart-wrapper">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-chart-area me-2"></i>Son 12 Ay Ciro (₺)</h5>
                <div class="chart-wrapper">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 mb-4">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Son 12 Ay Müşteri Aktiviteleri</h5>
                <div class="chart-wrapper tall">
                    <canvas id="activitiesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hızlı Erişim -->
    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-bolt me-2"></i>Hızlı Erişim</h5>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('plasiyer.selectCustomer') }}" class="btn btn-primary w-100">
                            <i class="fas fa-user-check me-2"></i>Müşteri Seç
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('plasiyer.customerActivities') }}" class="btn btn-success w-100">
                            <i class="fas fa-chart-line me-2"></i>Müşteri Aktiviteleri
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-info w-100">
                            <i class="fas fa-shopping-cart me-2"></i>Siparişler
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('home') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-home me-2"></i>Ana Sayfa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Veri hazırlama
    const monthlyData = @json($monthlyStats);
    const labels = monthlyData.map(item => item.month_short);
    const ordersData = monthlyData.map(item => item.orders_count);
    const revenueData = monthlyData.map(item => item.orders_total);
    const activitiesData = monthlyData.map(item => item.activities_count);
    
    // Sipariş Sayısı Grafiği
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        new Chart(ordersCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sipariş Sayısı',
                    data: ordersData,
                    borderColor: '#4facfe',
                    backgroundColor: 'rgba(79, 172, 254, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                resizeDelay: 200,
                animation: {
                    duration: 750
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    
    // Ciro Grafiği
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ciro (₺)',
                    data: revenueData,
                    backgroundColor: 'rgba(67, 233, 123, 0.8)',
                    borderColor: '#43e97b',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                resizeDelay: 200,
                animation: {
                    duration: 750
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('tr-TR') + ' ₺';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Aktivite Grafiği
    const activitiesCtx = document.getElementById('activitiesChart');
    if (activitiesCtx) {
        new Chart(activitiesCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Aktivite Sayısı',
                    data: activitiesData,
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: '#667eea',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                resizeDelay: 200,
                animation: {
                    duration: 750
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endpush
