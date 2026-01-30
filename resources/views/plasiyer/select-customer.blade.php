@extends('layouts.app')

@section('title', 'Müşteri Seçimi')

@push('styles')
    <style>
        .customer-table-wrapper {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .customer-table-header {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 1.5rem;
        }

        .search-input {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .search-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, .25);
        }

        .customers-table {
            font-size: 0.875rem;
            margin-bottom: 0;
        }

        .customers-table thead th {
            background: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 0.5rem 0.4rem;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .customers-table tbody tr {
            border-bottom: 1px solid #f1f1f1;
            transition: background 0.15s;
        }

        .customers-table tbody tr:hover {
            background: #f8f9fa;
        }

        .customers-table td {
            padding: 0.5rem 0.4rem;
            vertical-align: middle;
        }

        .customers-table td .mt-1 {
            margin-top: 0.2rem;
        }

        .table-scroll {
            max-height: calc(100vh - 280px);
            overflow-y: auto;
        }

        .btn-select {
            padding: 0.3rem 0.8rem;
            font-size: 0.85rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="customer-table-wrapper">
                    <div class="customer-table-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2 mb-md-0"><i class="fas fa-users me-2"></i>Müşteri Seçimi</h5>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control search-input" id="customerSearch"
                                    placeholder="Ara...">
                            </div>
                        </div>
                    </div>

                    @if($customers->count() > 0)
                        <div class="table-scroll">
                            <table class="table table-hover customers-table" id="customersTable">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">Kod</th>
                                        <th style="width: 180px;">Müşteri Adı</th>
                                        <th style="width: 110px;">İlçe / İl</th>
                                        <th class="text-center" style="width: 110px;">Son Giriş</th>
                                        <th class="text-center" style="width: 130px;">Sepet</th>
                                        <th class="text-center" style="width: 60px;">SEÇ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                        @php
                                            $searchText = $customer->username . ' ' . $customer->name . ' ' . $customer->ilce . ' ' . $customer->il . ' ' . $customer->telefon;
                                            // Türkçe karakterleri ASCII eşdeğerlerine çevir
                                            $normalized = strtr($searchText, [
                                                'İ' => 'I',
                                                'ı' => 'i',
                                                'Ş' => 'S',
                                                'ş' => 's',
                                                'Ğ' => 'G',
                                                'ğ' => 'g',
                                                'Ü' => 'U',
                                                'ü' => 'u',
                                                'Ö' => 'O',
                                                'ö' => 'o',
                                                'Ç' => 'C',
                                                'ç' => 'c'
                                            ]);
                                            $normalized = mb_strtolower($normalized, 'UTF-8');
                                        @endphp
                                        <tr class="customer-row" data-search="{{ $normalized }}">
                                            <td><span class="badge bg-secondary">{{ $customer->username }}</span></td>
                                            <td><strong>{{ \Str::limit($customer->name, 30) }}</strong></td>
                                            <td class="text-muted small">{{ $customer->ilce ?? '-' }} / {{ $customer->il ?? '-' }}
                                            </td>
                                            <td class="text-center">
                                                @if($customer->last_login_at)
                                                    <small class="text-muted"
                                                        title="{{ $customer->last_login_at->format('d.m.Y H:i:s') }}">
                                                        {{ $customer->last_login_at->diffForHumans() }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($customer->cart_count > 0)
                                                    <div class="small"><strong>{{ $customer->cart_item_count }} kalem</strong> /
                                                        {{ $customer->cart_count }} adet
                                                    </div>
                                                    <div class="mt-1"><strong
                                                            class="text-success small">{{ number_format($customer->cart_total, 2, ',', '.') }}
                                                            ₺</strong></div>
                                                @else
                                                    <span class="text-muted small">Boş</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('plasiyer.setCustomer') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                    <button type="submit" class="btn btn-sm btn-primary btn-select">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="noResults" style="display: none;" class="alert alert-info m-3 text-center">
                            Arama sonucu bulunamadı.
                        </div>
                    @else
                        <div class="alert alert-warning m-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Size atanmış müşteri bulunmamaktadır.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('customerSearch');
            const customerRows = document.querySelectorAll('.customer-row');
            const noResults = document.getElementById('noResults');
            const table = document.getElementById('customersTable');

            // Türkçe karakter normalleştirme - tüm varyasyonları aynı karaktere çevirir
            function normalizeTurkish(str) {
                if (!str) return '';
                // Önce Türkçe karakterleri ASCII eşdeğerlerine çevir, sonra küçük harfe
                return str
                    .replace(/İ/g, 'I')
                    .replace(/ı/g, 'i')
                    .replace(/Ş/g, 'S')
                    .replace(/ş/g, 's')
                    .replace(/Ğ/g, 'G')
                    .replace(/ğ/g, 'g')
                    .replace(/Ü/g, 'U')
                    .replace(/ü/g, 'u')
                    .replace(/Ö/g, 'O')
                    .replace(/ö/g, 'o')
                    .replace(/Ç/g, 'C')
                    .replace(/ç/g, 'c')
                    .toLowerCase();
            }

            if (searchInput && customerRows.length > 0) {
                // Her satır için normalize edilmiş arama verisi oluştur
                customerRows.forEach(function (row) {
                    const originalSearch = row.getAttribute('data-search') || '';
                    row.setAttribute('data-search-normalized', normalizeTurkish(originalSearch));
                });

                searchInput.addEventListener('keyup', function () {
                    const searchTerm = normalizeTurkish(this.value.trim());
                    let visibleCount = 0;

                    customerRows.forEach(function (row) {
                        const searchData = row.getAttribute('data-search-normalized');

                        if (searchData.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    if (visibleCount === 0) {
                        table.style.display = 'none';
                        noResults.style.display = 'block';
                    } else {
                        table.style.display = 'table';
                        noResults.style.display = 'none';
                    }
                });

                // Enter ile ilk sonucu seç
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        const firstVisible = Array.from(customerRows).find(row => row.style.display !== 'none');
                        if (firstVisible) {
                            firstVisible.querySelector('form').submit();
                        }
                    }
                });
            }
        });
    </script>
@endpush