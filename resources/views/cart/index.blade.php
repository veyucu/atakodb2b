@extends('layouts.app')

@section('title', 'Sepetim - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@push('head')
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
@endpush

@section('content')
    <div class="container">
        <h2 class="mb-4">
            <i class="fas fa-shopping-cart"></i> Sepetim
        </h2>

        @if($cartItems->count() > 0)
            <div class="row">
                <div class="col-lg-9 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive cart-table-wrapper">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="hide-on-mobile" style="width: 80px;">Resim</th>
                                            <th class="product-name-cell">Ürün</th>
                                            <th class="text-center hide-on-mobile desktop-only-cell" style="width: 100px;">Mal
                                                Fazlası</th>
                                            <th class="text-end hide-on-mobile desktop-only-cell" style="width: 120px;">Net
                                                Fiyat</th>
                                            <th class="text-center mobile-combined-price-cell" style="display: none;">
                                                <span class="show-on-mobile">MF / Fiyat</span>
                                            </th>
                                            <th class="text-center hide-on-mobile desktop-only-cell" style="width: 150px;">
                                                Miktar</th>
                                            <th class="text-end hide-on-mobile desktop-only-cell" style="width: 120px;">Toplam
                                            </th>
                                            <th class="hide-on-mobile desktop-only-cell" style="width: 50px;"></th>
                                            <th class="text-center mobile-qty-delete-cell" style="display: none;">
                                                <span class="show-on-mobile">Miktar / Sil</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cartItems as $item)
                                            @php
                                                // mf2'den step hesapla (base + bonus toplamı)
                                                $mf2Step = 0;
                                                if ($item->product->mf2 && str_contains($item->product->mf2, '+')) {
                                                    $partsCart = explode('+', $item->product->mf2);
                                                    $mf2Step = (int) trim($partsCart[0]) + (int) trim($partsCart[1]);
                                                }

                                                // Miktara göre otomatik MF seçimi:
                                                // Eğer hem mf1 hem mf2 varsa ve miktar >= mf2Step ise MF2 seç, değilse MF1
                                                if ($item->product->mf1 && $item->product->mf2 && $mf2Step > 0) {
                                                    $selectedOption = ($item->quantity >= $mf2Step) ? 2 : 1;
                                                } else {
                                                    $selectedOption = $item->bonus_option ?? 1;
                                                }

                                                // Seçili bonus opsiyonuna göre net fiyatı belirle
                                                if ($selectedOption == 2 && $item->product->net_fiyat2) {
                                                    $netFiyat = $item->product->net_fiyat2;
                                                } elseif ($item->product->net_fiyat1) {
                                                    $netFiyat = $item->product->net_fiyat1;
                                                } else {
                                                    $netFiyat = $item->product->net_price ?? $item->price;
                                                }
                                                $toplam = $netFiyat * $item->quantity;
                                            @endphp
                                            <tr id="cart-row-{{ $item->id }}">
                                                <!-- Image - Hidden on mobile -->
                                                <td class="hide-on-mobile">
                                                    <a href="javascript:void(0)"
                                                        onclick="showProductModal({{ $item->product->id }})"
                                                        style="cursor: pointer;">
                                                        @if($item->product->image_url)
                                                            <img src="{{ $item->product->image_url }}" class="img-thumbnail"
                                                                style="width: 60px; height: 60px; object-fit: cover;"
                                                                alt="{{ $item->product->urun_adi }}">
                                                        @else
                                                            <div class="img-thumbnail d-flex align-items-center justify-content-center"
                                                                style="width: 60px; height: 60px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border: 1px dashed #cbd5e0;">
                                                                <i class="fas fa-camera text-secondary"
                                                                    style="font-size: 1.5rem; opacity: 0.4;"></i>
                                                            </div>
                                                        @endif
                                                    </a>
                                                </td>

                                                <!-- Product Name -->
                                                <td class="product-name-cell">
                                                    <a href="javascript:void(0)"
                                                        onclick="showProductModal({{ $item->product->id }})"
                                                        class="text-decoration-none">
                                                        <strong class="text-dark">{{ $item->product->urun_adi }}</strong>
                                                    </a>
                                                    <br class="hide-on-mobile">
                                                    <small class="text-muted hide-on-mobile">Kod:
                                                        {{ $item->product->urun_kodu }}</small>
                                                </td>

                                                <!-- Mal Fazlası - Desktop Only -->
                                                <td class="text-center hide-on-mobile desktop-only-cell">
                                                    @if($item->product->mf1 && $item->product->mf2)
                                                        {{-- Hem MF1 hem MF2 var - Radio butonları göster --}}
                                                        <div style="font-size: 0.85rem;">
                                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                                <input class="form-check-input me-1" type="radio"
                                                                    name="cart_bonus_option_{{ $item->id }}"
                                                                    id="cart_bonus_{{ $item->id }}_1" value="1" {{ $selectedOption == 1 ? 'checked' : '' }} data-cart-id="{{ $item->id }}" data-min-qty="0"
                                                                    onchange="onCartBonusOption1Selected({{ $item->id }})"
                                                                    style="margin: 0;">
                                                                <label for="cart_bonus_{{ $item->id }}_1" style="cursor: pointer;">
                                                                    <span class="badge bg-success"
                                                                        style="font-size: 0.8rem;">{{ $item->product->mf1 }}</span>
                                                                </label>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <input class="form-check-input me-1" type="radio"
                                                                    name="cart_bonus_option_{{ $item->id }}"
                                                                    id="cart_bonus_{{ $item->id }}_2" value="2" {{ $selectedOption == 2 ? 'checked' : '' }} data-cart-id="{{ $item->id }}"
                                                                    data-min-qty="{{ $mf2Step }}"
                                                                    onchange="onCartBonusOption2Selected({{ $item->id }}, {{ $mf2Step }})"
                                                                    style="margin: 0;">
                                                                <label for="cart_bonus_{{ $item->id }}_2" style="cursor: pointer;">
                                                                    <span class="badge bg-primary"
                                                                        style="font-size: 0.8rem;">{{ $item->product->mf2 }}</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @elseif($item->product->mf1)
                                                        {{-- Sadece MF1 var - Badge göster --}}
                                                        <span class="badge bg-success"
                                                            style="font-size: 0.8rem;">{{ $item->product->mf1 }}</span>
                                                    @elseif($item->product->mf2)
                                                        {{-- Sadece MF2 var - Badge göster --}}
                                                        <span class="badge bg-primary"
                                                            style="font-size: 0.8rem;">{{ $item->product->mf2 }}</span>
                                                    @elseif($item->mal_fazlasi > 0)
                                                        <span class="badge bg-success" style="font-size: 0.9rem;">
                                                            {{ $item->quantity }}+{{ $item->mal_fazlasi }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>

                                                <!-- Net Fiyat - Desktop Only -->
                                                <td class="text-end hide-on-mobile desktop-only-cell">
                                                    @if($item->product->mf1 || $item->product->mf2)
                                                        <div style="font-size: 0.85rem;">
                                                            @if($item->product->mf1)
                                                                <div class="mb-1">
                                                                    <strong
                                                                        class="text-success">{{ $item->product->net_fiyat1 ? number_format($item->product->net_fiyat1, 2, ',', '.') . ' ₺' : '-' }}</strong>
                                                                </div>
                                                            @endif
                                                            @if($item->product->mf2)
                                                                <div>
                                                                    <strong
                                                                        class="text-primary">{{ $item->product->net_fiyat2 ? number_format($item->product->net_fiyat2, 2, ',', '.') . ' ₺' : '-' }}</strong>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <strong>{{ number_format($netFiyat, 2, ',', '.') }} ₺</strong>
                                                    @endif
                                                </td>

                                                <!-- Combined MF + Net Fiyat - Mobile Only -->
                                                <td class="text-center mobile-combined-price-cell" style="display: none;">
                                                    <div class="mf-section">
                                                        @if($item->product->mf1)
                                                            <div style="font-size: 0.65rem; margin-bottom: 2px;">
                                                                <span class="badge bg-success"
                                                                    style="font-size: 0.6rem; padding: 0.1rem 0.2rem;">{{ $item->product->mf1 }}</span>
                                                            </div>
                                                        @endif
                                                        @if($item->product->mf2)
                                                            <div style="font-size: 0.65rem;">
                                                                <span class="badge bg-primary"
                                                                    style="font-size: 0.6rem; padding: 0.1rem 0.2rem;">{{ $item->product->mf2 }}</span>
                                                            </div>
                                                        @endif
                                                        @if(!$item->product->mf1 && !$item->product->mf2 && $item->mal_fazlasi > 0)
                                                            <span class="badge bg-success"
                                                                style="font-size: 0.65rem; padding: 0.15rem 0.3rem;">
                                                                {{ $item->quantity }}+{{ $item->mal_fazlasi }}
                                                            </span>
                                                        @elseif(!$item->product->mf1 && !$item->product->mf2)
                                                            <span class="text-muted" style="font-size: 0.7rem;">-</span>
                                                        @endif
                                                    </div>
                                                    <div class="price-section">
                                                        {{ number_format($netFiyat, 2, ',', '.') }} ₺
                                                    </div>
                                                </td>

                                                <!-- Miktar - Desktop Only -->
                                                <td class="hide-on-mobile desktop-only-cell">
                                                    @php
                                                        // Başlangıçta hatalı miktar kontrolü
                                                        $isMf2Error = false;
                                                        $tooltipText = '';
                                                        if ($item->product->mf2bolunemez && $mf2Step > 0 && $selectedOption == 2) {
                                                            if ($item->quantity % $mf2Step !== 0) {
                                                                $isMf2Error = true;
                                                            }
                                                            $ornekler = [];
                                                            for ($i = 1; $i <= 5; $i++) {
                                                                $ornekler[] = $mf2Step * $i;
                                                            }
                                                            $tooltipText = $mf2Step . ' ve katlarında sipariş verebilirsiniz. Örnek: ' . implode(', ', $ornekler) . '...';
                                                        }
                                                    @endphp
                                                    <div class="input-group input-group-sm" id="cart-qty-group-{{ $item->id }}">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="decreaseCartQuantity({{ $item->id }})"
                                                            style="min-width: 32px;">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number"
                                                            class="form-control text-center {{ $isMf2Error ? 'mf2-error' : '' }}"
                                                            id="cart-qty-{{ $item->id }}" value="{{ $item->quantity }}"
                                                            data-old-value="{{ $item->quantity }}" min="1"
                                                            data-mf2bolunemez="{{ $item->product->mf2bolunemez ? '1' : '0' }}"
                                                            data-mf2-step="{{ $mf2Step }}" data-tooltip="{{ $tooltipText }}"
                                                            style="min-width: 70px; flex: 1;"
                                                            onchange="updateCartQuantity({{ $item->id }}, this.value)"
                                                            onblur="roundMf2QuantityCart({{ $item->id }})"
                                                            onfocus="showMf2Tooltip({{ $item->id }})"
                                                            onmouseout="hideMf2Tooltip({{ $item->id }})">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="increaseCartQuantity({{ $item->id }})"
                                                            style="min-width: 32px;">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div id="mf2-tooltip-{{ $item->id }}" class="mf2-tooltip"
                                                        style="display: none;"></div>
                                                </td>

                                                <!-- Toplam - Desktop Only -->
                                                <td class="text-end hide-on-mobile desktop-only-cell">
                                                    <strong id="cart-total-{{ $item->id }}">
                                                        {{ number_format($toplam, 2, ',', '.') }} ₺
                                                    </strong>
                                                </td>

                                                <!-- Sil Butonu - Desktop Only -->
                                                <td class="text-center hide-on-mobile desktop-only-cell">
                                                    <button class="btn btn-danger btn-sm" onclick="removeFromCart({{ $item->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>

                                                <!-- Miktar + Sil Butonu - Mobile Only -->
                                                <td class="text-center mobile-qty-delete-cell" style="display: none;">
                                                    <div class="input-group input-group-sm"
                                                        id="cart-qty-group-mobile-{{ $item->id }}">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="decreaseCartQuantity({{ $item->id }})"
                                                            style="min-width: 28px;">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number"
                                                            class="form-control text-center {{ $isMf2Error ? 'mf2-error' : '' }}"
                                                            id="cart-qty-mobile-{{ $item->id }}" value="{{ $item->quantity }}"
                                                            data-old-value="{{ $item->quantity }}" min="1"
                                                            data-mf2bolunemez="{{ $item->product->mf2bolunemez ? '1' : '0' }}"
                                                            data-mf2-step="{{ $mf2Step }}" data-tooltip="{{ $tooltipText }}"
                                                            style="min-width: 45px; flex: 1;"
                                                            onchange="updateCartQuantity({{ $item->id }}, this.value)"
                                                            onblur="roundMf2QuantityCart({{ $item->id }})"
                                                            onfocus="showMf2Tooltip({{ $item->id }}, true)"
                                                            onmouseout="hideMf2Tooltip({{ $item->id }}, true)">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="increaseCartQuantity({{ $item->id }})"
                                                            style="min-width: 28px;">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div id="mf2-tooltip-mobile-{{ $item->id }}" class="mf2-tooltip"
                                                        style="display: none;"></div>
                                                    <button class="btn btn-danger btn-sm" onclick="removeFromCart({{ $item->id }})"
                                                        style="margin-top: 0.2rem;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Sepet Özeti</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ara Toplam:</span>
                                <strong id="subtotal">{{ number_format($totalWithoutVat, 2, ',', '.') }} ₺</strong>
                            </div>

                            @foreach($vatByRate as $rate => $amount)
                                <div class="d-flex justify-content-between mb-2" id="vat-rate-{{ $rate }}">
                                    <span>KDV %{{ number_format($rate, 0) }}:</span>
                                    <strong>{{ number_format($amount, 2, ',', '.') }} ₺</strong>
                                </div>
                            @endforeach

                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="h5">Genel Toplam:</span>
                                <strong class="h5 text-primary" id="grand-total">{{ number_format($totalWithVat, 2, ',', '.') }}
                                    ₺</strong>
                            </div>
                            <form id="checkoutForm" action="{{ route('cart.checkout') }}" method="POST"
                                onsubmit="return validateCheckout()">
                                @csrf
                                @php
                                    $gonderimSekilleri = $siteSettings->gonderim_sekilleri ?? [];
                                    // Debug
                                    \Log::info('Gonderim Sekilleri: ' . json_encode($gonderimSekilleri));
                                @endphp
                                {{-- Debug: {{ json_encode($gonderimSekilleri) }} --}}
                                @if(is_array($gonderimSekilleri) && count($gonderimSekilleri) > 0)
                                    <div class="mb-3">
                                        <label for="gonderim_sekli" class="form-label">
                                            <i class="fas fa-shipping-fast"></i> Gönderim Şekli
                                        </label>
                                        <select class="form-select" id="gonderim_sekli" name="gonderim_sekli" required>
                                            <option value="">Seçiniz...</option>
                                            @foreach($gonderimSekilleri as $gonderim)
                                                <option value="{{ $gonderim['erp_aciklama'] ?? '' }}">
                                                    {{ $gonderim['aciklama'] ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label for="order_notes" class="form-label">
                                        <i class="fas fa-sticky-note"></i> Sipariş Notu
                                    </label>
                                    <textarea class="form-control" id="order_notes" name="notes" rows="3"
                                        placeholder="Siparişiniz için eklemek istediğiniz notları yazabilirsiniz..."
                                        style="resize: vertical;"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg w-100 mb-2">
                                    <i class="fas fa-check"></i> Siparişi Tamamla
                                </button>
                            </form>
                            <button class="btn btn-outline-danger w-100" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Sepeti Temizle
                            </button>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body text-center">
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> Alışverişe Devam Et
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart" style="font-size: 5rem; color: #ccc;"></i>
                <h3 class="mt-3">Sepetiniz Boş</h3>
                <p class="text-muted">Sepetinize henüz ürün eklemediniz.</p>
                <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-shopping-bag"></i> Alışverişe Başla
                </a>
            </div>
        @endif
    </div>
@endsection

<!-- Ürün Detay Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body pt-0" id="modalContent">
                <!-- Ürün detayları AJAX ile yüklenecek -->
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* MF2 Hata Stilleri */
        .mf2-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            background-color: #fff5f5 !important;
        }

        .mf2-tooltip {
            position: absolute;
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1000;
            max-width: 280px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
            pointer-events: none;
        }

        .mf2-tooltip::before {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px 6px 0;
            border-style: solid;
            border-color: #dc3545 transparent transparent;
        }

        #productModal .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        #productModal .modal-header {
            padding: 1rem 1.5rem 0.5rem;
        }

        #productModal .modal-body {
            padding: 1.5rem;
        }

        #productModal .btn-close {
            box-shadow: none;
        }

        /* Mobile Cart Optimizations */
        @media (max-width: 768px) {
            .container {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            h2.mb-4 {
                font-size: 1.5rem !important;
                margin-bottom: 1rem !important;
            }

            .cart-table-wrapper .table {
                font-size: 0.85rem;
            }

            /* Hide columns on mobile */
            .hide-on-mobile {
                display: none !important;
            }

            /* Show mobile-specific cells */
            .mobile-combined-price-cell,
            .mobile-qty-delete-cell {
                display: table-cell !important;
            }

            /* Desktop-only cells */
            .desktop-only-cell {
                display: none !important;
            }

            /* Mobile column widths */
            .cart-table-wrapper table {
                table-layout: fixed;
                width: 100%;
            }

            .cart-table-wrapper .product-name-cell {
                width: 56%;
            }

            .cart-table-wrapper .mobile-combined-price-cell {
                width: 18%;
                padding: 0.4rem 0.2rem !important;
                vertical-align: middle !important;
            }

            .cart-table-wrapper .mobile-qty-delete-cell {
                width: 26%;
                padding: 0.4rem 0.2rem !important;
                vertical-align: middle !important;
            }

            /* Product name styling */
            .cart-table-wrapper .product-name-cell {
                padding: 0.4rem 0.3rem !important;
            }

            .cart-table-wrapper .product-name-cell a {
                font-size: 0.8rem !important;
                line-height: 1.2 !important;
                display: block;
            }

            /* Combined price cell (MF + Net Price) */
            .mobile-combined-price-cell {
                text-align: center !important;
                font-size: 0.75rem;
            }

            .mobile-combined-price-cell .mf-section {
                margin-bottom: 0.2rem;
                font-size: 0.7rem;
            }

            .mobile-combined-price-cell .price-section {
                font-weight: 600;
                font-size: 0.75rem;
            }

            /* Quantity and delete button cell */
            .mobile-qty-delete-cell {
                display: flex;
                flex-direction: column;
                gap: 0.2rem;
                align-items: center;
            }

            .mobile-qty-delete-cell .input-group {
                width: 100%;
                max-width: 100%;
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
            }

            .mobile-qty-delete-cell .input-group .btn {
                padding: 0.2rem 0.3rem !important;
                font-size: 0.7rem !important;
                min-width: 28px !important;
            }

            .mobile-qty-delete-cell .input-group input {
                font-size: 0.75rem !important;
                padding: 0.2rem 0.1rem !important;
                min-width: 45px !important;
                flex: 1;
            }

            .mobile-qty-delete-cell .btn-danger {
                width: 100%;
                padding: 0.25rem 0.4rem !important;
                font-size: 0.7rem !important;
            }

            /* Table header adjustments */
            .cart-table-wrapper thead th {
                padding: 0.4rem 0.2rem !important;
                font-size: 0.75rem !important;
                line-height: 1.2;
            }

            .cart-table-wrapper tbody td {
                padding: 0.4rem 0.2rem !important;
            }

            /* Cart summary adjustments */
            .col-lg-3 .card {
                margin-top: 1rem;
            }

            .col-lg-3 .card-body {
                padding: 0.75rem;
            }

            .col-lg-3 .card-body>div {
                font-size: 0.85rem;
            }

            .col-lg-3 .card-body .h5 {
                font-size: 1rem !important;
            }

            .col-lg-3 .btn {
                font-size: 0.9rem;
                padding: 0.5rem;
            }

            /* Empty cart styling */
            .text-center.py-5 {
                padding: 2rem 1rem !important;
            }

            .text-center.py-5 i.fa-shopping-cart {
                font-size: 3rem !important;
            }

            .text-center.py-5 h3 {
                font-size: 1.5rem !important;
            }
        }

        @media (max-width: 400px) {
            .cart-table-wrapper .product-name-cell {
                width: 54%;
            }

            .cart-table-wrapper .mobile-combined-price-cell {
                width: 19%;
            }

            .cart-table-wrapper .mobile-qty-delete-cell {
                width: 27%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Ürün detay modal fonksiyonu
        function showProductModal(productId) {
            // Ürün detay modalını üstte göstermek için z-index ayarla
            $('#productModal').css('z-index', '1060');

            // Eğer modal zaten açık değilse aç
            if (!$('#productModal').hasClass('show')) {
                $('#productModal').modal('show');
            }

            // Loading göster
            $('#modalContent').html(`
                                                                                                    <div class="text-center py-5">
                                                                                                        <div class="spinner-border text-primary" role="status">
                                                                                                            <span class="visually-hidden">Yükleniyor...</span>
                                                                                                        </div>
                                                                                                        <p class="mt-3">Ürün detayları yükleniyor...</p>
                                                                                                    </div>
                                                                                            `);

            // Modal'ı en üste çıkar         (diğer modalların üzerinde)
            $('#productModal').css('z-index', '1070');
            $('.modal-backdrop').last().css('z-index', '1065');

            // AJAX ile ürün detaylarını yükle
            $.ajax({
                url: '{{ url("/product") }}/' + productId + '/modal',
                method: 'GET',
                success: function (response) {
                    $('#modalContent').html(response);

                    // Tooltip'leri aktif et
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });

                    // Modal'ı en üste scroll et
                    $('#productModal .modal-body').scrollTop(0);
                },
                error: function (xhr) {
                    $('#modalContent').html(`
                                                                                                            <div class="alert alert-danger">
                                                                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                                                                Ürün detayları yüklenirken hata oluştu!
                                                                                                            </div>
                                                                                                        `);
                }
            });
        }

        // Modal miktar kontrolü
        function increaseModalQty() {
            const input = document.getElementById('modal-quantity');
            input.value = parseInt(input.value || 0) + 1;
        }

        function decreaseModalQty() {
            const input = document.getElementById('modal-quantity');
            const currentValue = parseInt(input.value || 0);
            if (currentValue > 0) {
                input.value = currentValue - 1;
            }
        }

        // Modal'dan sepete ekle
        function addToCartFromModal(productId, buttonElement) {
            const quantity = parseInt(document.getElementById('modal-quantity').value) || 0;

            if (quantity === 0) {
                if (buttonElement) {
                    showWarningNotification(buttonElement, 'Lütfen miktar girin!');
                } else {
                    showNotification('Lütfen miktar girin!', 'error');
                }
                return;
            }

            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity,
                    mf_satis: 0
                },
                success: function (response) {
                    if (response.success) {
                        updateCartCount();

                        // Başarı mesajı - butonun üzerinde
                        if (buttonElement) {
                            showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                        } else {
                            showNotification(response.message || 'Ürün sepete eklendi!', 'success');
                        }

                        // Input'u sıfırla
                        document.getElementById('modal-quantity').value = 0;

                        // 1.5 saniye sonra sayfayı yenile
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function (xhr) {
                    console.error('Add to cart error:', xhr);
                    let message = 'Sepete eklenirken hata oluştu';

                    if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status === 419) {
                        message = 'CSRF Token hatası. Sayfayı yenileyin.';
                    } else if (xhr.status === 404) {
                        message = 'Ürün bulunamadı.';
                    } else if (xhr.status === 401) {
                        message = 'Giriş yapmanız gerekiyor.';
                    } else if (xhr.status === 422) {
                        message = 'Geçersiz miktar değeri.';
                    } else if (xhr.status === 500) {
                        message = 'Sunucu hatası.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    // Butonun üstünde göster
                    if (buttonElement) {
                        showWarningNotification(buttonElement, message);
                    } else {
                        showNotification(message, 'error');
                    }
                }
            });
        }

        // Muadil ürün miktar kontrolü (MF2Utils kullanır)
        function increaseMuadilQty(productId) {
            const input = document.getElementById('muadil-qty-' + productId);
            MF2Utils.increaseQuantity(input);
        }

        function decreaseMuadilQty(productId) {
            const input = document.getElementById('muadil-qty-' + productId);
            MF2Utils.decreaseQuantity(input, 0);
        }

        function roundMf2QuantityMuadil(productId) {
            const input = document.getElementById('muadil-qty-' + productId);
            MF2Utils.roundQuantity(input);
        }

        // Muadil ürünü sepete ekle
        function addMuadilToCart(productId, buttonElement) {
            const quantity = parseInt(document.getElementById('muadil-qty-' + productId).value) || 0;

            if (quantity === 0) {
                if (buttonElement) {
                    showWarningNotification(buttonElement, 'Lütfen miktar girin!');
                } else {
                    showNotification('Lütfen miktar girin!', 'error');
                }
                return;
            }

            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity,
                    mf_satis: 0
                },
                success: function (response) {
                    if (response.success) {
                        updateCartCount();

                        // Başarı mesajı - butonun üzerinde
                        if (buttonElement) {
                            showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                        } else {
                            showNotification(response.message || 'Ürün sepete eklendi!', 'success');
                        }

                        // Miktarı sıfırla
                        document.getElementById('muadil-qty-' + productId).value = 0;

                        // 1.5 saniye sonra sayfayı yenile
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function (xhr) {
                    console.error('Add to cart error:', xhr);
                    let message = 'Sepete eklenirken hata oluştu';

                    if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status === 419) {
                        message = 'CSRF Token hatası. Sayfayı yenileyin.';
                    } else if (xhr.status === 404) {
                        message = 'Ürün bulunamadı.';
                    } else if (xhr.status === 401) {
                        message = 'Giriş yapmanız gerekiyor.';
                    } else if (xhr.status === 422) {
                        message = 'Geçersiz miktar değeri.';
                    } else if (xhr.status === 500) {
                        message = 'Sunucu hatası.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    // Butonun üstünde göster
                    if (buttonElement) {
                        showWarningNotification(buttonElement, message);
                    } else {
                        showNotification(message, 'error');
                    }
                }
            });
        }

        // Uyarı mesajı - butonun üzerinde (kırmızı)
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
            notification.style.zIndex = '99999';
            notification.style.maxWidth = '280px';
            notification.style.textAlign = 'center';
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s ease-out';

            document.body.appendChild(notification);

            const notificationWidth = notification.offsetWidth;
            const notificationHeight = notification.offsetHeight;
            notification.style.left = (buttonRect.left + buttonRect.width / 2 - notificationWidth / 2) + 'px';
            notification.style.top = (buttonRect.top - notificationHeight - 10) + 'px';

            setTimeout(function () { notification.style.opacity = '1'; }, 10);
            setTimeout(function () { notification.style.opacity = '0'; }, 2000);
            setTimeout(function () { notification.remove(); }, 2500);
        }

        // Başarı mesajı - butondan sepete uçan animasyon (yeşil)
        function showFlyingNotification(buttonElement, message) {
            const buttonRect = buttonElement.getBoundingClientRect();

            const notification = document.createElement('div');
            notification.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
            notification.style.position = 'fixed';
            notification.style.left = (buttonRect.left + buttonRect.width / 2 - 75) + 'px';
            notification.style.top = (buttonRect.top - 40) + 'px';
            notification.style.backgroundColor = '#28a745';
            notification.style.color = 'white';
            notification.style.padding = '10px 20px';
            notification.style.borderRadius = '25px';
            notification.style.fontSize = '1rem';
            notification.style.fontWeight = '500';
            notification.style.boxShadow = '0 4px 15px rgba(40, 167, 69, 0.5)';
            notification.style.zIndex = '99999';
            notification.style.opacity = '1';
            notification.style.whiteSpace = 'nowrap';
            notification.style.transition = 'opacity 0.5s ease-out';

            document.body.appendChild(notification);

            // 1.5 saniye bekle, sonra fade out yap
            setTimeout(function () {
                notification.style.opacity = '0';
            }, 1500);

            // Mesajı kaldır (toplam 2 saniye)
            setTimeout(function () {
                notification.remove();
            }, 2000);
        }

        // Sepet - Miktar artırma/azaltma - HER ZAMAN 1'er artır/azalt
        function increaseCartQuantity(cartId) {
            const input = document.getElementById('cart-qty-' + cartId);
            const newQty = MF2Utils.increaseQuantity(input);

            // Mobil input'u da senkronize et
            $('#cart-qty-mobile-' + cartId).val(newQty);
            // Bonus opsiyonunu kontrol et ve güncelle
            const bonusOption = checkCartBonusOptionOnQtyChange(cartId);
            // Backend'e gönder
            updateCartQuantityWithBonus(cartId, newQty, bonusOption);
        }

        function decreaseCartQuantity(cartId) {
            const input = document.getElementById('cart-qty-' + cartId);
            const currentQty = parseInt(input.value) || 1;
            // Sepette minimum değer her zaman 1
            const minValue = 1;

            if (currentQty > minValue) {
                const newQty = MF2Utils.decreaseQuantity(input, minValue);
                // Mobil input'u da senkronize et
                $('#cart-qty-mobile-' + cartId).val(newQty);
                // Bonus opsiyonunu kontrol et ve güncelle
                const bonusOption = checkCartBonusOptionOnQtyChange(cartId);
                // Backend'e gönder
                updateCartQuantityWithBonus(cartId, newQty, bonusOption);
            }
        }

        // Sepet - Blur'da bonus opsiyonu kontrol et (yuvarlama yapılmıyor)
        function roundMf2QuantityCart(cartId) {
            // Yuvarlama yapılmıyor, sadece radio kontrolü
            checkCartBonusOptionOnQtyChange(cartId);
        }

        function updateCartQuantity(cartId, quantity) {
            if (quantity < 1) return;

            // Input'ları güncelle (hem desktop hem mobile)
            $('#cart-qty-' + cartId).val(quantity);
            $('#cart-qty-mobile-' + cartId).val(quantity);

            // Bonus opsiyonunu kontrol et ve radio'yu güncelle
            const bonusOption = checkCartBonusOptionOnQtyChange(cartId);

            // Miktar ve bonus opsiyonunu birlikte gönder
            updateCartQuantityWithBonus(cartId, quantity, bonusOption);
        }

        // Sepet - Bonus opsiyon 1 seçildiğinde miktarı 1 yap
        function onCartBonusOption1Selected(cartId) {
            const qtyInput = $('#cart-qty-' + cartId);
            qtyInput.val(1);
            // Mobile input'u da güncelle
            $('#cart-qty-mobile-' + cartId).val(1);
            // MF2 hata durumunu güncelle (opsiyon 1 seçili olduğundan hata olmaz)
            updateMf2ErrorState(cartId, 1);
            // Miktarı backend'e gönder ve bonus opsiyonunu da güncelle
            updateCartQuantityWithBonus(cartId, 1, 1);
        }

        // Sepet - Bonus opsiyon 2 seçildiğinde minimum miktarı ayarla
        function onCartBonusOption2Selected(cartId, minQty) {
            const qtyInput = $('#cart-qty-' + cartId);
            // Her zaman minimum miktarı ayarla
            qtyInput.val(minQty);
            // Mobile input'u da güncelle
            $('#cart-qty-mobile-' + cartId).val(minQty);
            // MF2 hata durumunu güncelle
            updateMf2ErrorState(cartId, 2);
            // Miktarı ve bonus opsiyonunu backend'e gönder
            updateCartQuantityWithBonus(cartId, minQty, 2);
        }

        // Sepet - Miktar değiştiğinde bonus opsiyonu kontrol et
        function checkCartBonusOptionOnQtyChange(cartId) {
            const qtyInput = $('#cart-qty-' + cartId);
            const currentQty = parseInt(qtyInput.val()) || 0;

            // Opsiyon radio butonlarını bul
            const option2Radio = document.getElementById('cart_bonus_' + cartId + '_2');
            const option1Radio = document.getElementById('cart_bonus_' + cartId + '_1');

            let bonusOption = 1;

            // Eğer opsiyon 2 varsa
            if (option2Radio) {
                const minQty = parseInt(option2Radio.dataset.minQty) || 0;

                if (currentQty >= minQty && minQty > 0) {
                    // Miktar minimum miktara eşit veya fazlaysa opsiyon 2'yi seç
                    option2Radio.checked = true;
                    bonusOption = 2;
                } else if (option1Radio) {
                    // Miktar minimumun altındaysa opsiyon 1'e geç
                    option1Radio.checked = true;
                    bonusOption = 1;
                }
            }

            // MF2 hata kontrolü yap
            updateMf2ErrorState(cartId, bonusOption);

            return bonusOption;
        }

        // Sepet - Miktar ve bonus opsiyonunu birlikte güncelle
        function updateCartQuantityWithBonus(cartId, quantity, bonusOption) {
            if (quantity < 1) return;

            $.ajax({
                url: '{{ url("/cart") }}/' + cartId,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'PATCH',
                    quantity: quantity,
                    bonus_option: bonusOption
                },
                success: function (response) {
                    if (response.success) {
                        // Update both desktop and mobile quantity inputs
                        $('#cart-qty-' + cartId).val(quantity);
                        $('#cart-qty-mobile-' + cartId).val(quantity);
                        // Yeni değeri eski değer olarak kaydet
                        $('#cart-qty-' + cartId).data('old-value', quantity);
                        $('#cart-qty-mobile-' + cartId).data('old-value', quantity);
                        // Format with Turkish locale
                        const formattedTotal = parseFloat(response.total).toLocaleString('tr-TR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' ₺';
                        $('#cart-total-' + cartId).text(formattedTotal);
                        updateCartSummary();
                    } else {
                        showNotification(response.message || 'Güncelleme hatası', 'error');
                    }
                },
                error: function (xhr) {
                    console.error('Update with bonus error:', xhr);
                    showNotification('Güncelleme hatası', 'error');
                }
            });
        }

        function removeFromCart(cartId) {
            if (!confirm('Bu ürünü sepetten çıkarmak istediğinize emin misiniz?')) {
                return;
            }


            $.ajax({
                url: '{{ url("/cart") }}/' + cartId,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                },
                success: function (response) {

                    if (response.success) {
                        $('#cart-row-' + cartId).fadeOut(function () {
                            $(this).remove();
                            updateCartSummary();
                            updateCartCount();

                            // Check if cart is empty
                            if ($('tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                        showNotification(response.message, 'success');
                    } else {
                        showNotification(response.message || 'Ürün silinirken hata oluştu', 'error');
                    }
                },
                error: function (xhr) {
                    console.error('Delete error:', xhr);
                    let message = 'Ürün silinirken hata oluştu';

                    // Detailed error handling
                    if (xhr.status === 419) {
                        message = 'CSRF Token hatası. Lütfen sayfayı yenileyin. (Hata: 419)';
                        setTimeout(() => location.reload(), 2000);
                    } else if (xhr.status === 404) {
                        // Item already deleted or doesn't exist - remove from UI and reload
                        message = 'Ürün bulunamadı veya zaten silinmiş. Sayfa yenileniyor...';
                        showNotification(message, 'error');
                        $('#cart-row-' + cartId).fadeOut(function () {
                            $(this).remove();
                        });
                        setTimeout(() => location.reload(), 1500);
                        return; // Don't show notification again
                    } else if (xhr.status === 403) {
                        message = 'Bu işlem için yetkiniz yok. (Hata: 403)';
                    } else if (xhr.status === 500) {
                        message = 'Sunucu hatası. Lütfen tekrar deneyin. (Hata: 500)';
                    } else if (xhr.status === 405) {
                        message = 'HTTP Method hatası. (Hata: 405 - Method Not Allowed)';
                    } else if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message + ' (Hata: ' + xhr.status + ')';
                        }
                        if (xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            message += ' - Detaylar: ' + errors.join(', ');
                        }
                    } else if (xhr.responseText) {
                        try {
                            const text = xhr.responseText.substring(0, 200);
                            message += ' (Status: ' + xhr.status + ', Detay: ' + text + ')';
                        } catch (e) {
                            message += ' (Status: ' + xhr.status + ')';
                        }
                    }

                    showNotification(message, 'error');
                }
            });
        }

        function clearCart() {
            if (!confirm('Sepetinizdeki tüm ürünleri silmek istediğinize emin misiniz?')) {
                return;
            }

            $.ajax({
                url: '{{ route("cart.clear") }}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function (xhr) {
                    console.error('Clear cart error:', xhr);
                    let message = 'Sepet temizlenirken hata oluştu';

                    if (xhr.status === 419) {
                        message = 'CSRF Token hatası. Sayfayı yenileyin. (Hata: 419)';
                    } else if (xhr.status === 500) {
                        message = 'Sunucu hatası. (Hata: 500)';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message + ' (Hata: ' + xhr.status + ')';
                    } else {
                        message += ' (Status: ' + xhr.status + ')';
                    }

                    showNotification(message, 'error');
                }
            });
        }

        function updateCartSummary() {
            // Fetch updated totals from server
            $.ajax({
                url: '{{ route("cart.index") }}',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json'
                },
                success: function (response) {
                    if (response.subtotal !== undefined) {
                        // Ara Toplam
                        $('#subtotal').text(parseFloat(response.subtotal).toLocaleString('tr-TR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' ₺');

                        // KDV'leri oranlarına göre güncelle
                        // Önce mevcut KDV satırlarını kaldır
                        $('[id^="vat-rate-"]').remove();

                        // Yeni KDV satırlarını ekle
                        if (response.vat_by_rate) {
                            let vatHtml = '';
                            for (let rate in response.vat_by_rate) {
                                let amount = parseFloat(response.vat_by_rate[rate]);
                                let formattedAmount = amount.toLocaleString('tr-TR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                vatHtml += '<div class="d-flex justify-content-between mb-2" id="vat-rate-' + rate + '">';
                                vatHtml += '<span>KDV %' + Math.round(rate) + ':</span>';
                                vatHtml += '<strong>' + formattedAmount + ' ₺</strong>';
                                vatHtml += '</div>';
                            }
                            // Ara Toplam'dan sonra, hr'den önce ekle
                            $('#subtotal').parent().after(vatHtml);
                        }

                        // Genel Toplam
                        $('#grand-total').text(parseFloat(response.total).toLocaleString('tr-TR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' ₺');
                    }
                },
                error: function () {
                    // If AJAX fails, reload page as fallback
                    location.reload();
                }
            });
        }

        function showCartQuantityWarning(cartId, message) {
            // Try to find the visible input group (desktop or mobile)
            let inputGroup = document.getElementById('cart-qty-group-' + cartId);

            // If desktop input group is not visible, try mobile
            if (!inputGroup || window.getComputedStyle(inputGroup).display === 'none') {
                inputGroup = document.getElementById('cart-qty-group-mobile-' + cartId);
            }

            if (!inputGroup) return;

            const groupRect = inputGroup.getBoundingClientRect();

            // Mesaj elementi oluştur
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
            notification.style.maxWidth = '350px';
            notification.style.textAlign = 'center';
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s ease-out';

            document.body.appendChild(notification);

            // Genişliği aldıktan sonra pozisyonu ayarla (ortalamak için)
            const notificationWidth = notification.offsetWidth;
            const notificationHeight = notification.offsetHeight;
            notification.style.left = (groupRect.left + groupRect.width / 2 - notificationWidth / 2) + 'px';
            notification.style.top = (groupRect.top - notificationHeight - 10) + 'px'; // Input grubunun 10px üstünde

            // Görünür yap
            setTimeout(function () {
                notification.style.opacity = '1';
            }, 10);

            // Kaybol
            setTimeout(function () {
                notification.style.opacity = '0';
            }, 2500); // 2.5 saniye bekle

            // Kaldır
            setTimeout(function () {
                notification.remove();
            }, 2800); // 0.3s fade out sonrası
        }

        // Sipariş öncesi MF2 bölünemez validasyonu
        function validateCheckout() {
            const errors = [];

            // Tüm cart satırlarını kontrol et
            document.querySelectorAll('[id^="cart-row-"]').forEach(row => {
                const cartId = row.id.replace('cart-row-', '');
                const qtyInput = document.getElementById('cart-qty-' + cartId);
                if (!qtyInput) return;

                const mf2bolunemez = qtyInput.dataset.mf2bolunemez === '1';
                const mf2Step = parseInt(qtyInput.dataset.mf2Step) || 0;
                const quantity = parseInt(qtyInput.value) || 0;

                // MF2 radyo seçili mi kontrol et
                const option2Radio = document.getElementById('cart_bonus_' + cartId + '_2');
                const isMf2Selected = option2Radio && option2Radio.checked;

                if (mf2bolunemez && mf2Step > 0 && isMf2Selected) {
                    if (quantity % mf2Step !== 0) {
                        // Ürün adını bul
                        const productNameCell = row.querySelector('.product-name-cell strong');
                        const productName = productNameCell ? productNameCell.textContent : 'Ürün';

                        // Örnek miktarlar
                        const examples = [];
                        for (let i = 1; i <= 5; i++) {
                            examples.push(mf2Step * i);
                        }

                        errors.push(`• ${productName}: ${quantity} adet (Gerekli: ${mf2Step} ve katları, örn: ${examples.join(', ')}...)`);
                    }
                }
            });

            if (errors.length > 0) {
                alert('Aşağıdaki ürünlerin miktarları düzeltilmeden sipariş verilemez:\n\n' + errors.join('\n'));
                return false;
            }

            return true;
        }

        // MF2 hata durumunu güncelle
        function updateMf2ErrorState(cartId, bonusOption) {
            const desktopInput = document.getElementById('cart-qty-' + cartId);
            const mobileInput = document.getElementById('cart-qty-mobile-' + cartId);

            if (!desktopInput) return;

            const mf2bolunemez = desktopInput.dataset.mf2bolunemez === '1';
            const mf2Step = parseInt(desktopInput.dataset.mf2Step) || 0;
            const quantity = parseInt(desktopInput.value) || 0;
            const isMf2Selected = bonusOption === 2;

            let hasError = false;
            if (mf2bolunemez && mf2Step > 0 && isMf2Selected) {
                hasError = quantity % mf2Step !== 0;
            }

            // Desktop input
            if (hasError) {
                desktopInput.classList.add('mf2-error');
            } else {
                desktopInput.classList.remove('mf2-error');
            }

            // Mobile input
            if (mobileInput) {
                if (hasError) {
                    mobileInput.classList.add('mf2-error');
                } else {
                    mobileInput.classList.remove('mf2-error');
                }
            }
        }

        // MF2 tooltip göster
        function showMf2Tooltip(cartId, isMobile = false) {
            const inputId = isMobile ? 'cart-qty-mobile-' + cartId : 'cart-qty-' + cartId;
            const tooltipId = isMobile ? 'mf2-tooltip-mobile-' + cartId : 'mf2-tooltip-' + cartId;
            const input = document.getElementById(inputId);
            const tooltip = document.getElementById(tooltipId);

            if (!input || !tooltip) return;

            const tooltipText = input.dataset.tooltip;
            if (!tooltipText) return;

            // MF2 seçili mi kontrol et
            const option2Radio = document.getElementById('cart_bonus_' + cartId + '_2');
            const isMf2Selected = option2Radio && option2Radio.checked;
            if (!isMf2Selected) return;

            tooltip.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>' + tooltipText;
            tooltip.style.display = 'block';

            // Pozisyonu hesapla
            const inputRect = input.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();

            tooltip.style.position = 'fixed';
            tooltip.style.left = (inputRect.left + inputRect.width / 2 - tooltipRect.width / 2) + 'px';
            tooltip.style.top = (inputRect.top - tooltipRect.height - 10) + 'px';
        }

        // MF2 tooltip gizle
        function hideMf2Tooltip(cartId, isMobile = false) {
            const tooltipId = isMobile ? 'mf2-tooltip-mobile-' + cartId : 'mf2-tooltip-' + cartId;
            const tooltip = document.getElementById(tooltipId);
            if (tooltip) {
                tooltip.style.display = 'none';
            }
        }
    </script>
@endpush