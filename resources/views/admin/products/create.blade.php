@extends('layouts.app')

@section('title', 'Yeni Ürün Ekle - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plus"></i> Yeni Ürün Ekle
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                            id="productForm">
                            @csrf

                            <!-- Temel Bilgiler -->
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label for="urun_kodu" class="form-label">Ürün Kodu <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control form-control-sm @error('urun_kodu') is-invalid @enderror"
                                        id="urun_kodu" name="urun_kodu" value="{{ old('urun_kodu') }}" required>
                                    @error('urun_kodu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="urun_adi" class="form-label">Ürün Adı <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control form-control-sm @error('urun_adi') is-invalid @enderror"
                                        id="urun_adi" name="urun_adi" value="{{ old('urun_adi') }}" required>
                                    @error('urun_adi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="barkod" class="form-label">Barkod</label>
                                    <input type="text"
                                        class="form-control form-control-sm @error('barkod') is-invalid @enderror"
                                        id="barkod" name="barkod" value="{{ old('barkod') }}">
                                    @error('barkod')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label for="marka" class="form-label">Marka</label>
                                    <input type="text"
                                        class="form-control form-control-sm @error('marka') is-invalid @enderror" id="marka"
                                        name="marka" value="{{ old('marka') }}">
                                </div>

                                <div class="col-md-4">
                                    <label for="grup" class="form-label">Grup</label>
                                    <input type="text"
                                        class="form-control form-control-sm @error('grup') is-invalid @enderror" id="grup"
                                        name="grup" value="{{ old('grup') }}">
                                </div>

                                <div class="col-md-4">
                                    <label for="muadil_kodu" class="form-label">Muadil Kodu</label>
                                    <input type="text"
                                        class="form-control form-control-sm @error('muadil_kodu') is-invalid @enderror"
                                        id="muadil_kodu" name="muadil_kodu" value="{{ old('muadil_kodu') }}">
                                </div>
                            </div>

                            <!-- Fiyat ve İskonto Bilgileri -->
                            <div class="row mb-3">
                                <!-- PSF -->
                                <div class="col-md-2">
                                    <div class="border rounded p-2 h-100" style="background-color: #f8f9fa;">
                                        <label for="satis_fiyati" class="form-label fw-bold mb-1"
                                            style="font-size: 0.85rem;">PSF <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('satis_fiyati') is-invalid @enderror"
                                            id="satis_fiyati" name="satis_fiyati" value="{{ old('satis_fiyati') }}"
                                            oninput="hesaplaDepocuFiyati()" required>
                                    </div>
                                </div>

                                <!-- İskontolar -->
                                <div class="col-md-3">
                                    <div class="border rounded p-2 h-100">
                                        <div class="fw-bold mb-2" style="font-size: 0.85rem; color: #6c757d;">İskontolar (%)
                                        </div>
                                        <div class="row g-1">
                                            <div class="col-4">
                                                <label class="form-label" style="font-size: 0.75rem;">Eczacı</label>
                                                <input type="number" step="0.01" class="form-control form-control-sm"
                                                    id="eczaci_kari" name="eczaci_kari" value="{{ old('eczaci_kari', 0) }}"
                                                    oninput="hesaplaDepocuFiyati()" min="0" max="100">
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" style="font-size: 0.75rem;">Kurum</label>
                                                <input type="number" step="0.01" class="form-control form-control-sm"
                                                    id="kurum_iskonto" name="kurum_iskonto"
                                                    value="{{ old('kurum_iskonto', 0) }}" oninput="hesaplaDepocuFiyati()"
                                                    min="0" max="100">
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" style="font-size: 0.75rem;">Ticari</label>
                                                <input type="number" step="0.01" class="form-control form-control-sm"
                                                    id="ticari_iskonto" name="ticari_iskonto"
                                                    value="{{ old('ticari_iskonto', 0) }}" oninput="hesaplaDepocuFiyati()"
                                                    min="0" max="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Depocu Fiyatı -->
                                <div class="col-md-2">
                                    <div class="border rounded p-2 h-100" style="background-color: #e3f2fd;">
                                        <label for="depocu_fiyati" class="form-label fw-bold mb-1"
                                            style="font-size: 0.85rem;">Depocu Fiyatı</label>
                                        <input type="number" step="0.01" class="form-control" id="depocu_fiyati"
                                            name="depocu_fiyati" value="{{ old('depocu_fiyati') }}"
                                            oninput="hesaplaNetFiyat()">
                                    </div>
                                </div>

                                <!-- Mal Fazlası Opsiyonları -->
                                <div class="col-md-5">
                                    <div class="border rounded p-2 h-100" style="background-color: #fff8e1;">
                                        <div class="fw-bold mb-2" style="font-size: 0.85rem; color: #6c757d;">Mal Fazlası
                                            Opsiyonları</div>
                                        <div class="row g-2">
                                            <!-- Opsiyon 1 -->
                                            <div class="col-6">
                                                <label class="form-label" style="font-size: 0.75rem;">Opsiyon 1 (örn:
                                                    10+1)</label>
                                                <input type="text" class="form-control form-control-sm" id="mf1" name="mf1"
                                                    value="{{ old('mf1') }}" oninput="hesaplaNetFiyat1()"
                                                    placeholder="10+1">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label" style="font-size: 0.75rem;">Net Fiyat 1</label>
                                                <input type="number" step="0.01" class="form-control form-control-sm"
                                                    id="net_fiyat1" name="net_fiyat1" value="{{ old('net_fiyat1') }}">
                                            </div>
                                            <!-- Opsiyon 2 -->
                                            <div class="col-6">
                                                <label class="form-label" style="font-size: 0.75rem;">Opsiyon 2 (örn:
                                                    15+5)</label>
                                                <input type="text" class="form-control form-control-sm" id="mf2" name="mf2"
                                                    value="{{ old('mf2') }}" oninput="hesaplaNetFiyat2()"
                                                    placeholder="15+5">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label" style="font-size: 0.75rem;">Net Fiyat 2</label>
                                                <input type="number" step="0.01" class="form-control form-control-sm"
                                                    id="net_fiyat2" name="net_fiyat2" value="{{ old('net_fiyat2') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- KDV & Stok -->
                                <div class="col-md-1">
                                    <div class="border rounded p-2 h-100">
                                        <label for="kdv_orani" class="form-label fw-bold mb-1"
                                            style="font-size: 0.75rem;">KDV %</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm" id="kdv_orani"
                                            name="kdv_orani" value="{{ old('kdv_orani', 10) }}" required>
                                        <label for="bakiye" class="form-label fw-bold mb-1 mt-2"
                                            style="font-size: 0.75rem;">Stok</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm" id="bakiye"
                                            name="bakiye" value="{{ old('bakiye', 0) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Resim -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="urun_resmi" class="form-label">Ürün Resmi</label>
                                    <input type="file"
                                        class="form-control form-control-sm @error('urun_resmi') is-invalid @enderror"
                                        id="urun_resmi" name="urun_resmi" accept="image/*">
                                    <small class="text-muted">Max 4MB</small>
                                    @error('urun_resmi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Checkboxlar -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="ozel_liste" name="ozel_liste"
                                            value="1" {{ old('ozel_liste', false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ozel_liste">
                                            <i class="fas fa-star text-warning"></i> Özel Liste
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> İptal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function hesaplaDepocuFiyati() {
            const psf = parseFloat(document.getElementById('satis_fiyati').value) || 0;
            const kurumIskonto = parseFloat(document.getElementById('kurum_iskonto').value) || 0;
            const eczaciKari = parseFloat(document.getElementById('eczaci_kari').value) || 0;
            const ticariIskonto = parseFloat(document.getElementById('ticari_iskonto').value) || 0;

            // Hesaplama: PSF - Kurum İsk -> Eczacı Karı -> Ticari İsk
            let fiyat = psf;

            // Kurum iskontosu düş
            fiyat = fiyat - (fiyat * kurumIskonto / 100);

            // Eczacı karı düş
            fiyat = fiyat - (fiyat * eczaciKari / 100);

            // Ticari iskonto düş
            fiyat = fiyat - (fiyat * ticariIskonto / 100);

            // Depocu fiyatına yaz
            document.getElementById('depocu_fiyati').value = fiyat.toFixed(2);

            // Net fiyatları da hesapla
            hesaplaNetFiyat1();
            hesaplaNetFiyat2();
        }

        function hesaplaNetFiyat1() {
            const depocuFiyati = parseFloat(document.getElementById('depocu_fiyati').value) || 0;
            const mf1 = (document.getElementById('mf1').value || '').trim();

            let netFiyat = depocuFiyati;

            // MF'yi parse et (örn: 10+1)
            if (mf1 && mf1.includes('+')) {
                const parts = mf1.split('+');
                if (parts.length === 2) {
                    const miktar = parseFloat(parts[0]) || 0;
                    const malFazlasi = parseFloat(parts[1]) || 0;
                    const toplam = miktar + malFazlasi;

                    if (miktar > 0 && toplam > 0) {
                        netFiyat = (depocuFiyati * miktar) / toplam;
                    }
                }
            }

            document.getElementById('net_fiyat1').value = netFiyat.toFixed(2);
        }

        function hesaplaNetFiyat2() {
            const depocuFiyati = parseFloat(document.getElementById('depocu_fiyati').value) || 0;
            const mf2 = (document.getElementById('mf2').value || '').trim();

            let netFiyat = depocuFiyati;

            // MF'yi parse et (örn: 15+5)
            if (mf2 && mf2.includes('+')) {
                const parts = mf2.split('+');
                if (parts.length === 2) {
                    const miktar = parseFloat(parts[0]) || 0;
                    const malFazlasi = parseFloat(parts[1]) || 0;
                    const toplam = miktar + malFazlasi;

                    if (miktar > 0 && toplam > 0) {
                        netFiyat = (depocuFiyati * miktar) / toplam;
                    }
                }
            }

            document.getElementById('net_fiyat2').value = netFiyat.toFixed(2);
        }

        // Sayfa yüklendiginde hesapla
        document.addEventListener('DOMContentLoaded', function () {
            hesaplaDepocuFiyati();
        });
    </script>
@endpush