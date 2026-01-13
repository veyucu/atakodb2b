@extends('layouts.app')

@section('title', 'Cari Ekstre - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@push('styles')
    <style>
        /* Cari Ekstre Tablo Stili */
        .cari-ekstre-table {
            background: white;
            font-size: 0.9rem;
            border-collapse: collapse !important;
            border-spacing: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            overflow: hidden;
        }

        .cari-ekstre-table thead {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
            color: white;
        }

        .cari-ekstre-table thead th {
            background: transparent !important;
            color: #ffffff !important;
            border: 0 !important;
            padding: 12px 10px;
            font-weight: 600;
            font-size: 0.85rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            white-space: nowrap;
        }

        .cari-ekstre-table tbody tr {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .cari-ekstre-table tbody tr:hover {
            background-color: #f0f7ff;
            transform: scale(1.005);
            box-shadow: 0 2px 8px rgba(30, 60, 114, 0.1);
        }

        .cari-ekstre-table tbody td {
            padding: 12px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef !important;
            border-left: 0 !important;
            border-right: 0 !important;
        }

        .cari-ekstre-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* Borç/Alacak Renkleri */
        .text-borc {
            color: #dc3545;
            font-weight: 600;
        }

        .text-alacak {
            color: #28a745;
            font-weight: 600;
        }

        /* Özet Kartları */
        .summary-card {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .summary-card-borc {
            background: linear-gradient(135deg, #fff1f0 0%, #ffe7e6 100%);
            border-left: 4px solid #dc3545;
        }

        .summary-card-alacak {
            background: linear-gradient(135deg, #f0fff4 0%, #e6ffed 100%);
            border-left: 4px solid #28a745;
        }

        .summary-card-bakiye {
            background: linear-gradient(135deg, #f0f4ff 0%, #e6ebff 100%);
            border-left: 4px solid #1e3c72;
        }

        /* Tarih Picker */
        .date-range-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #e9ecef;
        }

        /* Hareket Türü Badges */
        .badge-fatura {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge-tahsilat {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .badge-iade {
            background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
            color: white;
        }

        /* Detay Modal */
        #detailModal .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        #detailModal .modal-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        #detailModal .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
        }

        .detail-value {
            font-size: 1rem;
            color: #212529;
        }

        /* Dark Theme */
        [data-theme="dark"] .cari-ekstre-table {
            background: #2d2d2d;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        [data-theme="dark"] .cari-ekstre-table tbody tr {
            background-color: #2d2d2d;
        }

        [data-theme="dark"] .cari-ekstre-table tbody tr:hover {
            background-color: #3a3a3a;
        }

        [data-theme="dark"] .cari-ekstre-table tbody td {
            border-bottom-color: #444 !important;
            color: #e0e0e0;
        }

        [data-theme="dark"] .summary-card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        [data-theme="dark"] .summary-card-borc {
            background: linear-gradient(135deg, #3d2a2a 0%, #4d3333 100%);
        }

        [data-theme="dark"] .summary-card-alacak {
            background: linear-gradient(135deg, #2a3d2a 0%, #334d33 100%);
        }

        [data-theme="dark"] .summary-card-bakiye {
            background: linear-gradient(135deg, #2a2a3d 0%, #33334d 100%);
        }

        [data-theme="dark"] .date-range-card {
            background: linear-gradient(135deg, #2d2d2d 0%, #3a3a3a 100%);
            border-color: #444;
        }

        [data-theme="dark"] #detailModal .modal-content {
            background-color: #2d2d2d;
        }

        [data-theme="dark"] #detailModal .modal-body {
            background-color: #2d2d2d;
            color: #e0e0e0;
        }

        [data-theme="dark"] .detail-value {
            color: #e0e0e0;
        }

        /* Mobil Responsive */
        @media (max-width: 768px) {
            .cari-ekstre-table {
                font-size: 0.8rem;
            }

            .cari-ekstre-table thead th {
                padding: 8px 6px;
                font-size: 0.75rem;
            }

            .cari-ekstre-table tbody td {
                padding: 8px 6px;
            }

            .summary-card {
                padding: 0.75rem 1rem;
                margin-bottom: 0.5rem;
            }

            /* Mobilde bazı sütunları gizle */
            .hide-mobile {
                display: none;
            }
        }

        /* Çift tıklama ipucu */
        .double-click-hint {
            font-size: 0.8rem;
            color: #6c757d;
        }

        [data-theme="dark"] .double-click-hint {
            color: #aaa;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <!-- Sayfa Başlığı -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Cari Ekstre
                </h4>
                <p class="text-muted mb-0">
                    <strong>{{ $musteriAdi }}</strong>
                    @if($musteriKodu)
                        <span class="badge bg-secondary ms-2">{{ $musteriKodu }}</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Ana Sayfa
            </a>
        </div>

        <!-- Tarih Filtresi -->
        <div class="date-range-card p-3 mb-4">
            <form action="{{ route('cari.ekstre') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4 col-6">
                    <label for="baslangic_tarihi" class="form-label fw-semibold">
                        <i class="fas fa-calendar-alt me-1"></i>Başlangıç Tarihi
                    </label>
                    <input type="date" class="form-control" id="baslangic_tarihi" name="baslangic_tarihi"
                        value="{{ $baslangicTarihi }}">
                </div>
                <div class="col-md-4 col-6">
                    <label for="bitis_tarihi" class="form-label fw-semibold">
                        <i class="fas fa-calendar-alt me-1"></i>Bitiş Tarihi
                    </label>
                    <input type="date" class="form-control" id="bitis_tarihi" name="bitis_tarihi"
                        value="{{ $bitisTarihi }}">
                </div>
                <div class="col-md-4 col-12">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Sorgula
                    </button>
                </div>
            </form>
        </div>

        <!-- Özet Kartları -->
        <div class="row mb-4">
            <div class="col-md-4 col-12 mb-2 mb-md-0">
                <div class="summary-card summary-card-borc">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Toplam Borç</small>
                            <h4 class="mb-0 text-borc">{{ number_format($toplamBorc, 2, ',', '.') }} ₺</h4>
                        </div>
                        <i class="fas fa-arrow-up fa-2x text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-2 mb-md-0">
                <div class="summary-card summary-card-alacak">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Toplam Alacak</small>
                            <h4 class="mb-0 text-alacak">{{ number_format($toplamAlacak, 2, ',', '.') }} ₺</h4>
                        </div>
                        <i class="fas fa-arrow-down fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="summary-card summary-card-bakiye">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Genel Bakiye</small>
                            <h4 class="mb-0" style="color: #1e3c72;">{{ number_format($genelBakiye, 2, ',', '.') }} ₺</h4>
                        </div>
                        <i class="fas fa-balance-scale fa-2x opacity-50" style="color: #1e3c72;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ekstre Tablosu -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>Hesap Hareketleri
                        <span class="badge bg-secondary ms-2">{{ count($hareketler) }} kayıt</span>
                    </h6>
                    <span class="double-click-hint">
                        <i class="fas fa-mouse-pointer me-1"></i>Detay için satıra çift tıklayın
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @if(count($hareketler) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover cari-ekstre-table mb-0">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Belge No</th>
                                    <th>Hareket Türü</th>
                                    <th class="hide-mobile">Açıklama</th>
                                    <th class="text-end">Borç</th>
                                    <th class="text-end">Alacak</th>
                                    <th class="text-end">Bakiye</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hareketler as $index => $hareket)
                                    <tr ondblclick="showDetail({{ $index }})" data-hareket='@json($hareket)'
                                        title="Detay için çift tıklayın">
                                        <td>
                                            <i class="fas fa-calendar-day me-1 text-muted"></i>
                                            {{ \Carbon\Carbon::parse($hareket['tarih'])->format('d.m.Y') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $hareket['belge_no'] }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = 'bg-secondary';
                                                if (str_contains($hareket['hareket_turu'], 'Fatura') && !str_contains($hareket['hareket_turu'], 'İade')) {
                                                    $badgeClass = 'badge-fatura';
                                                } elseif (str_contains($hareket['hareket_turu'], 'Tahsilat')) {
                                                    $badgeClass = 'badge-tahsilat';
                                                } elseif (str_contains($hareket['hareket_turu'], 'İade')) {
                                                    $badgeClass = 'badge-iade';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $hareket['hareket_turu'] }}</span>
                                        </td>
                                        <td class="hide-mobile">{{ $hareket['aciklama'] }}</td>
                                        <td class="text-end">
                                            @if($hareket['borc'] > 0)
                                                <span class="text-borc">{{ number_format($hareket['borc'], 2, ',', '.') }} ₺</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($hareket['alacak'] > 0)
                                                <span class="text-alacak">{{ number_format($hareket['alacak'], 2, ',', '.') }} ₺</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ number_format($hareket['bakiye'], 2, ',', '.') }} ₺</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #f8f9fa;">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold hide-mobile">Toplam:</td>
                                    <td colspan="3" class="text-end fw-bold d-md-none">Toplam:</td>
                                    <td class="text-end text-borc fw-bold">{{ number_format($toplamBorc, 2, ',', '.') }} ₺</td>
                                    <td class="text-end text-alacak fw-bold">{{ number_format($toplamAlacak, 2, ',', '.') }} ₺
                                    </td>
                                    <td class="text-end fw-bold" style="color: #1e3c72;">
                                        {{ number_format($genelBakiye, 2, ',', '.') }} ₺</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Seçilen tarih aralığında hareket bulunamadı.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Hareket Detay Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title mb-0" id="detailModalLabel">
                        <i class="fas fa-file-alt me-2"></i>Hareket Detayı
                    </h6>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body py-2">
                    <!-- Kompakt Üst Bilgiler -->
                    <div class="d-flex flex-wrap gap-3 mb-2 p-2 rounded" style="background: #f8f9fa; font-size: 0.85rem;">
                        <div><span class="text-muted">Tarih:</span> <strong id="detail-tarih">-</strong></div>
                        <div><span class="text-muted">Belge No:</span> <strong id="detail-belge-no">-</strong></div>
                        <div><span class="text-muted">Vade:</span> <strong id="detail-vade">-</strong></div>
                        <div><span class="text-muted">Tür:</span> <span id="detail-hareket-turu" class="badge bg-secondary">-</span></div>
                    </div>
                    <div class="mb-2" style="font-size: 0.85rem;">
                        <span class="text-muted">Açıklama:</span> <span id="detail-aciklama">-</span>
                    </div>
                    <div class="d-flex gap-4 mb-2" style="font-size: 0.9rem;">
                        <div><span class="text-muted">Borç:</span> <strong class="text-borc" id="detail-borc">-</strong></div>
                        <div><span class="text-muted">Alacak:</span> <strong class="text-alacak" id="detail-alacak">-</strong></div>
                    </div>
                    
                    <!-- Belge Kalemleri (Fatura/İade için) -->
                    <div id="kalemler-container" style="display: none;">
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="fw-semibold text-muted">
                                <i class="fas fa-boxes me-1"></i>Belge Kalemleri
                            </small>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" id="kalemler-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size: 0.75rem;">Ürün Kodu</th>
                                        <th style="font-size: 0.75rem;">Ürün Adı</th>
                                        <th class="text-center" style="font-size: 0.75rem;">Miktar</th>
                                        <th class="text-end" style="font-size: 0.75rem;">Birim Fiyat</th>
                                        <th class="text-end" style="font-size: 0.75rem;">Tutar</th>
                                    </tr>
                                </thead>
                                <tbody id="kalemler-body">
                                    <!-- JavaScript ile doldurulacak -->
                                </tbody>
                                <tfoot id="kalemler-footer" class="table-light">
                                    <!-- JavaScript ile doldurulacak -->
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Sayı formatla
        function formatCurrency(number) {
            return new Intl.NumberFormat('tr-TR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number) + ' ₺';
        }

        // Tarih formatla
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('tr-TR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        // Hareket detayını göster
        function showDetail(index) {
            const rows = document.querySelectorAll('.cari-ekstre-table tbody tr');
            const row = rows[index];

            if (!row) return;

            const hareket = JSON.parse(row.dataset.hareket);

            // Modal alanlarını doldur (kompakt)
            document.getElementById('detail-tarih').textContent = formatDate(hareket.tarih);
            document.getElementById('detail-belge-no').textContent = hareket.belge_no;
            document.getElementById('detail-vade').textContent = formatDate(hareket.vade_tarihi);
            document.getElementById('detail-hareket-turu').textContent = hareket.hareket_turu;
            document.getElementById('detail-aciklama').textContent = hareket.aciklama;
            document.getElementById('detail-borc').textContent = hareket.borc > 0 ? formatCurrency(hareket.borc) : '-';
            document.getElementById('detail-alacak').textContent = hareket.alacak > 0 ? formatCurrency(hareket.alacak) : '-';

            // Hareket türüne göre badge rengi
            const turuEl = document.getElementById('detail-hareket-turu');
            turuEl.className = 'badge ';
            if (hareket.hareket_turu.includes('Fatura') && !hareket.hareket_turu.includes('İade')) {
                turuEl.className += 'badge-fatura';
            } else if (hareket.hareket_turu.includes('Tahsilat')) {
                turuEl.className += 'badge-tahsilat';
            } else if (hareket.hareket_turu.includes('İade')) {
                turuEl.className += 'badge-iade';
            } else {
                turuEl.className += 'bg-secondary';
            }

            // Belge kalemlerini göster (Fatura veya İade ise)
            const kalemlerContainer = document.getElementById('kalemler-container');
            const kalemlerBody = document.getElementById('kalemler-body');
            const kalemlerFooter = document.getElementById('kalemler-footer');
            
            if (hareket.kalemler && hareket.kalemler.length > 0) {
                // Kalemleri tabloya ekle
                kalemlerBody.innerHTML = '';
                let toplamTutar = 0;
                
                hareket.kalemler.forEach(function(kalem) {
                    toplamTutar += kalem.tutar;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="font-size: 0.8rem;"><code>${kalem.urun_kodu}</code></td>
                        <td style="font-size: 0.8rem;">${kalem.urun_adi}</td>
                        <td class="text-center" style="font-size: 0.8rem;">${kalem.miktar}</td>
                        <td class="text-end" style="font-size: 0.8rem;">${formatCurrency(kalem.birim_fiyat)}</td>
                        <td class="text-end fw-semibold" style="font-size: 0.8rem;">${formatCurrency(kalem.tutar)}</td>
                    `;
                    kalemlerBody.appendChild(tr);
                });
                
                // Alt toplamları hesapla (KDV %20 varsayım)
                const kdvOrani = hareket.kdv_orani || 20;
                const kdvHaricTutar = toplamTutar / (1 + kdvOrani / 100);
                const kdvTutari = toplamTutar - kdvHaricTutar;
                
                // Footer'ı doldur
                kalemlerFooter.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-end" style="font-size: 0.8rem;">KDV Hariç Tutar:</td>
                        <td class="text-end fw-semibold" style="font-size: 0.8rem;">${formatCurrency(kdvHaricTutar)}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end" style="font-size: 0.8rem;">KDV (%${kdvOrani}):</td>
                        <td class="text-end fw-semibold" style="font-size: 0.8rem;">${formatCurrency(kdvTutari)}</td>
                    </tr>
                    <tr style="background: #e9ecef;">
                        <td colspan="4" class="text-end fw-bold" style="font-size: 0.85rem;">Genel Toplam:</td>
                        <td class="text-end fw-bold" style="font-size: 0.9rem; color: #1e3c72;">${formatCurrency(toplamTutar)}</td>
                    </tr>
                `;
                
                kalemlerContainer.style.display = 'block';
            } else {
                kalemlerContainer.style.display = 'none';
            }

            // Modal'ı aç
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }
    </script>
@endpush