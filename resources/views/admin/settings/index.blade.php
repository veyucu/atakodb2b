@extends('layouts.app')

@section('title', 'Site Ayarları')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <h2 class="mb-0">Site Ayarları</h2>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Site Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Site Bilgileri -->
                            <h6 class="text-primary mb-3"><i class="fas fa-globe me-2"></i>Site Bilgileri</h6>

                            <div class="mb-3">
                                <label for="site_name" class="form-label fw-bold">Site Adı</label>
                                <input type="text" class="form-control @error('site_name') is-invalid @enderror"
                                    id="site_name" name="site_name" value="{{ old('site_name', $settings->site_name) }}"
                                    placeholder="Site adını girin">
                                @error('site_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="site_logo" class="form-label fw-bold">Site Logosu</label>
                                @if($settings->logo_url)
                                    <div class="mb-2 d-flex align-items-center gap-3">
                                        <img src="{{ $settings->logo_url }}" alt="Site Logo" class="img-thumbnail"
                                            style="max-height: 100px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="if(confirm('Logoyu silmek istediğinize emin misiniz?')) { document.getElementById('delete-logo-form').submit(); }">
                                            <i class="fas fa-trash me-1"></i>Logoyu Sil
                                        </button>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('site_logo') is-invalid @enderror"
                                    id="site_logo" name="site_logo" accept="image/*">
                                <small class="text-muted">JPG, PNG, GIF, SVG formatlarında, maksimum 4MB</small>
                                @error('site_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <!-- Firma Bilgileri -->
                            <h6 class="text-primary mb-3"><i class="fas fa-building me-2"></i>Firma Bilgileri</h6>

                            <div class="mb-3">
                                <label for="company_name" class="form-label fw-bold">Firma Adı</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                    id="company_name" name="company_name"
                                    value="{{ old('company_name', $settings->company_name) }}"
                                    placeholder="Firma adını girin">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="company_address" class="form-label fw-bold">Adres</label>
                                <textarea class="form-control @error('company_address') is-invalid @enderror"
                                    id="company_address" name="company_address" rows="3"
                                    placeholder="Firma adresini girin">{{ old('company_address', $settings->company_address) }}</textarea>
                                @error('company_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="company_phone" class="form-label fw-bold">Telefon</label>
                                <input type="text" class="form-control @error('company_phone') is-invalid @enderror"
                                    id="company_phone" name="company_phone"
                                    value="{{ old('company_phone', $settings->company_phone) }}"
                                    placeholder="+90 (XXX) XXX XX XX">
                                @error('company_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="company_email" class="form-label fw-bold">E-posta</label>
                                <input type="email" class="form-control @error('company_email') is-invalid @enderror"
                                    id="company_email" name="company_email"
                                    value="{{ old('company_email', $settings->company_email) }}"
                                    placeholder="info@example.com">
                                @error('company_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <!-- Gönderim Şekilleri -->
                            <h6 class="text-primary mb-3"><i class="fas fa-shipping-fast me-2"></i>Gönderim Şekilleri</h6>

                            <div id="gonderim-sekilleri-container">
                                @php
                                    $gonderimSekilleri = $settings->gonderim_sekilleri ?? [];
                                @endphp
                                @forelse($gonderimSekilleri as $index => $gonderim)
                                    <div class="row mb-2 gonderim-row">
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm"
                                                name="gonderim_sekilleri[{{ $index }}][aciklama]"
                                                value="{{ $gonderim['aciklama'] ?? '' }}"
                                                placeholder="Açıklama (Müşteriye gösterilecek)">
                                        </div>
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm"
                                                name="gonderim_sekilleri[{{ $index }}][erp_aciklama]"
                                                value="{{ $gonderim['erp_aciklama'] ?? '' }}"
                                                placeholder="ERP Kodu (max 20 karakter)" maxlength="20">
                                        </div>
                                        <div class="col-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger w-100"
                                                onclick="removeGonderimRow(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="row mb-2 gonderim-row">
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm"
                                                name="gonderim_sekilleri[0][aciklama]"
                                                placeholder="Açıklama (Müşteriye gösterilecek)">
                                        </div>
                                        <div class="col-5">
                                            <input type="text" class="form-control form-control-sm"
                                                name="gonderim_sekilleri[0][erp_aciklama]"
                                                placeholder="ERP Kodu (max 20 karakter)" maxlength="20">
                                        </div>
                                        <div class="col-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger w-100"
                                                onclick="removeGonderimRow(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mb-3" onclick="addGonderimRow()">
                                <i class="fas fa-plus"></i> Gönderim Şekli Ekle
                            </button>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Ayarları Kaydet
                                </button>
                            </div>
                        </form>

                        <!-- Logo Silme Formu -->
                        <form id="delete-logo-form" action="{{ route('admin.settings.deleteLogo') }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Bilgilendirme</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-check text-success me-2"></i>Site adı ve logo tüm sayfalarda
                            görünür.</p>
                        <p class="mb-2"><i class="fas fa-check text-success me-2"></i>Firma bilgileri footer'da gösterilir.
                        </p>
                        <p class="mb-2"><i class="fas fa-check text-success me-2"></i>Logo için PNG veya SVG önerilir.</p>
                        <p class="mb-0"><i class="fas fa-check text-success me-2"></i>Tüm alanlar isteğe bağlıdır.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let gonderimRowIndex = {{ count($settings->gonderim_sekilleri ?? []) }};

        function addGonderimRow() {
            const container = document.getElementById('gonderim-sekilleri-container');
            const newRow = document.createElement('div');
            newRow.className = 'row mb-2 gonderim-row';
            newRow.innerHTML = `
                            <div class="col-5">
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       name="gonderim_sekilleri[${gonderimRowIndex}][aciklama]" 
                                       placeholder="Açıklama (Müşteriye gösterilecek)">
                            </div>
                            <div class="col-5">
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       name="gonderim_sekilleri[${gonderimRowIndex}][erp_aciklama]" 
                                       placeholder="ERP Kodu (max 20 karakter)"
                                       maxlength="20">
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeGonderimRow(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
            container.appendChild(newRow);
            gonderimRowIndex++;
        }

        function removeGonderimRow(button) {
            const row = button.closest('.gonderim-row');
            row.remove();
        }
    </script>
@endpush