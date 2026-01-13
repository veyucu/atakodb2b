@extends('layouts.app')

@section('title', 'Ana Sayfa - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@push('styles')
<style>
    /* Arama Formu Genel Stil */
    .search-form-wrapper {
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .search-input-group {
        display: flex;
        align-items: center;
        flex: 1;
    }
    
    .filter-checkboxes {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .filter-checkboxes .filter-item {
        margin: 0;
    }
    
    /* Modal Animasyonu */
    .modal.fade .modal-dialog {
        transform: scale(0.8);
        opacity: 0;
        transition: all 0.3s ease-out;
    }
    
    .modal.show .modal-dialog {
        transform: scale(1);
        opacity: 1;
    }
    
    /* Modal İçerik */
    #productModal .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
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
    
    /* Kampanyalı Ürün Kartı (Katalog Görünümü) */
    .campaign-product-card {
        border: 2px solid #f59e0b !important;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2) !important;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
    }
    
    .campaign-product-card:hover {
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.35) !important;
        transform: translateY(-5px);
    }
    
    /* Kampanyalı Ürün Satırı (Liste Görünümü) */
    .campaign-product-row {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
        border-left: 4px solid #f59e0b !important;
    }
    
    .campaign-product-row:hover {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
    }
    
    /* Kampanyalı checkbox stili */
    #kampanyali_check:checked {
        background-color: #f59e0b;
        border-color: #f59e0b;
    }

    /* Mobil için popup ürün kartı optimizasyonu */
    @media (max-width: 768px) {
        /* Ana ürün kartı kompakt */
        .modal-product-main {
            padding: 0.75rem !important;
            margin-bottom: 1rem !important;
        }
        
        .modal-product-main .row {
            gap: 0.5rem !important;
        }
        
        /* Resmi küçült */
        .modal-product-image {
            width: 80px !important;
        }
        
        .modal-product-image img,
        .modal-product-image > div {
            width: 80px !important;
            height: 80px !important;
        }
        
        .modal-product-image i {
            font-size: 1.5rem !important;
        }
        
        /* Ürün bilgilerini küçült */
        .modal-product-main h5 {
            font-size: 0.85rem !important;
            margin-bottom: 0.5rem !important;
            line-height: 1.3 !important;
        }
        
        .modal-product-main .badge {
            font-size: 0.65rem !important;
            padding: 0.25rem 0.4rem !important;
        }
        
        .modal-product-main > div.col > div[style*="font-size: 0.9rem"] {
            font-size: 0.75rem !important;
            line-height: 1.4 !important;
        }
        
        .modal-product-main > div.col > div[style*="font-size: 0.9rem"] strong {
            font-size: 0.75rem !important;
        }
        
        /* Mal fazlası ve Net fiyat küçült */
        .modal-product-mf,
        .modal-product-price {
            width: 100% !important;
        }
        
        .modal-product-mf > div,
        .modal-product-price > div {
            min-width: auto !important;
            padding: 0.5rem 0.75rem !important;
        }
        
        .modal-product-mf small,
        .modal-product-price small {
            font-size: 0.6rem !important;
        }
        
        .modal-product-mf div[style*="font-size: 1.8rem"],
        .modal-product-price div[style*="font-size: 1.8rem"] {
            font-size: 1.3rem !important;
        }
        
        /* Sepete ekle alanı */
        .modal-product-cart {
            width: 100% !important;
        }
        
        .modal-product-cart > div {
            width: 100% !important;
        }
        
        .modal-product-cart .input-group button {
            padding: 0.35rem 0.5rem !important;
        }
        
        .modal-product-cart input {
            font-size: 0.9rem !important;
            padding: 0.35rem !important;
        }
        
        .modal-product-cart .btn-success {
            font-size: 0.75rem !important;
            padding: 0.4rem !important;
        }
        
        /* Modal muadil tablosu için ayarlar */
        .modal-muadil-table thead th:nth-child(2) {
            width: 56% !important;
            text-align: left !important;
        }
        
        .modal-muadil-table tbody td:nth-child(2) {
            width: 56% !important;
            word-wrap: break-word;
            white-space: normal;
            line-height: 1.4;
            padding: 0.6rem 0.4rem !important;
            text-align: left !important;
        }
        
        .modal-muadil-table tbody td:nth-child(2) a {
            font-size: 0.85rem;
            display: inline;
            line-height: 1.3;
        }
        
        .modal-muadil-table .badge.me-1 {
            margin-right: 0.05rem !important;
        }
        
        .modal-muadil-table thead th:nth-child(6) {
            width: 16% !important;
            text-align: center !important;
        }
        
        .modal-muadil-table tbody td:nth-child(6) {
            width: 16% !important;
            text-align: center !important;
            padding: 0.5rem 0.2rem !important;
        }
        
        .modal-muadil-table tbody td:nth-child(6) .mobile-price-mf strong {
            font-size: 0.75rem !important;
            display: block;
        }
        
        .modal-muadil-table thead th:nth-child(7) {
            width: 28% !important;
            text-align: center !important;
        }
        
        .modal-muadil-table tbody td:nth-child(7) {
            width: 28% !important;
            padding: 0.5rem 0.2rem !important;
            text-align: center !important;
        }
        
        .modal-muadil-table .input-group {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
        }
        
        .modal-muadil-table .input-group input {
            max-width: 32px !important;
            min-width: 32px !important;
            flex: 0 0 32px !important;
        }
        
        .modal-muadil-table .input-group button {
            flex: 0 0 auto !important;
        }
    }

    /* Mobil için katalog görünümü optimizasyonu */
    @media (max-width: 768px) {
        /* Arama formu mobil düzenlemesi */
        .search-form-wrapper {
            flex-direction: row !important;
            align-items: center !important;
            gap: 0.5rem;
        }
        
        .search-input-group {
            display: flex;
            flex: 1;
            gap: 0.25rem;
            min-width: 0;
        }
        
        .search-input-group input[type="text"] {
            flex: 1;
            min-width: 0;
            margin-right: 0 !important;
            font-size: 0.875rem;
        }
        
        .search-input-group .search-button {
            flex-shrink: 0;
            padding: 0.375rem 0.75rem !important;
            white-space: nowrap;
        }
        
        .filter-checkboxes {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            flex-shrink: 0;
            align-items: flex-start;
            justify-content: center;
        }
        
        .filter-checkboxes .filter-item {
            margin: 0 !important;
            font-size: 0.8rem;
            white-space: nowrap;
            line-height: 1;
        }
        
        .filter-checkboxes .filter-item .form-check-input {
            margin-top: 0;
        }
        
        .filter-checkboxes .filter-item .form-check-label {
            font-size: 0.8rem !important;
            line-height: 1.2;
            margin-bottom: 0;
        }
        
        /* Katalog kartları için mobil düzenlemeler */
        .product-card {
            font-size: 0.8rem;
        }
        
        .product-card .card-body {
            padding: 0.5rem !important;
        }
        
        .product-card h6 {
            font-size: 0.75rem !important;
            min-height: auto !important;
            line-height: 1.3 !important;
            margin-bottom: 0.5rem !important;
        }
        
        /* Ürün resmi küçült */
        .product-card .card-img-top,
        .product-card .product-image {
            height: 140px !important;
        }
        
        /* Fiyat alanı küçült */
        .product-card .card-body > div.mb-1 {
            padding: 0.4rem !important;
            font-size: 0.75rem;
        }
        
        .product-card .card-body > div.mb-1 small {
            font-size: 0.6rem !important;
        }
        
        .product-card .card-body > div.mb-1 strong {
            font-size: 0.85rem !important;
        }
        
        /* Badge'leri küçült */
        .product-card .badge {
            font-size: 0.6rem !important;
            padding: 0.2rem 0.35rem !important;
        }
        
        /* Input ve butonları küçült */
        .product-card .input-group-sm input {
            font-size: 0.7rem;
            padding: 0.25rem 0.15rem;
        }
        
        .product-card .input-group-sm button {
            padding: 0.25rem 0.3rem;
            font-size: 0.65rem;
        }
        
        .product-card .btn-sm {
            padding: 0.3rem 0.45rem !important;
            font-size: 0.75rem !important;
        }
        
        /* Kampanya rozeti küçült */
        .product-card .position-absolute.m-2 {
            margin: 0.3rem !important;
            font-size: 0.55rem !important;
            padding: 0.2rem 0.4rem !important;
        }
        
        /* Ürün kodu badge küçült */
        .product-card .badge.position-absolute.top-0.start-0 {
            font-size: 0.6rem !important;
            padding: 0.2rem 0.35rem !important;
        }
        
        /* Stok badge'i küçült */
        .product-card .position-absolute.top-0.end-0 {
            font-size: 0.55rem !important;
            padding: 0.2rem 0.3rem !important;
        }
        
        /* Col boşlukları azalt */
        .row > [class*='col-'] {
            padding-left: 0.35rem;
            padding-right: 0.35rem;
        }
        
        /* Resim hazırlanıyor alanı */
        .product-card .d-flex.align-items-center.justify-content-center {
            height: 140px !important;
        }
        
        .product-card .d-flex.align-items-center.justify-content-center i {
            font-size: 2rem !important;
        }
        
        .product-card .d-flex.align-items-center.justify-content-center p {
            font-size: 0.7rem !important;
        }
    }

    /* Mobil için özel kampanya popup düzenlemeleri */
    @media (max-width: 768px) {
        /* Gereksiz sütunları gizle */
        .hide-on-mobile {
            display: none !important;
        }
        
        /* Mobilde göster */
        .show-on-mobile {
            display: inline !important;
        }
        
        /* Desktop'ta gizle */
        .desktop-price {
            display: none !important;
        }
        
        /* Mobilde göster */
        .mobile-price-mf {
            display: block !important;
            text-align: center;
        }
        
        /* Modal boyutunu küçült */
        #specialCampaignModal .modal-dialog {
            margin: 0.25rem;
            max-width: calc(100% - 0.5rem);
        }
        
        #specialCampaignModal .modal-body {
            padding: 0.5rem !important;
        }
        
        /* Tablo yazı boyutlarını ayarla */
        .campaign-table {
            font-size: 0.85rem;
            margin-bottom: 0 !important;
            table-layout: fixed;
            width: 100%;
        }
        
        .campaign-table th {
            padding: 0.6rem 0.3rem !important;
            font-size: 0.75rem !important;
            white-space: nowrap;
            vertical-align: middle !important;
        }
        
        .campaign-table td {
            padding: 0.6rem 0.3rem !important;
            font-size: 0.8rem !important;
            vertical-align: middle !important;
        }
        
        /* Ürün adı sütunu - 2. sütun - büyütüldü */
        .campaign-table thead th:nth-child(2) {
            width: 56% !important;
            text-align: left !important;
        }
        
        .campaign-table tbody td:nth-child(2) {
            width: 56% !important;
            word-wrap: break-word;
            white-space: normal;
            line-height: 1.4;
            padding: 0.6rem 0.4rem !important;
            text-align: left !important;
        }
        
        .campaign-table tbody td:nth-child(2) a {
            font-size: 0.85rem;
            display: inline;
            line-height: 1.3;
        }
        
        /* Stok badge'lerini küçült - boşluk tamamen kaldırıldı */
        .campaign-table .badge.me-1 {
            font-size: 0.5rem !important;
            padding: 0.1rem 0.2rem !important;
            display: inline-block;
            vertical-align: middle;
            margin-right: 0.05rem !important;
        }
        
        /* MF ve Fiyat birleşik sütunu - 6. sütun */
        .campaign-table thead th:nth-child(6) {
            width: 16% !important;
            text-align: center !important;
        }
        
        .campaign-table tbody td:nth-child(6) {
            width: 16% !important;
            text-align: center !important;
            padding: 0.5rem 0.2rem !important;
        }
        
        .campaign-table tbody td:nth-child(6) .mobile-price-mf {
            line-height: 1.2;
        }
        
        .campaign-table tbody td:nth-child(6) .mobile-price-mf strong {
            font-size: 0.75rem !important;
            display: block;
        }
        
        /* Miktar ve Sepet birleşik sütunu - 7. sütun - küçültüldü */
        .campaign-table thead th:nth-child(7) {
            width: 28% !important;
            text-align: center !important;
        }
        
        .campaign-table tbody td:nth-child(7) {
            width: 28% !important;
            padding: 0.5rem 0.2rem !important;
            text-align: center !important;
        }
        
        /* Miktar ve sepet containerı - gap azaltıldı */
        .qty-cart-container {
            display: flex !important;
            flex-direction: row !important;
            gap: 0.1rem;
            align-items: center;
            justify-content: center;
            flex-wrap: nowrap !important;
        }
        
        .mobile-cart-btn {
            display: none;
        }
        
        .desktop-cart {
            display: block;
        }
        
        .campaign-table .input-group {
            max-width: 100%;
            min-width: 0;
            flex: 1;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
        }
        
        .campaign-table .input-group input {
            max-width: 32px !important;
            min-width: 32px !important;
            font-size: 0.7rem;
            padding: 0.28rem 0.08rem;
            text-align: center;
            flex: 0 0 32px !important;
        }
        
        .campaign-table .input-group button {
            padding: 0.28rem 0.3rem;
            font-size: 0.65rem;
            flex: 0 0 auto !important;
        }
        
        .campaign-table .input-group button i {
            font-size: 0.6rem;
        }
        
        /* Mobilde sepet butonunu göster */
        .mobile-cart-btn {
            display: block !important;
            flex-shrink: 0;
        }
        
        .mobile-cart-btn .btn {
            min-width: auto;
            padding: 0.28rem 0.38rem !important;
            font-size: 0.75rem !important;
        }
        
        .mobile-cart-btn .btn i {
            font-size: 0.72rem;
        }
        
        /* Desktop sepet butonunu gizle */
        .desktop-cart {
            display: none !important;
        }
        
        /* Modal header'ı küçült */
        #specialCampaignModal .modal-header {
            padding: 0.5rem 0.75rem !important;
        }
        
        #specialCampaignModal .modal-title {
            font-size: 0.85rem !important;
        }
        
        #specialCampaignModal .modal-title i {
            font-size: 0.7rem !important;
        }
        
        /* Resim önizlemesini mobilde devre dışı bırak */
        .desktop-only-hover:hover::after {
            display: none !important;
        }
        
        /* Table responsive ayarları */
        .table-responsive {
            border-radius: 0 !important;
        }
        
        /* Muadil ürünler için de aynı düzenlemeler */
        .list-view-table {
            font-size: 0.8rem;
            table-layout: fixed;
        }
        
        .list-view-table th {
            padding: 0.5rem 0.25rem !important;
            font-size: 0.7rem !important;
            white-space: nowrap;
        }
        
        .list-view-table td {
            padding: 0.5rem 0.25rem !important;
            font-size: 0.8rem !important;
            vertical-align: middle !important;
        }
        
        .list-view-table .badge {
            font-size: 0.7rem !important;
            padding: 0.2rem 0.35rem !important;
        }
        
        /* Muadil ürünler - Ürün adı 2. sütun - büyütüldü */
        .list-view-table thead th:nth-child(2) {
            width: 56% !important;
            text-align: left !important;
        }
        
        .list-view-table tbody td:nth-child(2) {
            width: 56% !important;
            word-wrap: break-word;
            white-space: normal;
            line-height: 1.4;
            padding: 0.6rem 0.4rem !important;
            text-align: left !important;
        }
        
        .list-view-table tbody td:nth-child(2) a {
            font-size: 0.85rem;
            line-height: 1.3;
            display: inline;
        }
        
        .list-view-table .badge.me-1 {
            margin-right: 0.05rem !important;
        }
        
        /* MF ve Fiyat birleşik - 6. sütun */
        .list-view-table thead th:nth-child(6) {
            width: 16% !important;
            text-align: center !important;
        }
        
        .list-view-table tbody td:nth-child(6) {
            width: 16% !important;
            text-align: center !important;
            padding: 0.5rem 0.2rem !important;
        }
        
        /* Miktar ve Sepet birleşik - 7. sütun - küçültüldü */
        .list-view-table thead th:nth-child(7) {
            width: 28% !important;
            text-align: center !important;
        }
        
        .list-view-table tbody td:nth-child(7) {
            width: 28% !important;
            padding: 0.5rem 0.2rem !important;
            text-align: center !important;
        }
        
        .list-view-table .input-group {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
        }
        
        .list-view-table .input-group input {
            max-width: 32px !important;
            min-width: 32px !important;
            flex: 0 0 32px !important;
        }
        
        .list-view-table .input-group button {
            flex: 0 0 auto !important;
        }
        
        /* Ana sayfa liste görünümü için özel ayarlar */
        .home-list-view-table {
            font-size: 0.85rem;
            margin-bottom: 0 !important;
            table-layout: fixed;
            width: 100%;
        }
        
        .home-list-view-table th {
            padding: 0.6rem 0.3rem !important;
            font-size: 0.75rem !important;
            white-space: nowrap;
            vertical-align: middle !important;
        }
        
        .home-list-view-table td {
            padding: 0.6rem 0.3rem !important;
            font-size: 0.8rem !important;
            vertical-align: middle !important;
        }
        
        /* Ana sayfa - Ürün adı sütunu - 2. sütun */
        .home-list-view-table thead th:nth-child(2) {
            width: 56% !important;
            text-align: left !important;
        }
        
        .home-list-view-table tbody td:nth-child(2) {
            width: 56% !important;
            word-wrap: break-word;
            white-space: normal;
            line-height: 1.4;
            padding: 0.6rem 0.4rem !important;
            text-align: left !important;
        }
        
        .home-list-view-table tbody td:nth-child(2) a {
            font-size: 0.85rem;
            display: inline;
            line-height: 1.3;
        }
        
        .home-list-view-table .badge.me-1 {
            margin-right: 0.05rem !important;
        }
        
        /* Ana sayfa - MF ve Fiyat birleşik - 6. sütun */
        .home-list-view-table thead th:nth-child(6) {
            width: 16% !important;
            text-align: center !important;
        }
        
        .home-list-view-table tbody td:nth-child(6) {
            width: 16% !important;
            text-align: center !important;
            padding: 0.5rem 0.2rem !important;
        }
        
        .home-list-view-table tbody td:nth-child(6) .mobile-price-mf {
            line-height: 1.2;
        }
        
        .home-list-view-table tbody td:nth-child(6) .mobile-price-mf strong {
            font-size: 0.75rem !important;
            display: block;
        }
        
        /* Ana sayfa - Miktar ve Sepet birleşik - 7. sütun */
        .home-list-view-table thead th:nth-child(7) {
            width: 28% !important;
            text-align: center !important;
        }
        
        .home-list-view-table tbody td:nth-child(7) {
            width: 28% !important;
            padding: 0.5rem 0.2rem !important;
            text-align: center !important;
        }
        
        .home-list-view-table .input-group {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
        }
        
        .home-list-view-table .input-group input {
            max-width: 32px !important;
            min-width: 32px !important;
            flex: 0 0 32px !important;
        }
        
        .home-list-view-table .input-group button {
            flex: 0 0 auto !important;
        }
    }
    
    /* Desktop için gizle */
    @media (min-width: 769px) {
        .show-on-mobile {
            display: none !important;
        }
        
        .mobile-price-mf {
            display: none !important;
        }
        
        .desktop-price {
            display: block !important;
        }
        
        .mobile-cart-btn {
            display: none !important;
        }
        
        .desktop-cart {
            display: block !important;
        }
        
        .qty-cart-container {
            display: block !important;
        }
    }
    
    /* Çok küçük ekranlar için (360px altı) */
    @media (max-width: 400px) {
        #specialCampaignModal .modal-dialog {
            margin: 0.15rem;
            max-width: calc(100% - 0.3rem);
        }
        
        .campaign-table {
            font-size: 0.75rem;
        }
        
        .campaign-table th {
            padding: 0.4rem 0.2rem !important;
            font-size: 0.65rem !important;
        }
        
        .campaign-table td {
            padding: 0.4rem 0.2rem !important;
            font-size: 0.75rem !important;
        }
        
        .campaign-table tbody td:nth-child(1) a {
            font-size: 0.75rem;
        }
        
        .campaign-table tbody td:nth-child(6) strong {
            font-size: 0.7rem !important;
        }
        
        .campaign-table .input-group input {
            max-width: 30px !important;
            min-width: 30px !important;
            font-size: 0.65rem;
            padding: 0.25rem 0.05rem;
            flex: 0 0 30px !important;
        }
        
        .campaign-table .input-group button {
            padding: 0.25rem 0.28rem;
            flex: 0 0 auto !important;
        }
        
        .campaign-table .input-group button i {
            font-size: 0.55rem;
        }
        
        .mobile-cart-btn .btn {
            padding: 0.25rem 0.35rem !important;
            font-size: 0.68rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Slider Section -->
    @if($sliders->count() > 0)
    <div class="slider-container">
        <div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($sliders as $index => $slider)
                    <button type="button" 
                            data-bs-target="#mainSlider" 
                            data-bs-slide-to="{{ $index }}" 
                            class="{{ $index === 0 ? 'active' : '' }}">
                    </button>
                @endforeach
            </div>
            
            <div class="carousel-inner">
                @foreach($sliders as $index => $slider)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ $slider->image_url }}" class="d-block w-100" alt="{{ $slider->title }}">
                        @if($slider->title || $slider->description)
                            <div class="carousel-caption d-none d-md-block">
                                @if($slider->title)
                                    <h5>{{ $slider->title }}</h5>
                                @endif
                                @if($slider->description)
                                    <p>{{ $slider->description }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
    @endif

    <!-- Search and View Toggle -->
    <div class="row mb-2">
        <div class="col-md-7">
            <form action="{{ route('search') }}" method="GET" class="d-flex search-form-wrapper" id="searchForm" onsubmit="clearSearchAfterSubmit()">
                <div class="search-input-group">
                    <input type="text" 
                           name="q" 
                           id="search-input"
                           class="form-control form-control-sm me-2" 
                           style="padding: 0.375rem 0.75rem;"
                           placeholder="Ürün ara..." 
                           value="{{ request('q') }}">
                    <input type="hidden" name="view" value="{{ $viewType }}">
                    <input type="hidden" name="stokta_olanlar" id="stokta_olanlar_hidden" value="{{ request('stokta_olanlar') ? '1' : '0' }}">
                    <input type="hidden" name="kampanyali" id="kampanyali_hidden" value="{{ request('kampanyali') ? '1' : '0' }}">
                    <button type="submit" class="btn btn-primary btn-sm search-button">
                        <i class="fas fa-search"></i> Ara
                    </button>
                </div>
                <div class="filter-checkboxes">
                    <div class="form-check d-flex align-items-center filter-item">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="stokta_olanlar_check" 
                               {{ request('stokta_olanlar') ? 'checked' : '' }}
                               onchange="toggleStoktaOlanlar()">
                        <label class="form-check-label ms-1 text-nowrap" for="stokta_olanlar_check" style="font-size: 0.85rem;">
                            Stokta Olanlar
                        </label>
                    </div>
                    <div class="form-check d-flex align-items-center filter-item">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="kampanyali_check" 
                               {{ request('kampanyali') ? 'checked' : '' }}
                               onchange="toggleKampanyali()"
                               style="border-color: #f59e0b;">
                        <label class="form-check-label ms-1 text-nowrap" for="kampanyali_check" style="font-size: 0.85rem; color: #d97706; font-weight: 500;">
                            <i class="fas fa-tag me-1"></i>Kampanyalı
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-5 text-end">
            <!-- Görünüm Seçimi -->
            <div class="btn-group" role="group">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'catalog']) }}" 
                   class="btn btn-outline-primary btn-sm {{ $viewType === 'catalog' ? 'active' : '' }}"
                   onclick="saveViewPreference('catalog')">
                    <i class="fas fa-th"></i> Katalog
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" 
                   class="btn btn-outline-primary btn-sm {{ $viewType === 'list' ? 'active' : '' }}"
                   onclick="saveViewPreference('list')">
                    <i class="fas fa-bars"></i> Liste
                </a>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    @if($viewType === 'catalog')
        <!-- Catalog View -->
        <div class="row">
            @forelse($products as $product)
                <div class="col-lg-3 col-md-4 col-6 mb-3">
                    <div class="card product-card h-100 {{ $product->ozel_liste ? 'campaign-product-card' : '' }}">
                        <div class="position-relative">
                            <!-- Kampanyalı Ürün Rozeti -->
                            @if($product->ozel_liste)
                                <span class="badge position-absolute m-2" 
                                      style="z-index: 11; top: 0; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #f59e0b, #d97706); color: white; font-size: 0.7rem; padding: 0.3rem 0.6rem; border-radius: 20px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4);">
                                    <i class="fas fa-tag me-1"></i>KAMPANYA
                                </span>
                            @endif
                            
                            <!-- Ürün Kodu Sol Üst -->
                            <span class="badge position-absolute top-0 start-0 m-2" 
                                  style="z-index: 10; background: rgba(255,255,255,0.95); color: #495057; font-size: 0.75rem; padding: 0.3rem 0.5rem; border: 1px solid rgba(0,0,0,0.1); font-weight: 600;">
                                {{ $product->urun_kodu }}
                            </span>
                            
                            <!-- Stok Göstergesi Sağ Üst -->
                            @if($product->bakiye > 0)
                                <span class="position-absolute top-0 end-0 m-2 badge" 
                                      title="Stokta Var"
                                      style="z-index: 10; background: #10b981; font-size: 0.65rem; padding: 0.25rem 0.4rem;">
                                    <i class="fas fa-check"></i>
                                </span>
                            @else
                                <span class="position-absolute top-0 end-0 m-2 badge" 
                                      title="Stokta Yok"
                                      style="z-index: 10; background: #ef4444; font-size: 0.65rem; padding: 0.25rem 0.4rem;">
                                    <i class="fas fa-times"></i>
                                </span>
                            @endif
                            
                            <a href="javascript:void(0)" onclick="showProductModal({{ $product->id }})">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" 
                                         class="card-img-top product-image" 
                                         alt="{{ $product->urun_adi }}"
                                         style="height: 200px; object-fit: cover; cursor: pointer;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center" 
                                         style="height: 200px; cursor: pointer; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border: 2px dashed #cbd5e0;">
                                        <div class="text-center p-3">
                                            <i class="fas fa-camera text-secondary mb-2" style="font-size: 3rem; opacity: 0.5;"></i>
                                            <p class="text-secondary mb-0 fw-bold" style="font-size: 0.85rem;">Resim Hazırlanıyor</p>
                                        </div>
                                    </div>
                                @endif
                            </a>
                        </div>
                        
                        <div class="card-body d-flex flex-column" style="padding: 0.75rem 0.875rem 0.875rem;">
                            <div class="mb-auto">
                                <h6 class="mb-2" style="font-size: 0.9rem; font-weight: 600; line-height: 1.3; min-height: 38px; color: #212529;">
                                    <a href="javascript:void(0)" onclick="showProductModal({{ $product->id }})" class="text-decoration-none text-dark">
                                        {{ $product->urun_adi }}
                                    </a>
                                </h6>
                            </div>
                            
                            <div class="mb-1 p-2 text-center" style="background: #f8f9fa; border-radius: 6px;">
                                <div class="row g-0">
                                    <div class="col-6">
                                        <small class="d-block text-muted mb-1" style="font-size: 0.65rem; font-weight: 600;">MAL FAZLASI</small>
                                        <strong class="d-block" style="font-size: 1.1rem; color: #f59e0b;">{{ $product->mf ?? '-' }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="d-block text-success fw-bold mb-1" style="font-size: 0.65rem;">NET FİYAT</small>
                                        @php 
                                            $netFiyat = $product->net_fiyat_manuel ?? $product->net_price ?? 0;
                                        @endphp
                                        <strong class="d-block text-success" style="font-size: 1.2rem;">{{ number_format($netFiyat, 2, ',', '.') }} ₺</strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-1">
                                <div class="input-group input-group-sm flex-grow-1">
                                    <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity({{ $product->id }})">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           id="qty-{{ $product->id }}" 
                                           class="form-control text-center fw-bold" 
                                           value="0" 
                                           min="0"
                                           style="font-size: 0.9rem;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity({{ $product->id }})">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <button type="button" 
                                        class="btn btn-success btn-sm" 
                                        id="add-btn-{{ $product->id }}"
                                        onclick="addProductToCart({{ $product->id }})"
                                        title="Sepete Ekle"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        style="padding: 0.25rem 0.6rem;">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Ürün bulunamadı.
                    </div>
                </div>
            @endforelse
        </div>
    @else
        <!-- List View -->
        <div class="table-responsive" style="border-radius: 8px; overflow: hidden;">
            <table class="table table-hover list-view-table home-list-view-table mb-0">
                <thead>
                    <tr>
                        <th class="hide-on-mobile text-center" style="width: 100px;">Ürün Kodu</th>
                        <th>Ürün Adı</th>
                        <th class="text-center hide-on-mobile" style="width: 90px;">Perakende Fiyatı</th>
                        <th class="text-center hide-on-mobile" style="width: 90px;">Depocu Fiyatı</th>
                        <th class="text-center hide-on-mobile" style="width: 80px;">Mal Fazlası</th>
                        <th class="text-center mobile-combined-cell" style="width: 100px;"><span class="hide-on-mobile">KDV Dahil Net Fiyat</span><span class="show-on-mobile">Fiyat</span></th>
                        <th class="text-center mobile-qty-cart" style="width: 150px;"><span class="hide-on-mobile">Miktar</span><span class="show-on-mobile">Miktar / Sepet</span></th>
                        <th class="hide-on-mobile" style="width: 60px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr id="product-row-{{ $product->id }}" class="{{ $product->ozel_liste ? 'campaign-product-row' : '' }}">
                            <td class="text-center hide-on-mobile">
                                @if($product->ozel_liste)
                                    <span class="badge" style="background: linear-gradient(135deg, #f59e0b, #d97706); font-size: 0.85rem; font-weight: 500;">
                                        {{ $product->urun_kodu }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border" style="font-size: 0.85rem; font-weight: 500;">
                                        {{ $product->urun_kodu }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="javascript:void(0)" onclick="showProductModal({{ $product->id }})" class="text-decoration-none">
                                    @if($product->bakiye > 0)
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
                                    @if($product->hasImage())
                                        <span class="product-name-with-image desktop-only-hover"
                                              onmouseenter="showImagePreview(event, '{{ $product->image_url }}')"
                                              onmouseleave="hideImagePreview()"
                                              style="cursor: pointer;">
                                            {{ $product->urun_adi }}
                                        </span>
                                    @else
                                        {{ $product->urun_adi }}
                                    @endif
                                </a>
                                @if($product->muadil_kodu)
                                    @php
                                        $muadilCount = \App\Models\Product::where('muadil_kodu', $product->muadil_kodu)
                                            ->where('is_active', true)
                                            ->where('id', '!=', $product->id)
                                            ->count();
                                    @endphp
                                    @if($muadilCount > 0)
                                        <i class="fas fa-sitemap muadil-icon hide-on-mobile" 
                                           onclick="toggleMuadilProducts({{ $product->id }}, '{{ $product->muadil_kodu }}')"
                                           title="Muadil Ürünler ({{ $muadilCount }})"
                                           style="cursor: pointer; color: #0d6efd; margin-left: 8px;">
                                        </i>
                                    @endif
                                @endif
                            </td>
                            <td class="text-end hide-on-mobile">
                                <span class="text-muted">{{ number_format($product->satis_fiyati, 2, ',', '.') }} ₺</span>
                            </td>
                            <td class="text-end hide-on-mobile">
                                @if($product->depocu_fiyati)
                                    <span class="text-muted">{{ number_format($product->depocu_fiyati, 2, ',', '.') }} ₺</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center hide-on-mobile">
                                @if($product->mf)
                                    <span class="badge bg-warning text-dark" style="font-size: 0.85rem;">{{ $product->mf }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end mobile-combined-cell">
                                @php
                                    $netFiyat = $product->net_fiyat_manuel ?? $product->net_price;
                                @endphp
                                <div class="desktop-price">
                                    <strong style="color: #198754; font-size: 1rem;">{{ number_format($netFiyat, 2, ',', '.') }} ₺</strong>
                                </div>
                                <div class="mobile-price-mf">
                                    @if($product->mf)
                                        <div style="font-size: 0.75rem; color: #666; margin-bottom: 2px;">
                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem; padding: 0.2rem 0.35rem;">{{ $product->mf }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <strong style="color: #198754; font-size: 0.85rem;">{{ number_format($netFiyat, 2, ',', '.') }} ₺</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="mobile-qty-cart-cell">
                                <div class="qty-cart-container">
                                    <div class="input-group input-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantityList({{ $product->id }})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" 
                                               id="qty-list-{{ $product->id }}" 
                                               class="form-control text-center" 
                                               value="0" 
                                               min="0"
                                               placeholder="Adet"
                                               onkeypress="if(event.key === 'Enter' && this.value > 0) { addProductToCartList({{ $product->id }}); }">
                                        <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantityList({{ $product->id }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="mobile-cart-btn">
                                        <button type="button" 
                                                class="btn btn-success btn-sm" 
                                                onclick="addProductToCartList({{ $product->id }})"
                                                title="Sepete Ekle"
                                                style="padding: 0.28rem 0.38rem; font-size: 0.75rem;">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td class="mobile-qty-cart-cell">
                                <div class="desktop-cart">
                                    <button type="button" 
                                            class="btn btn-primary btn-sm add-to-cart-btn" 
                                            id="add-btn-list-{{ $product->id }}"
                                            onclick="addProductToCartList({{ $product->id }})"
                                            title="Sepete Ekle"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Muadil ürünler için placeholder satır -->
                        <tr id="muadil-row-{{ $product->id }}" class="muadil-products-row" style="display: none;">
                            <td colspan="8" class="p-0">
                                <div id="muadil-content-{{ $product->id }}">
                                    <div class="text-center py-2">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Yükleniyor...</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle"></i> Ürün bulunamadı.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $products->appends(['view' => $viewType, 'q' => request('q')])->links() }}
    </div>
</div>

<!-- Image Preview Tooltip -->
<img id="imagePreview" class="image-preview" src="" alt="Preview">
@endsection

@push('scripts')
<script>
    // Görünüm tercihini kaydet
    function saveViewPreference(viewType) {
        localStorage.setItem('preferred_view', viewType);
    }
    
    // Sayfa yüklendiğinde görünüm tercihini kontrol et
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const currentView = urlParams.get('view');
        
        // Eğer view parametresi yoksa localStorage'dan al
        if (!currentView) {
            const preferredView = localStorage.getItem('preferred_view') || 'catalog';
            
            if (preferredView !== 'catalog') {
                urlParams.set('view', preferredView);
                window.location.search = urlParams.toString();
                return;
            }
        }
        
        // Mevcut görünüm tercihini kaydet
        if (currentView) {
            localStorage.setItem('preferred_view', currentView);
        }
    });

    // Arama sonrası input'u temizle
    function clearSearchAfterSubmit() {
        setTimeout(function() {
            document.getElementById('search-input').value = '';
        }, 100);
    }

    // Stokta Olanlar checkbox toggle
    function toggleStoktaOlanlar() {
        const checkbox = document.getElementById('stokta_olanlar_check');
        const hiddenInput = document.getElementById('stokta_olanlar_hidden');
        const searchInput = document.getElementById('search-input');
        const form = document.getElementById('searchForm');
        
        hiddenInput.value = checkbox.checked ? '1' : '0';
        
        // Eğer arama yapılmamışsa ana sayfaya git
        if (!searchInput.value || searchInput.value.trim() === '') {
            form.action = '{{ route('home') }}';
        } else {
            form.action = '{{ route('search') }}';
        }
        
        form.submit();
    }

    // Kampanyalı Ürünler checkbox toggle
    function toggleKampanyali() {
        const checkbox = document.getElementById('kampanyali_check');
        const hiddenInput = document.getElementById('kampanyali_hidden');
        const searchInput = document.getElementById('search-input');
        const form = document.getElementById('searchForm');
        
        hiddenInput.value = checkbox.checked ? '1' : '0';
        
        // Eğer arama yapılmamışsa ana sayfaya git
        if (!searchInput.value || searchInput.value.trim() === '') {
            form.action = '{{ route('home') }}';
        } else {
            form.action = '{{ route('search') }}';
        }
        
        form.submit();
    }

    // Tooltip'leri başlat
    function initTooltips() {
        // Mevcut tooltip'leri temizle
        var existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        existingTooltips.forEach(function(element) {
            var tooltip = bootstrap.Tooltip.getInstance(element);
            if (tooltip) {
                tooltip.dispose();
            }
        });
        
        // Yeni tooltip'leri başlat
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        initTooltips();
    });

    // Katalog görünümü - Miktar artırma/azaltma
    function increaseQuantity(productId) {
        const input = document.getElementById('qty-' + productId);
        input.value = parseInt(input.value || 0) + 1;
    }

    function decreaseQuantity(productId) {
        const input = document.getElementById('qty-' + productId);
        const currentValue = parseInt(input.value || 0);
        if (currentValue > 0) {
            input.value = currentValue - 1;
        }
    }

    // Liste görünümü - Miktar artırma/azaltma
    function increaseQuantityList(productId) {
        const input = document.getElementById('qty-list-' + productId);
        input.value = parseInt(input.value || 0) + 1;
    }

    function decreaseQuantityList(productId) {
        const input = document.getElementById('qty-list-' + productId);
        const currentValue = parseInt(input.value || 0);
        if (currentValue > 0) {
            input.value = currentValue - 1;
        }
    }

    function addProductToCart(productId) {
        const quantity = parseInt(document.getElementById('qty-' + productId).value) || 0;
        
        if (quantity === 0) {
            const button = document.getElementById('add-btn-' + productId);
            showWarningNotification(button, 'Lütfen miktar girin!');
            return;
        }
        
        const button = document.getElementById('add-btn-' + productId);
        addToCartWithMF(productId, quantity, 0, button);
    }

    // Liste görünümü için fonksiyonlar
    function addProductToCartList(productId) {
        const quantity = parseInt(document.getElementById('qty-list-' + productId).value) || 0;
        
        if (quantity === 0) {
            const button = document.getElementById('add-btn-list-' + productId);
            showWarningNotification(button, 'Lütfen miktar girin!');
            return;
        }
        
        const button = document.getElementById('add-btn-list-' + productId);
        addToCartWithMF(productId, quantity, 0, button);
    }

    // Muadil ürünleri göster/gizle
    let loadedMuadilProducts = {};
    
    function toggleMuadilProducts(productId, muadilKodu) {
        const muadilRow = $('#muadil-row-' + productId);
        const muadilContent = $('#muadil-content-' + productId);
        const cacheKey = muadilKodu + '_' + productId; // Cache key'i unique yap
        
        if (muadilRow.is(':visible')) {
            // Eğer görünüyorsa gizle
            muadilRow.hide();
            return;
        }
        
        // Eğer daha önce yüklenmediyse AJAX ile yükle
        if (!loadedMuadilProducts[cacheKey]) {
            muadilContent.html('<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>');
            muadilRow.show();
            
            $.ajax({
                url: '{{ url("/muadil-products") }}/' + muadilKodu + '?exclude=' + productId,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.products.length > 0) {
                        let html = '<table class="table table-hover list-view-table mb-0">';
                        html += '<thead><tr>';
                        html += '<th class="text-center" style="width: 100px;">Ürün Kodu</th>';
                        html += '<th>Ürün Adı</th>';
                        html += '<th class="text-center" style="width: 90px;">Perakende Fiyatı</th>';
                        html += '<th class="text-center" style="width: 90px;">Depocu Fiyatı</th>';
                        html += '<th class="text-center" style="width: 80px;">Mal Fazlası</th>';
                        html += '<th class="text-center" style="width: 100px;">KDV Dahil Net Fiyat</th>';
                        html += '<th class="text-center" style="width: 150px;">Miktar</th>';
                        html += '<th class="text-center" style="width: 60px;"></th>';
                        html += '</tr></thead><tbody>';
                        
                        response.products.forEach(function(product) {
                            // Kampanyalı ürün satırı
                            if (product.kampanyali) {
                                html += '<tr class="campaign-product-row">';
                            } else {
                                html += '<tr>';
                            }
                            html += '<td class="text-center" style="width: 100px;">';
                            // Kampanyalı ürün kodu turuncu badge
                            if (product.kampanyali) {
                                html += '<span class="badge" style="background: linear-gradient(135deg, #f59e0b, #d97706); font-size: 0.85rem; font-weight: 500;">';
                            } else {
                                html += '<span class="badge bg-light text-dark border" style="font-size: 0.85rem; font-weight: 500;">';
                            }
                            html += product.urun_kodu;
                            html += '</span>';
                            html += '</td>';
                            html += '<td>';
                            // Stok göstergesi
                            if (product.stokta) {
                                html += '<span class="badge me-1" ';
                                html += 'title="Stokta Var" ';
                                html += 'style="background: #10b981; font-size: 0.6rem; padding: 0.2rem 0.35rem;">';
                                html += '<i class="fas fa-check"></i></span>';
                            } else {
                                html += '<span class="badge me-1" ';
                                html += 'title="Stokta Yok" ';
                                html += 'style="background: #ef4444; font-size: 0.6rem; padding: 0.2rem 0.35rem;">';
                                html += '<i class="fas fa-times"></i></span>';
                            }
                            html += '<a href="javascript:void(0)" onclick="showProductModal(' + product.id + ')" class="text-decoration-none">';
                            if (product.image_url) {
                                html += '<span class="product-name-with-image" ';
                                html += 'onmouseenter="showImagePreview(event, \'' + product.image_url + '\')" ';
                                html += 'onmouseleave="hideImagePreview()" ';
                                html += 'style="cursor: pointer;">';
                                html += product.urun_adi;
                                html += '</span>';
                            } else {
                                html += product.urun_adi;
                            }
                            html += '</a>';
                            html += '</td>';
                            html += '<td class="text-end" style="width: 90px;"><span class="text-muted">' + product.satis_fiyati_formatted + '</span></td>';
                            html += '<td class="text-end" style="width: 90px;">';
                            if (product.depocu_fiyati_formatted) {
                                html += '<span class="text-muted">' + product.depocu_fiyati_formatted + '</span>';
                            } else {
                                html += '<span class="text-muted">-</span>';
                            }
                            html += '</td>';
                            html += '<td class="text-center" style="width: 80px;">';
                            if (product.mf) {
                                html += '<span class="badge bg-warning text-dark" style="font-size: 0.85rem;">' + product.mf + '</span>';
                            } else {
                                html += '<span class="text-muted">-</span>';
                            }
                            html += '</td>';
                            html += '<td class="text-end" style="width: 100px;"><strong style="color: #198754; font-size: 1rem;">' + product.net_fiyat_formatted + '</strong></td>';
                            html += '<td style="width: 140px;">';
                            html += '<div class="input-group input-group-sm">';
                            html += '<button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantityList(' + product.id + ')"><i class="fas fa-minus"></i></button>';
                            html += '<input type="number" id="qty-list-' + product.id + '" class="form-control text-center" value="0" min="0" placeholder="Adet">';
                            html += '<button type="button" class="btn btn-outline-secondary" onclick="increaseQuantityList(' + product.id + ')"><i class="fas fa-plus"></i></button>';
                            html += '</div>';
                            html += '</td>';
                            html += '<td class="text-center" style="width: 50px;">';
                            html += '<button type="button" class="btn btn-primary btn-sm add-to-cart-btn" id="add-btn-list-' + product.id + '" onclick="addProductToCartList(' + product.id + ')" title="Sepete Ekle" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-cart-plus"></i></button>';
                            html += '</td>';
                            html += '</tr>';
                        });
                        
                        html += '</tbody></table>';
                        muadilContent.html(html);
                        loadedMuadilProducts[cacheKey] = html;
                        
                        // Tooltip'leri yeniden başlat
                        setTimeout(function() {
                            initTooltips();
                        }, 100);
                    } else {
                        muadilContent.html('<div class="alert alert-info mb-0">Muadil ürün bulunamadı.</div>');
                    }
                },
                error: function() {
                    muadilContent.html('<div class="alert alert-danger mb-0">Hata oluştu.</div>');
                }
            });
        } else {
            // Daha önce yüklenmişse cache'den göster
            muadilContent.html(loadedMuadilProducts[cacheKey]);
            muadilRow.show();
            
            // Tooltip'leri yeniden başlat
            setTimeout(function() {
                initTooltips();
            }, 100);
        }
    }

    // Mal fazlası ile sepete ekleme
    function addToCartWithMF(productId, quantity, malFazlasi, buttonElement) {
        $.ajax({
            url: '{{ route("cart.add") }}',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                product_id: productId,
                quantity: quantity,
                mal_fazlasi: malFazlasi,
                product_campaign_id: null
            },
            success: function(response) {
                if (response.success) {
                    // Sepet sayısını ve tutarını güncelle
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                    
                    // Flying notification göster
                    if (buttonElement) {
                        showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                    } else {
                        showNotification(response.message, 'success');
                    }
                    
                    // Input'ları sıfırla
                    const qtyInput = document.getElementById('qty-' + productId);
                    const qtyListInput = document.getElementById('qty-list-' + productId);
                    
                    if (qtyInput) qtyInput.value = 0;
                    if (qtyListInput) qtyListInput.value = 0;
                }
            },
            error: function(xhr) {
                console.error('Add to cart error:', xhr);
                let message = 'Sepete eklenirken hata oluştu';
                
                if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
                    // Stok kontrolü hataları (400 Bad Request)
                    message = xhr.responseJSON.message;
                } else if (xhr.status === 419) {
                    message = 'CSRF Token hatası. Sayfayı yenileyin. (Hata: 419)';
                } else if (xhr.status === 404) {
                    message = 'Ürün bulunamadı. (Hata: 404)';
                } else if (xhr.status === 401) {
                    message = 'Giriş yapmanız gerekiyor. (Hata: 401)';
                } else if (xhr.status === 422) {
                    message = 'Geçersiz miktar değeri. (Hata: 422)';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        message += ' - ' + errors.join(', ');
                    }
                } else if (xhr.status === 500) {
                    message = 'Sunucu hatası. (Hata: 500)';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message + ' (Hata: ' + xhr.status + ')';
                } else {
                    message += ' (Status: ' + xhr.status + ')';
                }
                
                // Butonun üstünde göster (miktar 0 uyarısı gibi)
                if (buttonElement) {
                    showWarningNotification(buttonElement, message);
                } else {
                    showNotification(message, 'error');
                }
            }
        });
    }

    // Uyarı mesajı - butonun üzerinde
    function showWarningNotification(buttonElement, message) {
        const buttonRect = buttonElement.getBoundingClientRect();
        
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
        notification.style.maxWidth = '280px';
        notification.style.textAlign = 'center';
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease-out';
        
        document.body.appendChild(notification);
        
        // Genişliği aldıktan sonra pozisyonu ayarla (ortalamak için)
        const notificationWidth = notification.offsetWidth;
        const notificationHeight = notification.offsetHeight;
        notification.style.left = (buttonRect.left + buttonRect.width / 2 - notificationWidth / 2) + 'px';
        notification.style.top = (buttonRect.top - notificationHeight - 10) + 'px'; // Butonun 10px üstünde
        
        // Görünür yap - SABİT KALMALI, YUKARIYA GİTMEMELİ
        setTimeout(function() {
            notification.style.opacity = '1';
        }, 10);
        
        // Kaybol - YALNIZCA OPACITY, POZİSYON DEĞİŞMEZ
        setTimeout(function() {
            notification.style.opacity = '0';
        }, 2000); // 2 saniye bekle
        
        // Kaldır
        setTimeout(function() {
            notification.remove();
        }, 2500);
    }

    // Flying notification - butondan sepete animasyon
    function showFlyingNotification(buttonElement, message) {
        // Sepet linkinin pozisyonunu al
        const cartLink = document.querySelector('.navbar a[href*="cart"]');
        const buttonRect = buttonElement.getBoundingClientRect();
        const cartRect = cartLink ? cartLink.getBoundingClientRect() : null;
        
        // Mesaj elementi oluştur
        const notification = document.createElement('div');
        notification.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
        notification.style.position = 'fixed';
        notification.style.left = (buttonRect.left + buttonRect.width / 2 - 75) + 'px'; // Ortala
        notification.style.top = (buttonRect.top - 40) + 'px'; // Butonun üstünde
        notification.style.backgroundColor = '#28a745';
        notification.style.color = 'white';
        notification.style.padding = '10px 20px';
        notification.style.borderRadius = '25px';
        notification.style.fontSize = '1rem';
        notification.style.fontWeight = 'bold';
        notification.style.boxShadow = '0 4px 15px rgba(40, 167, 69, 0.6)';
        notification.style.zIndex = '9999';
        notification.style.transition = 'all 1.8s ease-in-out';
        notification.style.whiteSpace = 'nowrap';
        notification.style.opacity = '1';
        notification.style.transform = 'scale(1)';
        
        document.body.appendChild(notification);
        
        // 1.2 saniye bekle (kullanıcı mesajı okusun)
        setTimeout(function() {
            notification.style.transition = 'all 1.8s ease-in-out';
            if (cartRect) {
                // Sepet linkine doğru git
                notification.style.left = (cartRect.left + cartRect.width / 2 - 75) + 'px';
                notification.style.top = (cartRect.top - 10) + 'px';
                notification.style.opacity = '0';
                notification.style.transform = 'scale(0.2)';
            } else {
                // Sepet linki bulunamazsa yukarı git
                notification.style.top = '10px';
                notification.style.opacity = '0';
                notification.style.transform = 'scale(0.3)';
            }
        }, 1200);
        
        // Mesajı kaldır (toplam 3 saniye)
        setTimeout(function() {
            notification.remove();
        }, 3000);
    }

    function showImagePreview(event, imageUrl) {
        // Mobilde resim önizlemesini devre dışı bırak
        if (window.innerWidth <= 768) {
            return;
        }
        
        const preview = document.getElementById('imagePreview');
        preview.src = imageUrl;
        preview.style.display = 'block';
        
        // Mouse'un hemen yanında göster (fixed pozisyon - viewport bazlı)
        preview.style.left = (event.clientX + 20) + 'px';
        preview.style.top = (event.clientY - 50) + 'px';
    }

    function hideImagePreview() {
        // Mobilde resim önizlemesini devre dışı bırak
        if (window.innerWidth <= 768) {
            return;
        }
        
        const preview = document.getElementById('imagePreview');
        preview.style.display = 'none';
    }

    // Ürün Modal Göster
    // Ürün detay modalı kapandığında z-index'i sıfırla ve backdrop temizle
    $('#productModal').on('hidden.bs.modal', function () {
        $(this).css('z-index', '');
        
        // Ekstra backdrop'ları temizle
        $('.modal-backdrop').each(function(index, element) {
            if (index > 0) {
                $(element).remove();
            }
        });
        
        // Body'den modal-open class'ını kaldırma
        if ($('#specialCampaignModal').hasClass('show')) {
            $('body').addClass('modal-open');
        }
    });

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
        
        // Modal'ı en üste çıkar (diğer modalların üzerinde)
        $('#productModal').css('z-index', '1070');
        $('.modal-backdrop').last().css('z-index', '1065');
        
        // AJAX ile ürün detaylarını yükle
        $.ajax({
            url: '{{ url("/product") }}/' + productId + '/modal',
            method: 'GET',
            success: function(response) {
                $('#modalContent').html(response);
                
                // Tooltip'leri aktif et
                setTimeout(function() {
                    initTooltips();
                }, 100);
                
                // Modal'ı en üste scroll et
                $('#productModal .modal-body').scrollTop(0);
            },
            error: function(xhr) {
                $('#modalContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Ürün detayları yüklenirken hata oluştu!
                    </div>
                `);
            }
        });
    }

    // Modal içindeki ana ürün için miktar kontrolü
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

        // AJAX ile sepete ekle
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                quantity: quantity,
                mf_satis: 0
            },
            success: function(response) {
                if (response.success) {
                    // Sepet sayısını ve tutarını güncelle
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                    
                    // Başarı mesajı - butonun üzerinde
                    if (buttonElement) {
                        showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                    } else {
                        showNotification(response.message || 'Ürün sepete eklendi!', 'success');
                    }
                    
                    // Input'u sıfırla
                    document.getElementById('modal-quantity').value = 0;
                }
            },
            error: function(xhr) {
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

    // Muadil ürünler için miktar kontrolü
    function increaseMuadilQty(productId) {
        const input = document.getElementById('muadil-qty-' + productId);
        input.value = parseInt(input.value || 0) + 1;
    }

    function decreaseMuadilQty(productId) {
        const input = document.getElementById('muadil-qty-' + productId);
        const currentValue = parseInt(input.value || 0);
        if (currentValue > 0) {
            input.value = currentValue - 1;
        }
    }

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

        // AJAX ile sepete ekle
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                quantity: quantity,
                mf_satis: 0
            },
            success: function(response) {
                if (response.success) {
                    // Sepet sayısını ve tutarını güncelle
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                    
                    // Başarı mesajı - butonun üzerinde
                    if (buttonElement) {
                        showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                    } else {
                        showNotification(response.message || 'Ürün sepete eklendi!', 'success');
                    }
                    
                    // Miktarı sıfırla
                    document.getElementById('muadil-qty-' + productId).value = 0;
                }
            },
            error: function(xhr) {
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
</script>
@endpush

<!-- Ürün Detay Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0" id="modalContent">
                <!-- AJAX ile yüklenecek -->
            </div>
        </div>
    </div>
</div>

<!-- Özel Kampanya Modal -->
@if($specialCampaignProducts->count() > 0)
<div class="modal fade" id="specialCampaignModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border: 2px solid #f59e0b;">
            <div class="modal-header border-0 py-2" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #f59e0b 100%); box-shadow: 0 3px 10px rgba(245, 158, 11, 0.4);">
                <h5 class="modal-title text-white fw-bold mb-0 d-flex align-items-center" style="font-size: 1rem; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);">
                    <i class="fas fa-gift me-2" style="animation: bounce 1s ease-in-out infinite;"></i>
                    <span style="letter-spacing: 1px;">ÖZEL KAMPANYA</span>
                    <i class="fas fa-fire ms-2" style="animation: flicker 1.5s ease-in-out infinite;"></i>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2">
                <div class="table-responsive" style="border-radius: 8px; overflow: hidden;">
                    <table class="table table-hover campaign-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center hide-on-mobile" style="width: 100px;">Ürün Kodu</th>
                                <th>Ürün Adı</th>
                                <th class="text-center hide-on-mobile" style="width: 90px;">Perakende Fiyatı</th>
                                <th class="text-center hide-on-mobile" style="width: 90px;">Depocu Fiyatı</th>
                                <th class="text-center hide-on-mobile" style="width: 80px;">Mal Fazlası</th>
                                <th class="text-center mobile-combined-cell" style="width: 100px;"><span class="hide-on-mobile">Net Fiyat</span><span class="show-on-mobile">Fiyat</span></th>
                                <th class="text-center mobile-qty-cart" style="width: 120px;"><span class="hide-on-mobile">Miktar</span><span class="show-on-mobile">Miktar / Sepet</span></th>
                                <th class="hide-on-mobile" style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($specialCampaignProducts as $product)
                                <tr class="special-campaign-row" id="campaign-product-{{ $product->id }}">
                                    <td class="text-center hide-on-mobile">
                                        <span class="badge bg-light text-dark border" style="font-size: 0.85rem; font-weight: 500;">
                                            {{ $product->urun_kodu }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showProductModal({{ $product->id }})" class="text-decoration-none">
                                            @if($product->bakiye > 0)
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
                                            @if($product->hasImage())
                                                <span class="product-name-with-image desktop-only-hover"
                                                      onmouseenter="showImagePreview(event, '{{ $product->image_url }}')"
                                                      onmouseleave="hideImagePreview()"
                                                      style="cursor: pointer;">
                                                    {{ $product->urun_adi }}
                                                </span>
                                            @else
                                                {{ $product->urun_adi }}
                                            @endif
                                        </a>
                                        @if($product->muadil_kodu)
                                            @php
                                                $muadilCount = \App\Models\Product::where('muadil_kodu', $product->muadil_kodu)
                                                    ->where('is_active', true)
                                                    ->where('id', '!=', $product->id)
                                                    ->count();
                                            @endphp
                                            @if($muadilCount > 0)
                                                <i class="fas fa-sitemap muadil-icon hide-on-mobile" 
                                                   onclick="toggleCampaignMuadilProducts({{ $product->id }}, '{{ $product->muadil_kodu }}')"
                                                   title="Muadil Ürünler ({{ $muadilCount }})"
                                                   style="cursor: pointer; color: #f59e0b; margin-left: 8px;">
                                                </i>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-end hide-on-mobile">
                                        <span class="text-muted">{{ number_format($product->satis_fiyati, 2, ',', '.') }} ₺</span>
                                    </td>
                                    <td class="text-end hide-on-mobile">
                                        @if($product->depocu_fiyati)
                                            <span class="text-muted">{{ number_format($product->depocu_fiyati, 2, ',', '.') }} ₺</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center hide-on-mobile">
                                        @if($product->mf)
                                            <span class="badge bg-warning text-dark" style="font-size: 0.85rem;">{{ $product->mf }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end mobile-combined-cell">
                                        @php
                                            $netFiyat = $product->net_fiyat_manuel ?? $product->net_price;
                                        @endphp
                                        <div class="desktop-price">
                                            <strong style="color: #198754; font-size: 1rem;">{{ number_format($netFiyat, 2, ',', '.') }} ₺</strong>
                                        </div>
                                        <div class="mobile-price-mf">
                                            @if($product->mf)
                                                <div style="font-size: 0.75rem; color: #666; margin-bottom: 2px;">
                                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem; padding: 0.2rem 0.35rem;">{{ $product->mf }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <strong style="color: #198754; font-size: 0.85rem;">{{ number_format($netFiyat, 2, ',', '.') }} ₺</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="mobile-qty-cart-cell">
                                        <div class="qty-cart-container">
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-outline-secondary" onclick="decreaseCampaignQty({{ $product->id }})">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       id="campaign-qty-{{ $product->id }}" 
                                                       class="form-control text-center" 
                                                       value="0" 
                                                       min="0"
                                                       style="max-width: 80px;"
                                                       onkeypress="if(event.key === 'Enter' && this.value > 0) { addCampaignToCart({{ $product->id }}, this); }">
                                                <button type="button" class="btn btn-outline-secondary" onclick="increaseCampaignQty({{ $product->id }})">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <div class="mobile-cart-btn">
                                                <button type="button" 
                                                        class="btn btn-success btn-sm" 
                                                        onclick="addCampaignToCart({{ $product->id }}, this)"
                                                        title="Sepete Ekle"
                                                        style="padding: 0.25rem 0.4rem; font-size: 0.75rem;">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="mobile-qty-cart-cell">
                                        <div class="desktop-cart">
                                            <button type="button" 
                                                    class="btn btn-success btn-sm" 
                                                    onclick="addCampaignToCart({{ $product->id }}, this)"
                                                    title="Sepete Ekle"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    style="padding: 0.25rem 0.6rem;">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Muadil ürünler için placeholder satır -->
                                <tr id="campaign-muadil-row-{{ $product->id }}" class="muadil-products-row" style="display: none;">
                                    <td colspan="8" class="p-0">
                                        <div id="campaign-muadil-content-{{ $product->id }}">
                                            <div class="text-center py-2">
                                                <div class="spinner-border spinner-border-sm" role="status">
                                                    <span class="visually-hidden">Yükleniyor...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Sayfa yüklendiğinde Özel Kampanya modalını aç
    document.addEventListener('DOMContentLoaded', function() {
        @if($specialCampaignProducts->count() > 0)
            // URL'de show_campaign parametresi varsa her zaman göster
            const urlParams = new URLSearchParams(window.location.search);
            const forceShowCampaign = urlParams.get('show_campaign') === '1';
            
            // Session'da login flag'i varsa (her login'de göster)
            const showOnLogin = {{ ($showCampaignPopup ?? false) ? 'true' : 'false' }};
            
            if (forceShowCampaign || showOnLogin) {
                // URL parametresi veya login sonrası açıldıysa göster
                var campaignModal = new bootstrap.Modal(document.getElementById('specialCampaignModal'));
                campaignModal.show();
                logCampaignView();
                
                // URL'den parametreyi temizle (sayfa yenilenince tekrar açılmasın)
                if (forceShowCampaign) {
                    window.history.replaceState({}, document.title, window.location.pathname + window.location.hash);
                }
            }
        @endif
    });
    
    // Kampanya ürünleri için miktar artır/azalt
    function increaseCampaignQty(productId) {
        const input = document.getElementById('campaign-qty-' + productId);
        input.value = parseInt(input.value || 0) + 1;
    }

    function decreaseCampaignQty(productId) {
        const input = document.getElementById('campaign-qty-' + productId);
        const currentValue = parseInt(input.value || 0);
        if (currentValue > 0) {
            input.value = currentValue - 1;
        }
    }

    // Kampanya ürününü sepete ekle
    function addCampaignToCart(productId, buttonElement) {
        const quantity = parseInt(document.getElementById('campaign-qty-' + productId).value) || 0;
        
        if (quantity === 0) {
            if (buttonElement) {
                showWarningNotification(buttonElement, 'Lütfen miktar girin!');
            } else {
                showNotification('Lütfen miktar girin!', 'error');
            }
            return;
        }

        // AJAX ile sepete ekle
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                quantity: quantity,
                mf_satis: 0
            },
            success: function(response) {
                if (response.success) {
                    // Sepet sayısını ve tutarını güncelle
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                    
                    // Başarı mesajı - butonun üzerinde
                    if (buttonElement) {
                        showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                    } else {
                        showNotification(response.message || 'Ürün sepete eklendi!', 'success');
                    }
                    
                    // Miktarı sıfırla
                    document.getElementById('campaign-qty-' + productId).value = 0;
                }
            },
            error: function(xhr) {
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
    
    // Özel kampanya popup'ında muadil ürünleri göster/gizle
    let loadedCampaignMuadilProducts = {};
    
    function toggleCampaignMuadilProducts(productId, muadilKodu) {
        const muadilRow = $('#campaign-muadil-row-' + productId);
        const muadilContent = $('#campaign-muadil-content-' + productId);
        const cacheKey = muadilKodu + '_campaign_' + productId;
        
        if (muadilRow.is(':visible')) {
            // Eğer görünüyorsa gizle
            muadilRow.hide();
            return;
        }
        
        // Eğer daha önce yüklenmediyse AJAX ile yükle
        if (!loadedCampaignMuadilProducts[cacheKey]) {
            muadilContent.html('<div class="text-center py-2"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>');
            muadilRow.show();
            
            $.ajax({
                url: '{{ url("/muadil-products") }}/' + muadilKodu + '?exclude=' + productId,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.products.length > 0) {
                        let html = '<div class="p-2" style="background: #f8f9fa;"><table class="table table-hover list-view-table mb-0">';
                        html += '<thead><tr>';
                        html += '<th class="text-center hide-on-mobile" style="width: 100px;">Ürün Kodu</th>';
                        html += '<th>Ürün Adı</th>';
                        html += '<th class="text-center hide-on-mobile" style="width: 90px;">Perakende Fiyatı</th>';
                        html += '<th class="text-center hide-on-mobile" style="width: 90px;">Depocu Fiyatı</th>';
                        html += '<th class="text-center hide-on-mobile" style="width: 80px;">Mal Fazlası</th>';
                        html += '<th class="text-center mobile-combined-cell" style="width: 100px;"><span class="hide-on-mobile">Net Fiyat</span><span class="show-on-mobile">Fiyat</span></th>';
                        html += '<th class="text-center mobile-qty-cart" style="width: 120px;"><span class="hide-on-mobile">Miktar</span><span class="show-on-mobile">Miktar / Sepet</span></th>';
                        html += '<th class="text-center hide-on-mobile" style="width: 60px;"></th>';
                        html += '</tr></thead><tbody>';
                        
                        response.products.forEach(function(product) {
                            // Kampanyalı ürün satırı
                            if (product.kampanyali) {
                                html += '<tr class="campaign-product-row">';
                            } else {
                                html += '<tr>';
                            }
                            html += '<td class="text-center" style="width: 100px;">';
                            // Kampanyalı ürün kodu turuncu badge
                            if (product.kampanyali) {
                                html += '<span class="badge" style="background: linear-gradient(135deg, #f59e0b, #d97706); font-size: 0.85rem; font-weight: 500;">';
                            } else {
                                html += '<span class="badge bg-light text-dark border" style="font-size: 0.85rem; font-weight: 500;">';
                            }
                            html += product.urun_kodu;
                            html += '</span>';
                            html += '</td>';
                            html += '<td>';
                            html += '<a href="javascript:void(0)" onclick="showProductModal(' + product.id + ')" class="text-decoration-none">';
                            // Stok göstergesi - inline
                            if (product.stokta) {
                                html += '<span class="badge me-1" ';
                                html += 'title="Stokta Var" ';
                                html += 'style="background: #10b981; font-size: 0.6rem; padding: 0.2rem 0.35rem; vertical-align: middle;">';
                                html += '<i class="fas fa-check"></i></span>';
                            } else {
                                html += '<span class="badge me-1" ';
                                html += 'title="Stokta Yok" ';
                                html += 'style="background: #ef4444; font-size: 0.6rem; padding: 0.2rem 0.35rem; vertical-align: middle;">';
                                html += '<i class="fas fa-times"></i></span>';
                            }
                            if (product.image_url) {
                                html += '<span class="product-name-with-image" ';
                                html += 'onmouseenter="showImagePreview(event, \'' + product.image_url + '\')" ';
                                html += 'onmouseleave="hideImagePreview()" ';
                                html += 'style="cursor: pointer;">';
                                html += product.urun_adi;
                                html += '</span>';
                            } else {
                                html += product.urun_adi;
                            }
                            html += '</a>';
                            html += '</td>';
                            html += '<td class="text-end hide-on-mobile" style="width: 90px;"><span class="text-muted">' + product.satis_fiyati_formatted + '</span></td>';
                            html += '<td class="text-end hide-on-mobile" style="width: 90px;">';
                            if (product.depocu_fiyati_formatted) {
                                html += '<span class="text-muted">' + product.depocu_fiyati_formatted + '</span>';
                            } else {
                                html += '<span class="text-muted">-</span>';
                            }
                            html += '</td>';
                            html += '<td class="text-center hide-on-mobile" style="width: 80px;">';
                            if (product.mf) {
                                html += '<span class="badge bg-warning text-dark" style="font-size: 0.85rem;">' + product.mf + '</span>';
                            } else {
                                html += '<span class="text-muted">-</span>';
                            }
                            html += '</td>';
                            html += '<td class="text-end mobile-combined-cell" style="width: 100px;">';
                            html += '<div class="desktop-price"><strong style="color: #198754; font-size: 1rem;">' + product.net_fiyat_formatted + '</strong></div>';
                            html += '<div class="mobile-price-mf" style="text-align: center;">';
                            if (product.mf) {
                                html += '<div style="font-size: 0.75rem; color: #666; margin-bottom: 2px;">';
                                html += '<span class="badge bg-warning text-dark" style="font-size: 0.7rem; padding: 0.2rem 0.35rem;">' + product.mf + '</span>';
                                html += '</div>';
                            }
                            html += '<div><strong style="color: #198754; font-size: 0.85rem;">' + product.net_fiyat_formatted + '</strong></div>';
                            html += '</div>';
                            html += '</td>';
                            html += '<td class="mobile-qty-cart-cell" style="width: 150px;">';
                            html += '<div class="qty-cart-container">';
                            html += '<div class="input-group input-group-sm">';
                            html += '<button type="button" class="btn btn-outline-secondary" onclick="decreaseCampaignQty(' + product.id + ')"><i class="fas fa-minus"></i></button>';
                            html += '<input type="number" id="campaign-qty-' + product.id + '" class="form-control text-center" value="0" min="0" onkeypress="if(event.key === \'Enter\' && this.value > 0) { addCampaignProductToCart(' + product.id + ', this); }">';
                            html += '<button type="button" class="btn btn-outline-secondary" onclick="increaseCampaignQty(' + product.id + ')"><i class="fas fa-plus"></i></button>';
                            html += '</div>';
                            html += '<div class="mobile-cart-btn">';
                            html += '<button type="button" class="btn btn-success btn-sm" onclick="addCampaignProductToCart(' + product.id + ', this)" title="Sepete Ekle" style="padding: 0.25rem 0.4rem; font-size: 0.75rem;">';
                            html += '<i class="fas fa-cart-plus"></i>';
                            html += '</button>';
                            html += '</div>';
                            html += '</div>';
                            html += '</td>';
                            html += '<td class="mobile-qty-cart-cell">';
                            html += '<div class="desktop-cart">';
                            html += '<button type="button" class="btn btn-primary btn-sm add-to-cart-btn" id="add-btn-campaign-' + product.id + '" onclick="addCampaignProductToCart(' + product.id + ', document.getElementById(\'add-btn-campaign-' + product.id + '\'))" title="Sepete Ekle">';
                            html += '<i class="fas fa-cart-plus"></i>';
                            html += '</button>';
                            html += '</div>';
                            html += '</td>';
                            html += '</tr>';
                        });
                        
                        html += '</tbody></table></div>';
                        muadilContent.html(html);
                        loadedCampaignMuadilProducts[cacheKey] = true;
                    } else {
                        muadilContent.html('<div class="alert alert-info mb-0">Muadil ürün bulunamadı.</div>');
                    }
                },
                error: function() {
                    muadilContent.html('<div class="alert alert-danger mb-0">Muadil ürünler yüklenirken hata oluştu.</div>');
                }
            });
        } else {
            // Daha önce yüklenmişse sadece göster
            muadilRow.show();
        }
    }
    
    // Kampanya popup görüntüleme kaydı
    function logCampaignView() {
        @auth
        $.ajax({
            url: '{{ route("activity.campaign") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            }
        });
        @endauth
    }
</script>
@endif


