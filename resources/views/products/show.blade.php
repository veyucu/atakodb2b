@extends('layouts.app')

@section('title', $product->urun_adi . ' - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@section('content')
<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Ana Sayfa</a></li>
            <li class="breadcrumb-item active">{{ $product->urun_adi }}</li>
        </ol>
    </nav>

    <!-- Product Detail - Modern Professional -->
    <div class="card border-0 shadow">
        <!-- Header -->
        <div class="card-header border-0" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding: 1rem;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="text-white mb-2 fw-bold">{{ $product->urun_adi }}</h3>
                    <div class="d-flex flex-wrap gap-3 text-white" style="font-size: 0.9rem; opacity: 0.95;">
                        <span><i class="fas fa-hashtag me-1"></i>{{ $product->urun_kodu }}</span>
                        @if($product->barkod)
                            <span><i class="fas fa-barcode me-1"></i>{{ $product->barkod }}</span>
                        @endif
                        @if($product->marka)
                            <span><i class="fas fa-tag me-1"></i>{{ $product->marka }}</span>
                        @endif
                        @if($product->grup)
                            <span><i class="fas fa-folder me-1"></i>{{ $product->grup }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    @if($product->bakiye > 0)
                        <span class="badge" style="background: #28a745; font-size: 0.85rem; padding: 0.5rem 1rem;">
                            <i class="fas fa-check-circle me-1"></i>STOKTA VAR
                        </span>
                    @else
                        <span class="badge" style="background: #dc3545; font-size: 0.85rem; padding: 0.5rem 1rem;">
                            <i class="fas fa-times-circle me-1"></i>STOK YOK
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-3">
            <div class="row">
                <!-- Image Section -->
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="border h-100 d-flex align-items-center justify-content-center" style="padding: 2rem; background: #f8f9fa; border-radius: 4px;">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" 
                                 class="img-fluid" 
                                 alt="{{ $product->urun_adi }}"
                                 style="max-height: 350px; object-fit: contain;">
                        @else
                            <div class="text-center p-4" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border: 3px dashed #cbd5e0; border-radius: 8px; min-height: 300px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <i class="fas fa-camera text-secondary mb-3" style="font-size: 5rem; opacity: 0.4;"></i>
                                <p class="text-secondary mb-0 fw-bold" style="font-size: 1.2rem;">Resim Hazırlanıyor</p>
                                <p class="text-muted mt-2 mb-0" style="font-size: 0.9rem;">Bu ürün için resim yüklenmemiş</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Info Section -->
                <div class="col-lg-8">
                    <!-- Price Table -->
                    <table class="table table-bordered mb-2">
                        <thead style="background: #e9ecef;">
                            <tr>
                                <th class="text-center fw-bold" style="width: 33%; padding: 0.6rem; font-size: 0.85rem; color: #495057;">Perakende Fiyatı</th>
                                @if($product->depocu_fiyati)
                                    <th class="text-center fw-bold" style="width: 33%; padding: 0.6rem; font-size: 0.85rem; color: #495057;">Depocu Fiyatı</th>
                                @endif
                                <th class="text-center fw-bold" style="width: 34%; padding: 0.6rem; font-size: 0.85rem; color: #495057;">KDV Oranı</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" style="padding: 0.75rem; background: #ffffff;"><h4 class="mb-0 fw-bold" style="color: #1e3c72; font-size: 1.3rem;">{{ number_format($product->satis_fiyati, 2, ',', '.') }} ₺</h4></td>
                                @if($product->depocu_fiyati)
                                    <td class="text-center" style="padding: 0.75rem; background: #ffffff;"><h4 class="mb-0 fw-bold" style="color: #1e3c72; font-size: 1.3rem;">{{ number_format($product->depocu_fiyati, 2, ',', '.') }} ₺</h4></td>
                                @endif
                                <td class="text-center" style="padding: 0.75rem; background: #ffffff;"><h4 class="mb-0 fw-bold" style="color: #1e3c72; font-size: 1.3rem;">%{{ number_format($product->kdv_orani, 0) }}</h4></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- İskonto Table -->
                    <table class="table table-bordered mb-2">
                        <thead style="background: #e9ecef;">
                            <tr>
                                <th class="text-center fw-bold" style="padding: 0.6rem; font-size: 0.85rem; color: #495057;">Eczacı Karı</th>
                                <th class="text-center fw-bold" style="padding: 0.6rem; font-size: 0.85rem; color: #495057;">Kurum İskontosu</th>
                                <th class="text-center fw-bold" style="padding: 0.6rem; font-size: 0.85rem; color: #495057;">Ticari İskonto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" style="padding: 0.75rem; background: #ffffff;"><h5 class="mb-0 fw-bold" style="font-size: 1.1rem;">%{{ number_format($product->eczaci_kari, 2, ',', '.') }}</h5></td>
                                <td class="text-center" style="padding: 0.75rem; background: #ffffff;"><h5 class="mb-0 fw-bold" style="font-size: 1.1rem;">%{{ number_format($product->kurum_iskonto, 2, ',', '.') }}</h5></td>
                                <td class="text-center" style="padding: 0.75rem; background: #ffffff;"><h5 class="mb-0 fw-bold" style="font-size: 1.1rem;">%{{ number_format($product->ticari_iskonto, 2, ',', '.') }}</h5></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Highlight Table -->
                    <table class="table table-bordered mb-0">
                        <thead style="background: #1e3c72; color: white;">
                            <tr>
                                @if($product->mf)
                                    <th class="text-center fw-bold" style="width: 30%; padding: 0.6rem; font-size: 0.85rem; letter-spacing: 0.5px;">MAL FAZLASI</th>
                                @endif
                                <th class="text-center fw-bold" style="width: {{ $product->mf ? '35%' : '50%' }}; padding: 0.6rem; font-size: 0.85rem; letter-spacing: 0.5px;">NET FİYAT (KDV DAHİL)</th>
                                <th class="text-center fw-bold" style="width: {{ $product->mf ? '35%' : '50%' }}; padding: 0.6rem; font-size: 0.85rem; letter-spacing: 0.5px;">MİKTAR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @if($product->mf)
                                    <td class="text-center align-middle" style="background: #fff9e6; padding: 0.9rem;"><h3 class="mb-0 fw-bold" style="color: #f59e0b; font-size: 1.5rem;">{{ $product->mf }}</h3></td>
                                @endif
                                <td class="text-center align-middle" style="background: #e8f5e9; padding: 0.9rem;">
                                    @php $netFiyat = $product->net_fiyat_manuel ?? $product->net_price; @endphp
                                    <h3 class="mb-0 fw-bold" style="color: #28a745; font-size: 1.5rem;">{{ number_format($netFiyat, 2, ',', '.') }} ₺</h3>
                                </td>
                                <td class="text-center align-middle" style="background: #f8f9fa; padding: 0.75rem;">
                                    <div class="d-flex gap-1 align-items-center justify-content-center">
                                        <div class="input-group" style="max-width: 250px;">
                                            <button class="btn btn-outline-dark" type="button" onclick="decreaseQty()" style="padding: 0.5rem 0.75rem;">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" 
                                                   id="product-quantity" 
                                                   class="form-control text-center fw-bold" 
                                                   value="0" 
                                                   min="0"
                                                   style="font-size: 1.3rem; background: white; max-width: 120px;">
                                            <button class="btn btn-outline-dark" type="button" onclick="increaseQty()" style="padding: 0.5rem 0.75rem;">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <button type="button" 
                                                class="btn btn-lg text-white" 
                                                id="add-to-cart-btn"
                                                onclick="addToCart()"
                                                title="Sepete Ekle"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                style="background: #1e3c72; border: none; padding: 0.6rem 1rem;">
                                            <i class="fas fa-cart-plus" style="font-size: 1.3rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Muadil Products Section -->
    @if($muadilProducts->count() > 0)
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    Muadil Ürünler <span class="text-muted">({{ $muadilProducts->count() }})</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 list-view-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 100px;">Ürün Kodu</th>
                                <th>Ürün Adı</th>
                                <th class="text-center" style="width: 90px;">Perakende<br>Fiyatı</th>
                                <th class="text-center" style="width: 90px;">Depocu<br>Fiyatı</th>
                                <th class="text-center" style="width: 80px;">Mal<br>Fazlası</th>
                                <th class="text-center" style="width: 90px;">KDV Dahil<br>Net Fiyat</th>
                                <th class="text-center" style="width: 140px;">Miktar</th>
                                <th class="text-center" style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($muadilProducts as $muadil)
                                <tr>
                                    <td>
                                        <a href="{{ route('product.show', $muadil->id) }}" class="text-decoration-none">
                                            {{ $muadil->urun_kodu }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($muadil->bakiye > 0)
                                            <span class="badge me-1" 
                                                  title="Stokta Var"
                                                  style="background: #10b981; font-size: 0.6rem; padding: 0.2rem 0.35rem;">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        @else
                                            <span class="badge me-1" 
                                                  title="Stokta Yok"
                                                  style="background: #ef4444; font-size: 0.6rem; padding: 0.2rem 0.35rem;">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        @endif
                                        <a href="{{ route('product.show', $muadil->id) }}" class="text-decoration-none">
                                            @if($muadil->hasImage())
                                                <span class="product-name-with-image"
                                                      onmouseenter="showImagePreview(event, '{{ $muadil->image_url }}')"
                                                      onmouseleave="hideImagePreview()"
                                                      style="cursor: pointer;">
                                                    {{ $muadil->urun_adi }}
                                                </span>
                                            @else
                                                {{ $muadil->urun_adi }}
                                            @endif
                                        </a>
                                    </td>
                                    <td class="text-end">{{ number_format($muadil->satis_fiyati, 2, ',', '.') }} ₺</td>
                                    <td class="text-end">{{ $muadil->depocu_fiyati ? number_format($muadil->depocu_fiyati, 2, ',', '.') . ' ₺' : '-' }}</td>
                                    <td class="text-end">{{ $muadil->mf ?? '-' }}</td>
                                    <td class="text-end">
                                        @php $muadilNetFiyat = $muadil->net_fiyat_manuel ?? $muadil->net_price; @endphp
                                        <strong>{{ number_format($muadilNetFiyat, 2, ',', '.') }} ₺</strong>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" onclick="decreaseMuadilQty({{ $muadil->id }})">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" 
                                                   id="qty-muadil-{{ $muadil->id }}" 
                                                   class="form-control text-center" 
                                                   value="0" 
                                                   min="0">
                                            <button type="button" class="btn btn-outline-secondary" onclick="increaseMuadilQty({{ $muadil->id }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-primary btn-sm" 
                                                id="add-btn-muadil-{{ $muadil->id }}"
                                                onclick="addMuadilToCart({{ $muadil->id }})"
                                                title="Sepete Ekle"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Image Preview (for muadil products) -->
<div id="image-preview" style="display: none; position: fixed; z-index: 9999; background: white; border: 1px solid #ddd; padding: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
    <img id="preview-image" src="" alt="Ürün Resmi" style="max-width: 300px; max-height: 300px; object-fit: contain;">
</div>

@endsection

@push('scripts')
<script>
    // Miktar artırma/azaltma
    function increaseQty() {
        const input = document.getElementById('product-quantity');
        input.value = parseInt(input.value || 0) + 1;
    }

    function decreaseQty() {
        const input = document.getElementById('product-quantity');
        const currentValue = parseInt(input.value || 0);
        if (currentValue > 0) {
            input.value = currentValue - 1;
        }
    }

    // Muadil ürün miktar artırma/azaltma
    function increaseMuadilQty(productId) {
        const input = document.getElementById('qty-muadil-' + productId);
        input.value = parseInt(input.value || 0) + 1;
    }

    function decreaseMuadilQty(productId) {
        const input = document.getElementById('qty-muadil-' + productId);
        const currentValue = parseInt(input.value || 0);
        if (currentValue > 0) {
            input.value = currentValue - 1;
        }
    }

    // Sepete ekleme
    function addToCart() {
        const quantity = parseInt(document.getElementById('product-quantity').value) || 0;
        const button = document.getElementById('add-to-cart-btn');
        
        if (quantity === 0) {
            showWarningNotification(button, 'Lütfen miktar girin!');
            return;
        }
        
        addToCartAjax({{ $product->id }}, quantity, button);
    }

    // Muadil ürün sepete ekleme
    function addMuadilToCart(productId) {
        const quantity = parseInt(document.getElementById('qty-muadil-' + productId).value) || 0;
        const button = document.getElementById('add-btn-muadil-' + productId);
        
        if (quantity === 0) {
            showWarningNotification(button, 'Lütfen miktar girin!');
            return;
        }
        
        addToCartAjax(productId, quantity, button);
    }

    // AJAX Sepete Ekleme
    function addToCartAjax(productId, quantity, buttonElement) {
        $.ajax({
            url: '{{ route("cart.add") }}',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                product_id: productId,
                quantity: quantity,
                mal_fazlasi: 0,
                product_campaign_id: null
            },
            success: function(response) {
                if (response.success) {
                    $('#cart-count').text(response.cart_count);
                    showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                    
                    // Input'ları sıfırla
                    const mainInput = document.getElementById('product-quantity');
                    const muadilInput = document.getElementById('qty-muadil-' + productId);
                    
                    if (mainInput && productId == {{ $product->id }}) {
                        mainInput.value = 0;
                    }
                    if (muadilInput) {
                        muadilInput.value = 0;
                    }
                }
            },
            error: function(xhr) {
                console.error('Add to cart error:', xhr);
                let message = 'Sepete eklenirken hata oluştu';
                
                if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                if (buttonElement) {
                    showWarningNotification(buttonElement, message);
                }
            }
        });
    }

    // Uyarı mesajı göster
    function showWarningNotification(buttonElement, message) {
        const buttonRect = buttonElement.getBoundingClientRect();
        
        const notification = document.createElement('div');
        notification.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
        notification.style.position = 'fixed';
        notification.style.backgroundColor = '#dc3545';
        notification.style.color = 'white';
        notification.style.padding = '10px 15px';
        notification.style.borderRadius = '8px';
        notification.style.fontSize = '0.85rem';
        notification.style.fontWeight = '500';
        notification.style.boxShadow = '0 4px 12px rgba(220, 53, 69, 0.5)';
        notification.style.zIndex = '9999';
        notification.style.maxWidth = '280px';
        notification.style.textAlign = 'center';
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease-out';
        
        document.body.appendChild(notification);
        
        const notificationWidth = notification.offsetWidth;
        const notificationHeight = notification.offsetHeight;
        notification.style.left = (buttonRect.left + buttonRect.width / 2 - notificationWidth / 2) + 'px';
        notification.style.top = (buttonRect.top - notificationHeight - 10) + 'px';
        
        setTimeout(function() {
            notification.style.opacity = '1';
        }, 10);
        
        setTimeout(function() {
            notification.style.opacity = '0';
        }, 2000);
        
        setTimeout(function() {
            notification.remove();
        }, 2500);
    }

    // Flying notification
    function showFlyingNotification(buttonElement, message) {
        const buttonRect = buttonElement.getBoundingClientRect();
        
        const notification = document.createElement('div');
        notification.className = 'flying-notification';
        notification.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        notification.style.position = 'fixed';
        notification.style.background = '#28a745';
        notification.style.color = 'white';
        notification.style.padding = '10px 20px';
        notification.style.borderRadius = '25px';
        notification.style.boxShadow = '0 4px 12px rgba(40, 167, 69, 0.4)';
        notification.style.zIndex = '9999';
        notification.style.opacity = '1';
        notification.style.fontSize = '1rem';
        notification.style.display = 'flex';
        notification.style.alignItems = 'center';
        notification.style.gap = '8px';
        notification.style.transition = 'opacity 0.5s ease-out';
        notification.style.left = (buttonRect.left + buttonRect.width / 2 - 75) + 'px';
        notification.style.top = (buttonRect.top - 40) + 'px';
        
        document.body.appendChild(notification);

        // 1.5 saniye bekle, sonra fade out yap
        setTimeout(() => {
            notification.style.opacity = '0';
        }, 1500);

        // Mesajı kaldır (toplam 2 saniye)
        setTimeout(() => {
            notification.remove();
        }, 2000);
    }

    // Image preview for muadil products
    function showImagePreview(event, imageUrl) {
        const preview = document.getElementById('image-preview');
        const previewImage = document.getElementById('preview-image');
        
        previewImage.src = imageUrl;
        preview.style.display = 'block';
        
        // Mouse'un hemen yanında göster (fixed pozisyon - viewport bazlı)
        preview.style.left = (event.clientX + 20) + 'px';
        preview.style.top = (event.clientY - 50) + 'px';
    }

    function hideImagePreview() {
        document.getElementById('image-preview').style.display = 'none';
    }

    // Tooltip'leri başlat
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Product Detail Styles */
    .product-name-with-image {
        cursor: pointer;
        text-decoration: underline;
        text-decoration-style: dotted;
    }

    .list-view-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    [data-theme="dark"] .list-view-table tbody tr:hover {
        background-color: #343a40;
    }

    /* Dark theme - table backgrounds */
    [data-theme="dark"] .table tbody tr[style*="background: #fff3cd"] {
        background: #5a4a1f !important;
    }
    
    [data-theme="dark"] .table tbody tr[style*="background: #d1e7dd"] {
        background: #1e4a3a !important;
    }
</style>
@endpush

