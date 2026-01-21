@extends('layouts.app')

@section('title', 'Ürün Yönetimi - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
    <style>
        .edit-mode input,
        .edit-mode select {
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
        }

        /* Input içinde para ve yüzde işaretleri için */
        .edit-mode .input-with-suffix {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .edit-mode .input-with-suffix input {
            padding-right: 25px !important;
            width: 100%;
        }

        .edit-mode .input-with-suffix .suffix {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #6c757d;
            font-weight: 500;
            font-size: 0.85rem;
        }
    </style>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-box"></i> Ürün Yönetimi
            </h2>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Yeni Ürün Ekle
                </a>
            </div>
        </div>

        <!-- Arama ve Filtreleme -->
        <div class="card mb-3">
            <div class="card-body p-2">
                <form action="{{ route('admin.products.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-2">
                            <select name="search_type" class="form-select form-select-sm">
                                <option value="all" {{ request('search_type') == 'all' || !request('search_type') ? 'selected' : '' }}>Tümünde</option>
                                <option value="barkod" {{ request('search_type') == 'barkod' ? 'selected' : '' }}>Barkod
                                </option>
                                <option value="urun_kodu" {{ request('search_type') == 'urun_kodu' ? 'selected' : '' }}>Ürün
                                    Kodu</option>
                                <option value="urun_adi" {{ request('search_type') == 'urun_adi' ? 'selected' : '' }}>Ürün Adı
                                </option>
                                <option value="muadil_kodu" {{ request('search_type') == 'muadil_kodu' ? 'selected' : '' }}>
                                    Muadil Kodu</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Ara..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <select name="marka" class="form-select form-select-sm">
                                <option value="">Marka</option>
                                @foreach($markalar as $marka)
                                    <option value="{{ $marka }}" {{ request('marka') == $marka ? 'selected' : '' }}>{{ $marka }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select name="grup" class="form-select form-select-sm">
                                <option value="">Grup</option>
                                @foreach($gruplar as $grup)
                                    <option value="{{ $grup }}" {{ request('grup') == $grup ? 'selected' : '' }}>{{ $grup }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select name="is_active" class="form-select form-select-sm">
                                <option value="">Durum</option>
                                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Pasif</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select name="ozel_liste" class="form-select form-select-sm">
                                <option value="">Özel</option>
                                <option value="1" {{ request('ozel_liste') === '1' ? 'selected' : '' }}>Evet</option>
                                <option value="0" {{ request('ozel_liste') === '0' ? 'selected' : '' }}>Hayır</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="sort" class="form-select form-select-sm">
                                <option value="created_at_desc" {{ request('sort') == 'created_at_desc' || !request('sort') ? 'selected' : '' }}>Yeni→Eski</option>
                                <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>
                                    Eski→Yeni</option>
                                <option value="updated_at_desc" {{ request('sort') == 'updated_at_desc' ? 'selected' : '' }}>
                                    Güncelleme↓</option>
                                <option value="urun_kodu_asc" {{ request('sort') == 'urun_kodu_asc' ? 'selected' : '' }}>Kod
                                    A→Z</option>
                                <option value="urun_kodu_desc" {{ request('sort') == 'urun_kodu_desc' ? 'selected' : '' }}>Kod
                                    Z→A</option>
                                <option value="urun_adi_asc" {{ request('sort') == 'urun_adi_asc' ? 'selected' : '' }}>İsim
                                    A→Z</option>
                                <option value="urun_adi_desc" {{ request('sort') == 'urun_adi_desc' ? 'selected' : '' }}>İsim
                                    Z→A</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request()->hasAny(['search', 'marka', 'grup', 'is_active', 'ozel_liste']))
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm w-100 mt-1"
                                    style="padding: 0.15rem;">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($products->count() > 0)
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center align-middle" style="width: 40px;">
                                        <i class="fas fa-star" title="Özel Liste"></i>
                                    </th>
                                    <th class="text-center align-middle" style="width: 40px;">
                                        <i class="fas fa-toggle-on" title="Durum"></i>
                                    </th>
                                    <th class="text-center align-middle" style="width: 70px;">Resim</th>
                                    <th class="text-center align-middle" style="min-width: 220px;">
                                        <div>Ürün Kodu</div>
                                        <small class="text-muted">Ürün Adı</small>
                                    </th>
                                    <th class="text-center align-middle" style="width: 120px;">
                                        <div>Marka</div>
                                        <small class="text-muted">Grup</small>
                                    </th>
                                    <th class="text-center align-middle" style="width: 120px;">Muadil Kodu</th>
                                    <th class="text-center align-middle" style="width: 110px;">Perakende Fiyatı</th>
                                    <th class="text-center align-middle" style="width: 90px;">Eczacı Karı</th>
                                    <th class="text-center align-middle" style="width: 90px;">Kurum İsk.</th>
                                    <th class="text-center align-middle" style="width: 90px;">Ticari İsk.</th>
                                    <th class="text-center align-middle" style="width: 110px;">Depocu Fiyatı</th>
                                    <th class="text-center align-middle" style="width: 200px;">
                                        <div>Mal Fazlası 1 / 2</div>
                                        <small class="text-muted">Net Fiyat 1 / 2</small>
                                    </th>
                                    <th class="text-center align-middle" style="width: 100px;">
                                        <div>KDV</div>
                                        <small class="text-muted">Stok</small>
                                    </th>
                                    <th class="text-center align-middle" style="width: 120px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr id="product-row-{{ $product->id }}" data-product-id="{{ $product->id }}">
                                        <td class="text-center align-middle">
                                            <span class="view-mode">
                                                @if($product->ozel_liste)
                                                    <i class="fas fa-star text-warning" title="Özel Listede"></i>
                                                @else
                                                    <i class="far fa-star text-muted" title="Normal"></i>
                                                @endif
                                            </span>
                                            <span class="edit-mode d-none">
                                                <input type="checkbox" class="form-check-input" name="ozel_liste" {{ $product->ozel_liste ? 'checked' : '' }}>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="view-mode">
                                                @if($product->is_active)
                                                    <i class="fas fa-toggle-on text-primary" title="Aktif"
                                                        style="font-size: 1.3rem;"></i>
                                                @else
                                                    <i class="fas fa-toggle-off text-muted" title="Pasif"
                                                        style="font-size: 1.3rem;"></i>
                                                @endif
                                            </span>
                                            <span class="edit-mode d-none">
                                                <input type="checkbox" class="form-check-input" name="is_active" {{ $product->is_active ? 'checked' : '' }}>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($product->hasImage())
                                                <img src="{{ $product->image_url }}" class="img-thumbnail"
                                                    style="width: 50px; height: 50px; object-fit: cover;"
                                                    alt="{{ $product->urun_adi }}">
                                            @else
                                                <div class="bg-light text-center d-inline-block"
                                                    style="width: 50px; height: 50px; line-height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <span class="view-mode">
                                                <div><strong class="text-primary">{{ $product->urun_kodu }}</strong></div>
                                                <small class="text-muted">{{ $product->urun_adi }}</small>
                                            </span>
                                            <span class="edit-mode d-none">
                                                <div><strong class="text-primary">{{ $product->urun_kodu }}</strong></div>
                                                <input type="text" class="form-control form-control-sm mt-1" name="urun_adi"
                                                    value="{{ $product->urun_adi }}">
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="view-mode">
                                                <div><strong>{{ $product->marka ?? '-' }}</strong></div>
                                                <small class="text-muted">{{ $product->grup ?? '-' }}</small>
                                            </span>
                                            <span class="edit-mode d-none">
                                                <input type="text" class="form-control form-control-sm mb-1" name="marka"
                                                    value="{{ $product->marka }}" placeholder="Marka">
                                                <input type="text" class="form-control form-control-sm" name="grup"
                                                    value="{{ $product->grup }}" placeholder="Grup">
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="view-mode">
                                                {{ $product->muadil_kodu ?? '-' }}
                                            </span>
                                            <span class="edit-mode d-none">
                                                <input type="text" class="form-control form-control-sm" name="muadil_kodu"
                                                    value="{{ $product->muadil_kodu }}">
                                            </span>
                                        </td>
                                        <td class="text-end align-middle">
                                            <span class="view-mode">
                                                <strong
                                                    class="text-primary">{{ number_format($product->satis_fiyati, 2, ',', '.') }}
                                                    ₺</strong>
                                            </span>
                                            <span class="edit-mode d-none">
                                                <div class="input-with-suffix">
                                                    <input type="number" class="form-control form-control-sm text-end"
                                                        name="satis_fiyati" value="{{ $product->satis_fiyati }}" step="0.01">
                                                    <span class="suffix">₺</span>
                                                </div>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="view-mode">
                                                <span class="badge bg-purple text-white"
                                                    style="background-color: #6f42c1;">%{{ number_format($product->eczaci_kari ?? 0, 2) }}</span>
                                            </span>
                                            <span class="edit-mode d-none">
                                                <div class="input-with-suffix">
                                                    <input type="number" class="form-control form-control-sm text-end"
                                                        name="eczaci_kari" value="{{ $product->eczaci_kari }}" step="0.01">
                                                    <span class="suffix">%</span>
                                                </div>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="view-mode">
                                                <span
                                                    class="badge bg-warning text-dark">%{{ number_format($product->kurum_iskonto ?? 0, 2) }}</span>
                                            </span>
                                            <span class="edit-mode d-none">
                                                <div class="input-with-suffix">
                                                    <input type="number" class="form-control form-control-sm text-end"
                                                        name="kurum_iskonto" value="{{ $product->kurum_iskonto }}" step="0.01">
                                                    <span class="suffix">%</span>
                                                </div>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="view-mode">
                                                <span
                                                    class="badge bg-secondary">%{{ number_format($product->ticari_iskonto ?? 0, 2) }}</span>
                                            </span>
                                            <span class="edit-mode d-none">
                                                <div class="input-with-suffix">
                                                    <input type="number" class="form-control form-control-sm text-end"
                                                        name="ticari_iskonto" value="{{ $product->ticari_iskonto }}" step="0.01">
                                                    <span class="suffix">%</span>
                                                </div>
                                            </span>
                                        </td>
                                        <td class="text-end align-middle">
                                            <span class="view-mode">
                                                {{ $product->depocu_fiyati ? number_format($product->depocu_fiyati, 2, ',', '.') . ' ₺' : '-' }}
                                            </span>
                                            <span class="edit-mode d-none">
                                                <div class="input-with-suffix">
                                                    <input type="number" class="form-control form-control-sm text-end"
                                                        name="depocu_fiyati" value="{{ $product->depocu_fiyati }}" step="0.01">
                                                    <span class="suffix">₺</span>
                                                </div>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="view-mode">
                                                <div class="d-flex justify-content-center gap-1 mb-1">
                                                    @if($product->mf1)
                                                        <span class="badge bg-success" title="Opsiyon 1">{{ $product->mf1 }}</span>
                                                    @endif
                                                    @if($product->mf2)
                                                        <span class="badge bg-info" title="Opsiyon 2">{{ $product->mf2 }}</span>
                                                    @endif
                                                    @if(!$product->mf1 && !$product->mf2)
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex justify-content-center gap-1">
                                                    @if($product->net_fiyat1)
                                                        <small
                                                            class="text-success">{{ number_format($product->net_fiyat1, 2, ',', '.') }}₺</small>
                                                    @endif
                                                    @if($product->net_fiyat2)
                                                        <small
                                                            class="text-info">{{ number_format($product->net_fiyat2, 2, ',', '.') }}₺</small>
                                                    @endif
                                                    @if(!$product->net_fiyat1 && !$product->net_fiyat2)
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </span>
                                            <span class="edit-mode d-none">
                                                <div class="row g-1">
                                                    <div class="col-6">
                                                        <input type="text" class="form-control form-control-sm text-center"
                                                            name="mf1" value="{{ $product->mf1 }}" placeholder="MF1">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="text" class="form-control form-control-sm text-center"
                                                            name="mf2" value="{{ $product->mf2 }}" placeholder="MF2">
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-with-suffix">
                                                            <input type="number" class="form-control form-control-sm text-end"
                                                                name="net_fiyat1" value="{{ $product->net_fiyat1 }}" step="0.01"
                                                                placeholder="NF1">
                                                            <span class="suffix">₺</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-with-suffix">
                                                            <input type="number" class="form-control form-control-sm text-end"
                                                                name="net_fiyat2" value="{{ $product->net_fiyat2 }}" step="0.01"
                                                                placeholder="NF2">
                                                            <span class="suffix">₺</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="view-mode">
                                                <div class="mb-1">
                                                    <span class="badge bg-dark">%{{ number_format($product->kdv_orani, 0) }}</span>
                                                </div>
                                                <div>
                                                    @if($product->bakiye && $product->bakiye > 0)
                                                        <span class="badge bg-success">{{ number_format($product->bakiye, 0) }}</span>
                                                    @else
                                                        <span class="badge bg-danger">0</span>
                                                    @endif
                                                </div>
                                            </span>
                                            <span class="edit-mode d-none">
                                                <div class="input-with-suffix mb-1">
                                                    <input type="number" class="form-control form-control-sm text-end"
                                                        name="kdv_orani" value="{{ $product->kdv_orani }}" step="1">
                                                    <span class="suffix">%</span>
                                                </div>
                                                <input type="number" class="form-control form-control-sm text-end" name="bakiye"
                                                    value="{{ $product->bakiye }}" step="1">
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="view-mode">
                                                <button type="button" class="btn btn-sm btn-info btn-quick-edit mb-1"
                                                    title="Hızlı Değiştir">
                                                    <i class="fas fa-bolt"></i>
                                                </button>
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                    class="btn btn-sm btn-warning mb-1" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </span>
                                            <span class="edit-mode d-none">
                                                <button type="button" class="btn btn-sm btn-success btn-save-quick mb-1"
                                                    title="Kaydet" data-product-id="{{ $product->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-secondary btn-cancel-quick"
                                                    title="İptal">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Henüz ürün eklenmemiş.
                <a href="{{ route('admin.products.create') }}" class="alert-link">İlk ürünü ekleyin.</a>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Başarı mesajı - butonun üzerinde (yeşil)
            function showSuccessNotification(buttonElement, message) {
                const buttonRect = buttonElement.getBoundingClientRect();

                const notification = document.createElement('div');
                notification.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
                notification.style.position = 'fixed';
                notification.style.backgroundColor = '#28a745';
                notification.style.color = 'white';
                notification.style.padding = '12px 20px';
                notification.style.borderRadius = '8px';
                notification.style.fontSize = '0.9rem';
                notification.style.fontWeight = '500';
                notification.style.boxShadow = '0 4px 12px rgba(40, 167, 69, 0.5)';
                notification.style.zIndex = '99999';
                notification.style.maxWidth = '300px';
                notification.style.textAlign = 'center';
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s ease-out';

                document.body.appendChild(notification);

                const notificationWidth = notification.offsetWidth;
                const notificationHeight = notification.offsetHeight;
                notification.style.left = (buttonRect.left + buttonRect.width / 2 - notificationWidth / 2) + 'px';
                notification.style.top = (buttonRect.top - notificationHeight - 10) + 'px';

                setTimeout(function () { notification.style.opacity = '1'; }, 10);
                setTimeout(function () { notification.style.opacity = '0'; }, 2500);
                setTimeout(function () { notification.remove(); }, 3000);
            }

            // Uyarı mesajı - butonun üzerinde (kırmızı)
            function showWarningNotification(buttonElement, message) {
                const buttonRect = buttonElement.getBoundingClientRect();

                const notification = document.createElement('div');
                notification.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
                notification.style.position = 'fixed';
                notification.style.backgroundColor = '#dc3545';
                notification.style.color = 'white';
                notification.style.padding = '12px 20px';
                notification.style.borderRadius = '8px';
                notification.style.fontSize = '0.85rem';
                notification.style.fontWeight = '500';
                notification.style.boxShadow = '0 4px 12px rgba(220, 53, 69, 0.5)';
                notification.style.zIndex = '99999';
                notification.style.maxWidth = '320px';
                notification.style.textAlign = 'center';
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s ease-out';
                notification.style.lineHeight = '1.4';

                document.body.appendChild(notification);

                const notificationWidth = notification.offsetWidth;
                const notificationHeight = notification.offsetHeight;
                notification.style.left = (buttonRect.left + buttonRect.width / 2 - notificationWidth / 2) + 'px';
                notification.style.top = (buttonRect.top - notificationHeight - 10) + 'px';

                setTimeout(function () { notification.style.opacity = '1'; }, 10);
                setTimeout(function () { notification.style.opacity = '0'; }, 3000);
                setTimeout(function () { notification.remove(); }, 3500);
            }

            // Numeric inputları virgülden sonra 2 haneye sınırla
            function limitDecimalPlaces(input, decimalPlaces = 2) {
                let value = input.value;
                if (value === '') return;

                // Sayıya çevir
                let num = parseFloat(value);
                if (isNaN(num)) return;

                // Virgülden sonra kaç hane var kontrol et
                if (value.includes('.')) {
                    const parts = value.split('.');
                    if (parts[1] && parts[1].length > decimalPlaces) {
                        input.value = num.toFixed(decimalPlaces);
                    }
                }
            }

            // Depocu fiyatını hesapla (her satır için)
            function hesaplaDepocuFiyatiRow(row) {
                const psf = parseFloat(row.find('input[name="satis_fiyati"]').val()) || 0;
                const kurumIskonto = parseFloat(row.find('input[name="kurum_iskonto"]').val()) || 0;
                const eczaciKari = parseFloat(row.find('input[name="eczaci_kari"]').val()) || 0;
                const ticariIskonto = parseFloat(row.find('input[name="ticari_iskonto"]').val()) || 0;

                // Hesaplama: PSF - Kurum İsk -> Eczacı Karı -> Ticari İsk
                let fiyat = psf;

                // Kurum iskontosu düş
                fiyat = fiyat - (fiyat * kurumIskonto / 100);

                // Eczacı karı düş
                fiyat = fiyat - (fiyat * eczaciKari / 100);

                // Ticari iskonto düş
                fiyat = fiyat - (fiyat * ticariIskonto / 100);

                // Depocu fiyatına yaz
                row.find('input[name="depocu_fiyati"]').val(fiyat.toFixed(2));

                // Net fiyatları da hesapla
                hesaplaNetFiyat1Row(row);
                hesaplaNetFiyat2Row(row);
            }

            // Net fiyat 1'i hesapla (her satır için)
            function hesaplaNetFiyat1Row(row) {
                const depocuFiyati = parseFloat(row.find('input[name="depocu_fiyati"]').val()) || 0;
                const mf1 = (row.find('input[name="mf1"]').val() || '').trim();

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

                row.find('input[name="net_fiyat1"]').val(netFiyat.toFixed(2));
            }

            // Net fiyat 2'yi hesapla (her satır için)
            function hesaplaNetFiyat2Row(row) {
                const depocuFiyati = parseFloat(row.find('input[name="depocu_fiyati"]').val()) || 0;
                const mf2 = (row.find('input[name="mf2"]').val() || '').trim();

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

                row.find('input[name="net_fiyat2"]').val(netFiyat.toFixed(2));
            }

            $(document).ready(function () {
                // Hızlı düzenleme butonu - event delegation kullan
                $(document).on('click', '.btn-quick-edit', function () {
                    const row = $(this).closest('tr');
                    row.find('.view-mode').addClass('d-none');
                    row.find('.edit-mode').removeClass('d-none');

                    // İlk hesaplamayı yap
                    hesaplaDepocuFiyatiRow(row);
                });

                // İptal butonu - event delegation kullan
                $(document).on('click', '.btn-cancel-quick', function () {
                    const row = $(this).closest('tr');
                    row.find('.edit-mode').addClass('d-none');
                    row.find('.view-mode').removeClass('d-none');
                });

                // Otomatik hesaplama için event listener'lar
                $(document).on('input', 'input[name="satis_fiyati"], input[name="kurum_iskonto"], input[name="eczaci_kari"], input[name="ticari_iskonto"]', function () {
                    const row = $(this).closest('tr');
                    hesaplaDepocuFiyatiRow(row);
                });

                $(document).on('input', 'input[name="depocu_fiyati"], input[name="mf1"]', function () {
                    const row = $(this).closest('tr');
                    hesaplaNetFiyat1Row(row);
                });

                $(document).on('input', 'input[name="depocu_fiyati"], input[name="mf2"]', function () {
                    const row = $(this).closest('tr');
                    hesaplaNetFiyat2Row(row);
                });

                // Numeric inputlara virgülden sonra 2 hane sınırı ekle
                $(document).on('blur', '.edit-mode input[type="number"][step="0.01"]', function () {
                    limitDecimalPlaces(this, 2);
                });

                $(document).on('input', '.edit-mode input[type="number"][step="0.01"]', function () {
                    // Input sırasında da kontrol et
                    let value = this.value;
                    if (value.includes('.')) {
                        const parts = value.split('.');
                        if (parts[1] && parts[1].length > 2) {
                            // Otomatik düzelt
                            limitDecimalPlaces(this, 2);
                        }
                    }
                });

                // Kaydet butonu - event delegation kullan
                $(document).on('click', '.btn-save-quick', function () {
                    const btn = $(this);
                    const row = btn.closest('tr');

                    // Önce butondan, sonra row'dan productId al
                    let productId = btn.data('product-id');
                    if (!productId) {
                        productId = row.data('product-id');
                    }

                    if (!productId) {
                        showWarningNotification(btn[0], 'Ürün ID bulunamadı!');
                        return;
                    }

                    // Form verilerini topla
                    const data = {
                        _token: '{{ csrf_token() }}',
                        urun_adi: row.find('input[name="urun_adi"]').val(),
                        marka: row.find('input[name="marka"]').val(),
                        grup: row.find('input[name="grup"]').val(),
                        muadil_kodu: row.find('input[name="muadil_kodu"]').val(),
                        satis_fiyati: row.find('input[name="satis_fiyati"]').val(),
                        eczaci_kari: row.find('input[name="eczaci_kari"]').val(),
                        kurum_iskonto: row.find('input[name="kurum_iskonto"]').val(),
                        ticari_iskonto: row.find('input[name="ticari_iskonto"]').val(),
                        depocu_fiyati: row.find('input[name="depocu_fiyati"]').val(),
                        mf1: row.find('input[name="mf1"]').val(),
                        mf2: row.find('input[name="mf2"]').val(),
                        net_fiyat1: row.find('input[name="net_fiyat1"]').val(),
                        net_fiyat2: row.find('input[name="net_fiyat2"]').val(),
                        kdv_orani: row.find('input[name="kdv_orani"]').val(),
                        bakiye: row.find('input[name="bakiye"]').val(),
                        is_active: row.find('input[name="is_active"]').is(':checked') ? 1 : 0,
                        ozel_liste: row.find('input[name="ozel_liste"]').is(':checked') ? 1 : 0
                    };

                    // Butonu devre dışı bırak
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                    const url = '{{ url("admin/products") }}/' + productId + '/quick-update';

                    // AJAX isteği
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: data,
                        success: function (response) {
                            if (response.success) {
                                // Başarı mesajını butonun üzerinde göster
                                showSuccessNotification(btn[0], response.message || 'Ürün başarıyla güncellendi!');

                                // 2 saniye sonra sayfayı yenile
                                setTimeout(function () {
                                    location.reload();
                                }, 2000);
                            }
                        },
                        error: function (xhr) {
                            let errorMessage = 'Bilinmeyen hata';

                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                // Validation hataları
                                const errors = xhr.responseJSON.errors;
                                const errorList = [];
                                for (let field in errors) {
                                    errorList.push(errors[field].join(', '));
                                }
                                errorMessage = errorList.join(' | ');
                            } else if (xhr.responseJSON?.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 419) {
                                errorMessage = 'CSRF token hatası. Sayfayı yenileyin.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Sunucu hatası. Lütfen tekrar deneyin.';
                            } else if (xhr.status === 404) {
                                errorMessage = 'Ürün bulunamadı.';
                            }

                            // Hata mesajını butonun üzerinde göster
                            showWarningNotification(btn[0], errorMessage);
                            btn.prop('disabled', false).html('<i class="fas fa-check"></i>');
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection