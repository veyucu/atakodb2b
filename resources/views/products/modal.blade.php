<div class="border rounded p-3 mb-2 shadow-sm modal-product-main {{ $product->ozel_liste ? 'campaign-product-modal' : 'bg-white' }}"
    style="{{ $product->ozel_liste ? 'background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 2px solid #f59e0b !important;' : '' }}">
    <div class="row align-items-center g-3">
        <!-- Resim -->
        <div class="col-auto modal-product-image" style="width: 150px;">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" class="img-fluid rounded border" alt="{{ $product->urun_adi }}"
                    style="width: 140px; height: 140px; object-fit: contain; background: #f8f9fa; padding: 0.5rem;">
            @else
                <div class="bg-light rounded border d-flex align-items-center justify-content-center"
                    style="width: 140px; height: 140px;">
                    <i class="fas fa-camera text-muted" style="font-size: 2rem; opacity: 0.3;"></i>
                </div>
            @endif
        </div>

        <!-- Ürün Bilgileri -->
        <div class="col">
            <h5 class="fw-bold mb-2" style="font-size: 1.1rem; line-height: 1.3; color: #1e293b;">
                {{ $product->urun_adi }}
            </h5>
            <div class="mb-2">
                <span class="badge bg-primary"
                    style="font-size: 0.8rem; padding: 0.35rem 0.6rem;">{{ $product->urun_kodu }}</span>
                @if($product->marka)
                    <span class="badge bg-info"
                        style="font-size: 0.8rem; padding: 0.35rem 0.6rem;">{{ $product->marka }}</span>
                @endif
                @if($product->ozel_liste)
                    <span class="badge"
                        style="background: linear-gradient(135deg, #f59e0b, #d97706); font-size: 0.8rem; padding: 0.35rem 0.6rem;">
                        <i class="fas fa-tag me-1"></i>KAMPANYA
                    </span>
                @endif
                @if($product->bakiye > 0)
                    <span class="badge" style="background: #10b981; font-size: 0.8rem; padding: 0.35rem 0.6rem;">
                        <i class="fas fa-check me-1"></i>Stokta Var
                    </span>
                @else
                    <span class="badge" style="background: #ef4444; font-size: 0.8rem; padding: 0.35rem 0.6rem;">
                        <i class="fas fa-times me-1"></i>Stok Yok
                    </span>
                @endif
            </div>
            <div style="font-size: 0.9rem; line-height: 1.6;">
                <div>
                    <span class="text-muted">Perakende:</span> <strong
                        class="text-dark">{{ number_format($product->satis_fiyati, 2, ',', '.') }} ₺</strong>
                    @if($product->depocu_fiyati)
                        <span class="text-muted ms-3">Depocu:</span> <strong
                            class="text-dark">{{ number_format($product->depocu_fiyati, 2, ',', '.') }} ₺</strong>
                    @endif
                    <span class="text-muted ms-3">KDV:</span> <strong
                        class="text-dark">%{{ number_format($product->kdv_orani, 0) }}</strong>
                </div>
            </div>
            <div class="mt-2">
                <span class="badge"
                    style="background-color: #8b5cf6; font-size: 0.75rem; padding: 0.3rem 0.5rem;">Eczacı:
                    %{{ number_format($product->eczaci_kari ?? 0, 2) }}</span>
                <span class="badge bg-warning text-dark" style="font-size: 0.75rem; padding: 0.3rem 0.5rem;">Kurum:
                    %{{ number_format($product->kurum_iskonto ?? 0, 2) }}</span>
                <span class="badge bg-secondary" style="font-size: 0.75rem; padding: 0.3rem 0.5rem;">Ticari:
                    %{{ number_format($product->ticari_iskonto ?? 0, 2) }}</span>
            </div>
        </div>

        <!-- Mal Fazlası ve Net Fiyat (bonus opsiyonları) -->
        @if($product->mf1 || $product->mf2)
            @php
                $mf2MinQtyModal = 0;
                if ($product->mf2 && str_contains($product->mf2, '+')) {
                    $partsModal = explode('+', $product->mf2);
                    $mf2MinQtyModal = (int) trim($partsModal[0]) + (int) trim($partsModal[1]);
                }
            @endphp
            <div class="col-auto modal-product-mf">
                <div class="text-center px-3 py-2 rounded"
                    style="background: linear-gradient(135deg, #fef3c7, #fde68a); border: 2px solid #f59e0b; min-width: 200px;">
                    <small class="d-block fw-bold mb-2"
                        style="font-size: 0.7rem; color: #92400e; letter-spacing: 0.5px;">MAL FAZLASI & NET FİYAT</small>
                    @if($product->mf1)
                        <div class="d-flex align-items-center justify-content-between mb-2 px-2 py-1 rounded"
                            style="background: rgba(16, 185, 129, 0.15); gap: 1rem;">
                            <div class="d-flex align-items-center">
                                @if($product->mf1 && $product->mf2)
                                    <input class="form-check-input me-2" type="radio" name="modal_product_bonus_{{ $product->id }}"
                                        id="modal_product_bonus_{{ $product->id }}_1" value="1" checked data-min-qty="0"
                                        onchange="onModalProductBonusOption1Selected({{ $product->id }})"
                                        style="margin: 0; width: 1.1rem; height: 1.1rem; cursor: pointer;">
                                @endif
                                <label for="modal_product_bonus_{{ $product->id }}_1" style="cursor: pointer;">
                                    <span class="badge bg-success" style="font-size: 0.9rem;">{{ $product->mf1 }}</span>
                                </label>
                            </div>
                            <label for="modal_product_bonus_{{ $product->id }}_1" style="cursor: pointer; margin: 0;">
                                <strong class="text-success"
                                    style="font-size: 1.1rem;">{{ $product->net_fiyat1 ? number_format($product->net_fiyat1, 2, ',', '.') . ' ₺' : '-' }}</strong>
                            </label>
                        </div>
                    @endif
                    @if($product->mf2)
                        <div class="d-flex align-items-center justify-content-between px-2 py-1 rounded"
                            style="background: rgba(59, 130, 246, 0.15); gap: 1rem;">
                            <div class="d-flex align-items-center">
                                @if($product->mf1 && $product->mf2)
                                    <input class="form-check-input me-2" type="radio" name="modal_product_bonus_{{ $product->id }}"
                                        id="modal_product_bonus_{{ $product->id }}_2" value="2" data-min-qty="{{ $mf2MinQtyModal }}"
                                        onchange="onModalProductBonusOption2Selected({{ $product->id }}, {{ $mf2MinQtyModal }})"
                                        style="margin: 0; width: 1.1rem; height: 1.1rem; cursor: pointer;">
                                @endif
                                <label for="modal_product_bonus_{{ $product->id }}_2" style="cursor: pointer;">
                                    <span class="badge bg-primary" style="font-size: 0.9rem;">{{ $product->mf2 }}</span>
                                </label>
                            </div>
                            <label for="modal_product_bonus_{{ $product->id }}_2" style="cursor: pointer; margin: 0;">
                                <strong class="text-primary"
                                    style="font-size: 1.1rem;">{{ $product->net_fiyat2 ? number_format($product->net_fiyat2, 2, ',', '.') . ' ₺' : '-' }}</strong>
                            </label>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Sepete Ekle -->
        <div class="col-auto modal-product-cart">
            <div style="width: 150px;">
                <div class="input-group mb-2">
                    <button class="btn btn-outline-secondary" type="button"
                        onclick="decreaseModalProductQty({{ $product->id }})" style="padding: 0.4rem 0.7rem;">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" id="modal-product-qty-{{ $product->id }}"
                        class="form-control text-center fw-bold" value="0" min="0"
                        style="font-size: 1.1rem; padding: 0.4rem;"
                        data-mf2bolunemez="{{ $product->mf2bolunemez ? '1' : '0' }}"
                        data-mf2-step="{{ $mf2MinQtyModal }}"
                        oninput="checkModalProductBonusOptionOnQtyChange({{ $product->id }})"
                        onblur="roundMf2QuantityModal({{ $product->id }})"
                        onkeypress="if(event.key === 'Enter' && this.value > 0) { addModalProductToCart({{ $product->id }}, this); }">
                    <button class="btn btn-outline-secondary" type="button"
                        onclick="increaseModalProductQty({{ $product->id }})" style="padding: 0.4rem 0.7rem;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-success w-100 fw-bold"
                    onclick="addModalProductToCart({{ $product->id }}, this)"
                    style="font-size: 0.85rem; padding: 0.5rem;">
                    <i class="fas fa-cart-plus me-1"></i>SEPETE EKLE
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Muadil Ürünler -->
@if($muadilProducts->count() > 0)
    <div class="mt-3 pt-3 border-top">
        <h6 class="fw-bold mb-2" style="font-size: 0.95rem;">
            <i class="fas fa-sitemap text-info me-2"></i>
            Muadil Ürünler <span class="badge bg-info">{{ $muadilProducts->count() }}</span>
        </h6>

        <div class="table-responsive" style="border-radius: 8px; overflow: hidden;">
            <table class="table table-hover list-view-table modal-muadil-table mb-0">
                <thead>
                    <tr>
                        <th class="hide-on-mobile text-center" style="width: 100px;">Ürün Kodu</th>
                        <th>Ürün Adı</th>
                        <th class="text-center hide-on-mobile" style="width: 90px;">Perakende Fiyatı</th>
                        <th class="text-center hide-on-mobile" style="width: 90px;">Depocu Fiyatı</th>
                        <th class="text-center hide-on-mobile" style="width: 80px;">Mal Fazlası</th>
                        <th class="text-center mobile-combined-cell" style="width: 100px;"><span class="hide-on-mobile">KDV
                                Dahil Net Fiyat</span><span class="show-on-mobile">Fiyat</span></th>
                        <th class="text-center mobile-qty-cart" style="width: 150px;"><span
                                class="hide-on-mobile">Miktar</span><span class="show-on-mobile">Miktar / Sepet</span></th>
                        <th class="hide-on-mobile" style="width: 60px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($muadilProducts as $muadil)
                        <tr class="{{ $muadil->ozel_liste ? 'campaign-product-row' : '' }}">
                            <td class="text-center hide-on-mobile">
                                @if($muadil->ozel_liste)
                                    <span class="badge"
                                        style="background: linear-gradient(135deg, #f59e0b, #d97706); font-size: 0.85rem; font-weight: 500;">
                                        {{ $muadil->urun_kodu }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border" style="font-size: 0.85rem; font-weight: 500;">
                                        {{ $muadil->urun_kodu }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="javascript:void(0)" onclick="showProductModal({{ $muadil->id }})"
                                    class="text-decoration-none">
                                    @if($muadil->bakiye > 0)
                                        <span class="badge me-1" title="Stokta Var"
                                            style="background: #10b981; font-size: 0.6rem; padding: 0.2rem 0.35rem;">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @else
                                        <span class="badge me-1" title="Stokta Yok"
                                            style="background: #ef4444; font-size: 0.6rem; padding: 0.2rem 0.35rem;">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    @endif
                                    @if($muadil->hasImage())
                                        <span class="product-name-with-image desktop-only-hover"
                                            onmouseenter="showImagePreview(event, '{{ $muadil->image_url }}')"
                                            onmouseleave="hideImagePreview()" style="cursor: pointer;">
                                            {{ $muadil->urun_adi }}
                                        </span>
                                    @else
                                        {{ $muadil->urun_adi }}
                                    @endif
                                </a>
                            </td>
                            <td class="text-end hide-on-mobile">
                                <span class="text-muted">{{ number_format($muadil->satis_fiyati, 2, ',', '.') }} ₺</span>
                            </td>
                            <td class="text-end hide-on-mobile">
                                @if($muadil->depocu_fiyati)
                                    <span class="text-muted">{{ number_format($muadil->depocu_fiyati, 2, ',', '.') }} ₺</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center hide-on-mobile">
                                @if($muadil->mf1 || $muadil->mf2)
                                    @php
                                        $mf2MinQtyMuadilModal = 0;
                                        if ($muadil->mf2 && str_contains($muadil->mf2, '+')) {
                                            $partsMuadilModal = explode('+', $muadil->mf2);
                                            $mf2MinQtyMuadilModal = (int) trim($partsMuadilModal[0]) + (int) trim($partsMuadilModal[1]);
                                        }
                                    @endphp
                                    <div style="font-size: 0.85rem;">
                                        @if($muadil->mf1)
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                @if($muadil->mf1 && $muadil->mf2)
                                                    <input class="form-check-input me-1" type="radio"
                                                        name="muadil_modal_bonus_{{ $muadil->id }}"
                                                        id="muadil_modal_bonus_{{ $muadil->id }}_1" value="1" checked data-min-qty="0"
                                                        onchange="onMuadilModalBonusOption1Selected({{ $muadil->id }})" style="margin: 0;">
                                                @endif
                                                <label for="muadil_modal_bonus_{{ $muadil->id }}_1" style="cursor: pointer;">
                                                    <span class="badge bg-success" style="font-size: 0.8rem;">{{ $muadil->mf1 }}</span>
                                                </label>
                                            </div>
                                        @endif
                                        @if($muadil->mf2)
                                            <div class="d-flex align-items-center justify-content-center">
                                                @if($muadil->mf1 && $muadil->mf2)
                                                    <input class="form-check-input me-1" type="radio"
                                                        name="muadil_modal_bonus_{{ $muadil->id }}"
                                                        id="muadil_modal_bonus_{{ $muadil->id }}_2" value="2"
                                                        data-min-qty="{{ $mf2MinQtyMuadilModal }}"
                                                        onchange="onMuadilModalBonusOption2Selected({{ $muadil->id }}, {{ $mf2MinQtyMuadilModal }})"
                                                        style="margin: 0;">
                                                @endif
                                                <label for="muadil_modal_bonus_{{ $muadil->id }}_2" style="cursor: pointer;">
                                                    <span class="badge bg-primary" style="font-size: 0.8rem;">{{ $muadil->mf2 }}</span>
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end mobile-combined-cell">
                                @php $muadilNetFiyat = $muadil->net_fiyat1 ?? $muadil->net_price ?? 0; @endphp
                                <div class="desktop-price">
                                    @if($muadil->mf1 || $muadil->mf2)
                                        <div style="font-size: 0.85rem;">
                                            @if($muadil->mf1)
                                                <div class="mb-1">
                                                    <strong
                                                        class="text-success">{{ $muadil->net_fiyat1 ? number_format($muadil->net_fiyat1, 2, ',', '.') . ' ₺' : '-' }}</strong>
                                                </div>
                                            @endif
                                            @if($muadil->mf2)
                                                <div>
                                                    <strong
                                                        class="text-primary">{{ $muadil->net_fiyat2 ? number_format($muadil->net_fiyat2, 2, ',', '.') . ' ₺' : '-' }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <strong
                                            style="color: #198754; font-size: 1rem;">{{ number_format($muadilNetFiyat, 2, ',', '.') }}
                                            ₺</strong>
                                    @endif
                                </div>
                                <div class="mobile-price-mf">
                                    @if($muadil->mf1)
                                        <div style="font-size: 0.75rem; color: #666; margin-bottom: 2px;">
                                            <span class="badge bg-success"
                                                style="font-size: 0.6rem; padding: 0.1rem 0.2rem;">{{ $muadil->mf1 }}</span>
                                        </div>
                                    @endif
                                    @if($muadil->mf2)
                                        <div style="font-size: 0.75rem; color: #666; margin-bottom: 2px;">
                                            <span class="badge bg-primary"
                                                style="font-size: 0.6rem; padding: 0.1rem 0.2rem;">{{ $muadil->mf2 }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <strong
                                            style="color: #198754; font-size: 0.85rem;">{{ number_format($muadilNetFiyat, 2, ',', '.') }}
                                            ₺</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="mobile-qty-cart-cell">
                                <div class="qty-cart-container">
                                    <div class="input-group input-group-sm">
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="decreaseMuadilModalQty({{ $muadil->id }})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" id="muadil-modal-qty-{{ $muadil->id }}"
                                            class="form-control text-center" value="0" min="0" style="max-width: 80px;"
                                            oninput="checkMuadilModalBonusOptionOnQtyChange({{ $muadil->id }})"
                                            onkeypress="if(event.key === 'Enter' && this.value > 0) { addMuadilModalToCart({{ $muadil->id }}, this); }">
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="increaseMuadilModalQty({{ $muadil->id }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="mobile-cart-btn">
                                        <button type="button" class="btn btn-success btn-sm"
                                            onclick="addMuadilModalToCart({{ $muadil->id }}, this)" title="Sepete Ekle"
                                            style="padding: 0.28rem 0.38rem; font-size: 0.75rem;">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td class="mobile-qty-cart-cell">
                                <div class="desktop-cart">
                                    <button type="button" class="btn btn-success btn-sm"
                                        onclick="addMuadilModalToCart({{ $muadil->id }}, this)" title="Sepete Ekle"
                                        data-bs-toggle="tooltip" data-bs-placement="top" style="padding: 0.25rem 0.6rem;">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif