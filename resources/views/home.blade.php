@extends('layouts.app')

@section('title', 'Ana Sayfa - ' . (optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b')))

@push('styles')
    <style>
        /* ============================================
                                                                                   UNIFIED SEARCH & FILTER PANEL - MODERN DESIGN
                                                                                   ============================================ */
        .search-filter-panel {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.9) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 10px 18px;
            box-shadow:
                0 4px 24px rgba(0, 0, 0, 0.08),
                0 1px 2px rgba(0, 0, 0, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(226, 232, 240, 0.6);
            position: relative;
            overflow: hidden;
        }

        .search-filter-panel form {
            margin: 0;
            padding: 0;
        }

        .search-filter-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
        }

        [data-theme="dark"] .search-filter-panel {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.9) 100%);
            border-color: rgba(71, 85, 105, 0.4);
            box-shadow:
                0 4px 24px rgba(0, 0, 0, 0.4),
                0 1px 2px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        [data-theme="dark"] .search-filter-panel::before {
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        }

        .panel-content {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
        }

        /* ===== SEARCH BOX - PROMINENT DESIGN ===== */
        .search-box-wrapper {
            flex: 1;
            min-width: 0;
        }

        .search-input-container {
            display: flex;
            align-items: center;
            height: 46px;
            background: #ffffff;
            border: 2px solid #3b82f6;
            border-radius: 12px;
            padding: 0 8px 0 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-sizing: border-box;
            box-shadow:
                0 4px 12px rgba(59, 130, 246, 0.15),
                0 2px 4px rgba(0, 0, 0, 0.04);
        }

        [data-theme="dark"] .search-input-container {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #60a5fa;
            box-shadow:
                0 4px 12px rgba(96, 165, 250, 0.2),
                0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .search-input-container:hover {
            border-color: #2563eb;
            box-shadow:
                0 6px 16px rgba(59, 130, 246, 0.2),
                0 2px 4px rgba(0, 0, 0, 0.04);
            transform: translateY(-1px);
        }

        [data-theme="dark"] .search-input-container:hover {
            border-color: #93c5fd;
        }

        .search-input-container:focus-within {
            border-color: #3b82f6;
            box-shadow:
                0 0 0 4px rgba(59, 130, 246, 0.15),
                0 4px 16px rgba(59, 130, 246, 0.2);
            transform: translateY(-1px);
        }

        [data-theme="dark"] .search-input-container:focus-within {
            border-color: #60a5fa;
            box-shadow:
                0 0 0 4px rgba(96, 165, 250, 0.2),
                0 4px 16px rgba(96, 165, 250, 0.15);
        }

        .search-icon {
            color: #3b82f6;
            font-size: 1.15rem;
            margin-right: 12px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .search-input-container:focus-within .search-icon {
            color: #3b82f6;
            transform: scale(1.1);
        }

        [data-theme="dark"] .search-input-container:focus-within .search-icon {
            color: #60a5fa;
        }

        .search-input {
            flex: 1;
            border: none;
            background: transparent;
            height: 100%;
            font-size: 1rem;
            font-weight: 450;
            color: #1e293b;
            outline: none;
            min-width: 0;
            letter-spacing: -0.01em;
        }

        .search-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        [data-theme="dark"] .search-input {
            color: #f1f5f9;
        }

        [data-theme="dark"] .search-input::placeholder {
            color: #64748b;
        }

        .search-submit-btn {
            height: 38px;
            width: 38px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
        }

        .search-submit-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.5);
        }

        .search-submit-btn:active {
            transform: scale(0.98);
        }

        /* ===== DIVIDER - SUBTLE GRADIENT ===== */
        .panel-divider {
            width: 2px;
            height: 32px;
            background: linear-gradient(180deg, transparent, #e2e8f0, transparent);
            flex-shrink: 0;
            border-radius: 1px;
        }

        [data-theme="dark"] .panel-divider {
            background: linear-gradient(180deg, transparent, #475569, transparent);
        }

        /* ===== FILTER OPTIONS ===== */
        .filter-options {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .filter-chip {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 42px;
            padding: 0 14px;
            border-radius: 10px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1.5px solid #e2e8f0;
            color: #475569;
            font-size: 0.8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            gap: 6px;
            box-sizing: border-box;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        [data-theme="dark"] .filter-chip {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
            color: #94a3b8;
        }

        .filter-chip:hover {
            border-color: #cbd5e1;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        [data-theme="dark"] .filter-chip:hover {
            border-color: #475569;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .filter-chip.active {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-color: #3b82f6;
            color: #2563eb;
            box-shadow:
                0 0 0 3px rgba(59, 130, 246, 0.1),
                0 4px 12px rgba(59, 130, 246, 0.15);
        }

        [data-theme="dark"] .filter-chip.active {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(59, 130, 246, 0.1) 100%);
            border-color: #3b82f6;
            color: #60a5fa;
        }

        .filter-chip.campaign-chip.active {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-color: #f59e0b;
            color: #d97706;
            box-shadow:
                0 0 0 3px rgba(245, 158, 11, 0.1),
                0 4px 12px rgba(245, 158, 11, 0.15);
        }

        [data-theme="dark"] .filter-chip.campaign-chip.active {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2) 0%, rgba(245, 158, 11, 0.1) 100%);
            border-color: #f59e0b;
            color: #fbbf24;
        }

        .filter-chip input[type="checkbox"] {
            display: none;
        }

        .filter-chip i {
            font-size: 0.95rem;
            transition: transform 0.3s ease;
        }

        .filter-chip:hover i {
            transform: scale(1.1);
        }

        /* ===== VIEW TOGGLE - COMPACT PILL DESIGN ===== */
        .view-toggle {
            display: flex;
            align-items: center;
            height: 42px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border: 1.5px solid #e2e8f0;
            padding: 4px;
            border-radius: 10px;
            gap: 2px;
            flex-shrink: 0;
            box-sizing: border-box;
            margin: 0;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        [data-theme="dark"] .view-toggle {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .view-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 34px;
            padding: 0 12px;
            border-radius: 8px;
            color: #64748b;
            font-size: 0.8125rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            gap: 6px;
            box-sizing: border-box;
        }

        [data-theme="dark"] .view-btn {
            color: #94a3b8;
        }

        .view-btn:hover {
            color: #334155;
            background: rgba(255, 255, 255, 0.6);
        }

        [data-theme="dark"] .view-btn:hover {
            color: #e2e8f0;
            background: rgba(255, 255, 255, 0.08);
        }

        .view-btn.active {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            color: #3b82f6;
            box-shadow:
                0 2px 8px rgba(0, 0, 0, 0.08),
                0 1px 2px rgba(0, 0, 0, 0.04);
        }

        [data-theme="dark"] .view-btn.active {
            background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
            color: #60a5fa;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .view-btn i {
            transition: transform 0.3s ease;
        }

        .view-btn:hover i {
            transform: scale(1.1);
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 992px) {

            .filter-chip span,
            .view-btn .view-label {
                display: none;
            }

            .filter-chip,
            .view-btn {
                padding: 0 10px;
            }
        }

        @media (max-width: 768px) {
            .search-filter-panel {
                padding: 10px 12px;
                border-radius: 14px;
            }

            .panel-content {
                gap: 8px;
            }

            .search-input-container,
            .filter-chip,
            .view-toggle {
                height: 40px;
            }

            .view-btn {
                height: 32px;
            }

            .search-submit-btn {
                height: 32px;
                width: 32px;
            }

            .filter-chip,
            .view-btn {
                padding: 0 8px;
            }
        }

        @media (max-width: 576px) {
            .panel-divider {
                display: none;
            }

            .search-icon {
                display: none;
            }

            .search-input-container {
                padding-left: 12px;
            }
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
            .modal-product-image>div {
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

            .modal-product-main>div.col>div[style*="font-size: 0.9rem"] {
                font-size: 0.75rem !important;
                line-height: 1.4 !important;
            }

            .modal-product-main>div.col>div[style*="font-size: 0.9rem"] strong {
                font-size: 0.75rem !important;
            }

            /* Mal fazlası ve Net fiyat küçült */
            .modal-product-mf,
            .modal-product-price {
                width: 100% !important;
            }

            .modal-product-mf>div,
            .modal-product-price>div {
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

            .modal-product-cart>div {
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
            .product-card .card-body>div.mb-1 {
                padding: 0.4rem !important;
                font-size: 0.75rem;
            }

            .product-card .card-body>div.mb-1 small {
                font-size: 0.6rem !important;
            }

            .product-card .card-body>div.mb-1 strong {
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
            .row>[class*='col-'] {
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
                padding: 0.5rem 0.2rem !important;
                font-size: 0.7rem !important;
                white-space: nowrap;
                vertical-align: middle !important;
            }

            .home-list-view-table td {
                padding: 0.5rem 0.2rem !important;
                font-size: 0.75rem !important;
                vertical-align: middle !important;
            }

            /* Ana sayfa - Ürün adı sütunu - 2. sütun */
            .home-list-view-table thead th:nth-child(2) {
                width: 48% !important;
                text-align: left !important;
            }

            .home-list-view-table tbody td:nth-child(2) {
                width: 48% !important;
                word-wrap: break-word;
                white-space: normal;
                line-height: 1.3;
                padding: 0.4rem 0.3rem !important;
                text-align: left !important;
            }

            .home-list-view-table tbody td:nth-child(2) a {
                font-size: 0.75rem;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                line-height: 1.3;
            }

            .home-list-view-table .badge.me-1 {
                margin-right: 0.05rem !important;
                font-size: 0.5rem !important;
                padding: 0.1rem 0.15rem !important;
            }

            /* Ana sayfa - MF ve Fiyat birleşik - 6. sütun */
            .home-list-view-table thead th:nth-child(6) {
                width: 22% !important;
                text-align: right !important;
                padding-right: 0.3rem !important;
            }

            .home-list-view-table tbody td:nth-child(6) {
                width: 22% !important;
                text-align: right !important;
                padding: 0.3rem 0.2rem 0.3rem 0 !important;
            }

            .home-list-view-table tbody td:nth-child(6) .mobile-price-mf {
                line-height: 1.3;
                text-align: right !important;
            }

            .home-list-view-table .mobile-mf-row {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 3px;
                margin-bottom: 2px;
            }

            .home-list-view-table .mobile-mf-row:last-child {
                margin-bottom: 0;
            }

            .home-list-view-table .mobile-mf-radio {
                width: 11px !important;
                height: 11px !important;
                min-width: 11px !important;
                margin: 0 !important;
                flex-shrink: 0;
            }

            .home-list-view-table .mobile-mf-label {
                display: flex;
                align-items: center;
                gap: 3px;
                cursor: pointer;
                white-space: nowrap;
                margin: 0;
            }

            .home-list-view-table .mobile-mf-label .badge {
                font-size: 0.55rem !important;
                padding: 0.1rem 0.2rem !important;
            }

            .home-list-view-table .mobile-mf-label strong {
                font-size: 0.7rem !important;
                white-space: nowrap;
            }

            .home-list-view-table .mobile-single-price {
                text-align: right;
            }

            .home-list-view-table .mobile-single-price strong {
                color: #198754;
                font-size: 0.75rem !important;
                white-space: nowrap;
            }

            /* Ana sayfa - Miktar ve Sepet birleşik - 7. sütun */
            .home-list-view-table thead th:nth-child(7) {
                width: 30% !important;
                text-align: right !important;
                padding-right: 0.3rem !important;
            }

            .home-list-view-table tbody td:nth-child(7) {
                width: 30% !important;
                padding: 0.3rem 0.15rem 0.3rem 0 !important;
                text-align: right !important;
            }

            .home-list-view-table .qty-cart-container {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
                gap: 4px !important;
            }

            .home-list-view-table .input-group {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                width: auto !important;
                flex: 0 0 auto !important;
            }

            .home-list-view-table .input-group input {
                max-width: 26px !important;
                min-width: 26px !important;
                flex: 0 0 26px !important;
                font-size: 0.7rem !important;
                padding: 0.15rem 0.05rem !important;
            }

            .home-list-view-table .input-group button {
                flex: 0 0 auto !important;
                padding: 0.15rem 0.25rem !important;
                font-size: 0.55rem !important;
            }

            .home-list-view-table .mobile-cart-btn {
                flex: 0 0 auto !important;
            }

            .home-list-view-table .mobile-cart-btn .btn {
                padding: 0.2rem 0.35rem !important;
                font-size: 0.65rem !important;
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

        /* Top Selling Products Carousel */
        .top-selling-carousel-wrapper {
            overflow: hidden;
            position: relative;
            width: 100%;
        }

        .top-selling-carousel {
            display: flex;
            flex-direction: row;
            gap: 12px;
            animation: scrollLeft 25s linear infinite;
            width: max-content;
        }

        .top-selling-carousel:hover {
            animation-play-state: paused;
        }

        @keyframes scrollLeft {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .top-selling-card {
            display: flex;
            flex-direction: column;
            width: 140px;
            min-width: 140px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .top-selling-card:hover {
            background: #e9ecef;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        [data-theme="dark"] .top-selling-card {
            background: #2d3748;
        }

        [data-theme="dark"] .top-selling-card:hover {
            background: #3d4a5c;
        }

        .top-selling-image {
            width: 100%;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }

        .top-selling-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .top-selling-image .no-image {
            color: #adb5bd;
            font-size: 2rem;
        }

        .top-selling-image .stock-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.55rem;
            color: white;
        }

        .stock-badge.in-stock {
            background: #10b981;
        }

        .stock-badge.out-stock {
            background: #ef4444;
        }

        .top-selling-info {
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        .top-selling-info .product-name {
            font-size: 0.7rem;
            font-weight: 600;
            color: #1e3c72;
            line-height: 1.3;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 28px;
        }

        [data-theme="dark"] .top-selling-info .product-name {
            color: #93c5fd;
        }

        .top-selling-info .product-prices {
            display: flex;
            gap: 4px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 4px;
        }

        .top-selling-info .price-badge {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .top-selling-info .price-badge.price-1 {
            background: #d1fae5;
            color: #059669;
        }

        .top-selling-info .price-badge.price-2 {
            background: #dbeafe;
            color: #2563eb;
        }

        [data-theme="dark"] .top-selling-info .price-badge.price-1 {
            background: #065f46;
            color: #a7f3d0;
        }

        [data-theme="dark"] .top-selling-info .price-badge.price-2 {
            background: #1e40af;
            color: #bfdbfe;
        }

        .top-selling-info .product-mf {
            margin-top: 4px;
            display: flex;
            gap: 4px;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <!-- Slider + Top Selling Section -->
        <div class="row mb-3">
            <!-- Slider Section (Left - 7 columns) -->
            @if($sliders->count() > 0)
                <div class="col-lg-7 mb-3 mb-lg-0">
                    <div class="slider-container h-100">
                        <div id="mainSlider" class="carousel slide h-100" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                @foreach($sliders as $index => $slider)
                                    <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="{{ $index }}"
                                        class="{{ $index === 0 ? 'active' : '' }}">
                                    </button>
                                @endforeach
                            </div>

                            <div class="carousel-inner h-100">
                                @foreach($sliders as $index => $slider)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }} h-100">
                                        <img src="{{ $slider->image_url }}" class="d-block w-100 h-100" alt="{{ $slider->title }}"
                                            style="object-fit: cover;">
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

                            <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#mainSlider"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Top Selling Products Section (Right - 5 columns) -->
            <div class="col-lg-{{ $sliders->count() > 0 ? '5' : '12' }}">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header py-2" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                        <h6 class="mb-0 text-white fw-bold">
                            <i class="fas fa-fire text-warning me-2"></i>Son 7 Günün En Çok Satanları
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        @if(isset($topSellingProducts) && $topSellingProducts->count() > 0)
                            <div class="top-selling-carousel-wrapper">
                                <div class="top-selling-carousel" id="topSellingCarousel">
                                    @foreach($topSellingProducts as $topProduct)
                                        <div class="top-selling-card" onclick="showProductModal({{ $topProduct->id }})"
                                            style="cursor: pointer;">
                                            <div class="top-selling-image">
                                                @if($topProduct->image_url)
                                                    <img src="{{ $topProduct->image_url }}" alt="{{ $topProduct->urun_adi }}">
                                                @else
                                                    <div class="no-image">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                                @if($topProduct->bakiye > 0)
                                                    <span class="stock-badge in-stock"><i class="fas fa-check"></i></span>
                                                @else
                                                    <span class="stock-badge out-stock"><i class="fas fa-times"></i></span>
                                                @endif
                                            </div>
                                            <div class="top-selling-info">
                                                <div class="product-name" title="{{ $topProduct->urun_adi }}">
                                                    {{ $topProduct->urun_adi }}
                                                </div>
                                                <div class="product-prices">
                                                    @if($topProduct->net_fiyat1)
                                                        <span
                                                            class="price-badge price-1">{{ number_format($topProduct->net_fiyat1, 2, ',', '.') }}
                                                            ₺</span>
                                                    @endif
                                                    @if($topProduct->mf2 && $topProduct->net_fiyat2)
                                                        <span
                                                            class="price-badge price-2">{{ number_format($topProduct->net_fiyat2, 2, ',', '.') }}
                                                            ₺</span>
                                                    @elseif(!$topProduct->net_fiyat1 && $topProduct->net_price)
                                                        <span
                                                            class="price-badge price-1">{{ number_format($topProduct->net_price, 2, ',', '.') }}
                                                            ₺</span>
                                                    @endif
                                                </div>
                                                @if($topProduct->mf1)
                                                    <div class="product-mf">
                                                        <span class="badge bg-success"
                                                            style="font-size: 0.65rem;">{{ $topProduct->mf1 }}</span>
                                                        @if($topProduct->mf2)
                                                            <span class="badge bg-primary"
                                                                style="font-size: 0.65rem;">{{ $topProduct->mf2 }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <p class="mb-0">Henüz satış verisi yok</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Panel -->
        <div class="search-filter-panel mb-3">
            <form action="{{ route('search') }}" method="GET" id="searchForm" onsubmit="clearSearchAfterSubmit()">
                <div class="panel-content">
                    <!-- Search Box -->
                    <div class="search-box-wrapper">
                        <div class="search-input-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="q" id="search-input" class="search-input"
                                placeholder="Ürün adı, barkod veya kod ile ara..." value="{{ request('q') }}">
                            <button type="submit" class="search-submit-btn">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Filter Options -->
                    <div class="filter-options">
                        <label class="filter-chip {{ request('stokta_olanlar') ? 'active' : '' }}">
                            <input type="checkbox" id="stokta_olanlar_check" {{ request('stokta_olanlar') ? 'checked' : '' }} onchange="toggleStoktaOlanlar()">
                            <i class="fas fa-box-open"></i>
                            <span>Stokta Olanlar</span>
                        </label>
                        <label class="filter-chip campaign-chip {{ request('kampanyali') ? 'active' : '' }}">
                            <input type="checkbox" id="kampanyali_check" {{ request('kampanyali') ? 'checked' : '' }}
                                onchange="toggleKampanyali()">
                            <i class="fas fa-tag"></i>
                            <span>Kampanyalı</span>
                        </label>
                    </div>

                    <!-- View Toggle -->
                    <div class="view-toggle">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'catalog']) }}"
                            class="view-btn {{ $viewType === 'catalog' ? 'active' : '' }}"
                            onclick="saveViewPreference('catalog')" title="Katalog Görünümü">
                            <i class="fas fa-th-large"></i>
                            <span class="view-label">Katalog</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
                            class="view-btn {{ $viewType === 'list' ? 'active' : '' }}" onclick="saveViewPreference('list')"
                            title="Liste Görünümü">
                            <i class="fas fa-list"></i>
                            <span class="view-label">Liste</span>
                        </a>
                    </div>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="view" value="{{ $viewType }}">
                <input type="hidden" name="stokta_olanlar" id="stokta_olanlar_hidden"
                    value="{{ request('stokta_olanlar') ? '1' : '0' }}">
                <input type="hidden" name="kampanyali" id="kampanyali_hidden"
                    value="{{ request('kampanyali') ? '1' : '0' }}">
            </form>
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
                                    <span class="position-absolute top-0 end-0 m-2 badge" title="Stokta Var"
                                        style="z-index: 10; background: #10b981; font-size: 0.65rem; padding: 0.25rem 0.4rem;">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @else
                                    <span class="position-absolute top-0 end-0 m-2 badge" title="Stokta Yok"
                                        style="z-index: 10; background: #ef4444; font-size: 0.65rem; padding: 0.25rem 0.4rem;">
                                        <i class="fas fa-times"></i>
                                    </span>
                                @endif

                                <a href="javascript:void(0)" onclick="showProductModal({{ $product->id }})">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" class="card-img-top product-image"
                                            alt="{{ $product->urun_adi }}" style="height: 200px; object-fit: cover; cursor: pointer;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center"
                                            style="height: 200px; cursor: pointer; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border: 2px dashed #cbd5e0;">
                                            <div class="text-center p-3">
                                                <i class="fas fa-camera text-secondary mb-2" style="font-size: 3rem; opacity: 0.5;"></i>
                                                <p class="text-secondary mb-0 fw-bold" style="font-size: 0.85rem;">Resim Hazırlanıyor
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </a>
                            </div>

                            <div class="card-body d-flex flex-column" style="padding: 0.75rem 0.875rem 0.875rem;">
                                <div class="mb-auto">
                                    <h6 class="mb-2"
                                        style="font-size: 0.9rem; font-weight: 600; line-height: 1.3; min-height: 38px; color: #212529;">
                                        <a href="javascript:void(0)" onclick="showProductModal({{ $product->id }})"
                                            class="text-decoration-none text-dark">
                                            {{ $product->urun_adi }}
                                        </a>
                                    </h6>
                                </div>

                                <div class="mb-1 p-2" style="background: #f8f9fa; border-radius: 6px;">
                                    @if($product->mf1 || $product->mf2)
                                        <!-- Header Row -->
                                        <div class="d-flex mb-1" style="padding-left: 1.8rem;">
                                            <small class="text-muted fw-bold flex-grow-1" style="font-size: 0.6rem;">MAL FAZLASI</small>
                                            <small class="text-muted fw-bold" style="font-size: 0.6rem;">NET FİYAT</small>
                                        </div>
                                        @php
                                            // mf2'den minimum miktarı parse et (örn: "20+10" -> 20+10=30)
                                            $mf2MinQty = 0;
                                            if ($product->mf2 && str_contains($product->mf2, '+')) {
                                                $parts = explode('+', $product->mf2);
                                                $mf2MinQty = (int) trim($parts[0]) + (int) trim($parts[1]);
                                            }
                                        @endphp
                                        <!-- Opsiyon 1 -->
                                        @if($product->mf1)
                                            <div class="mb-1 py-1 px-2 rounded d-flex align-items-center" style="background: white;">
                                                @if($product->mf1 && $product->mf2)
                                                    <input class="form-check-input bonus-radio" type="radio"
                                                        name="bonus_option_{{ $product->id }}" id="bonus_{{ $product->id }}_1" value="1" checked
                                                        data-product-id="{{ $product->id }}" data-min-qty="0"
                                                        onchange="onBonusOption1Selected({{ $product->id }})"
                                                        style="margin: 0 0.4rem 0 0; flex-shrink: 0;">
                                                @endif
                                                <label class="d-flex justify-content-between align-items-center flex-grow-1"
                                                    for="bonus_{{ $product->id }}_1" style="font-size: 0.75rem; cursor: pointer;">
                                                    <span class="badge bg-success">{{ $product->mf1 }}</span>
                                                    <strong
                                                        class="text-success">{{ $product->net_fiyat1 ? number_format($product->net_fiyat1, 2, ',', '.') . ' ₺' : '-' }}</strong>
                                                </label>
                                            </div>
                                        @endif
                                        <!-- Opsiyon 2 -->
                                        @if($product->mf2)
                                            <div class="py-1 px-2 rounded d-flex align-items-center" style="background: white;">
                                                @if($product->mf1 && $product->mf2)
                                                    <input class="form-check-input bonus-radio" type="radio"
                                                        name="bonus_option_{{ $product->id }}" id="bonus_{{ $product->id }}_2" value="2"
                                                        data-product-id="{{ $product->id }}" data-min-qty="{{ $mf2MinQty }}"
                                                        onchange="onBonusOption2Selected({{ $product->id }}, {{ $mf2MinQty }})"
                                                        style="margin: 0 0.4rem 0 0; flex-shrink: 0;">
                                                @endif
                                                <label class="d-flex justify-content-between align-items-center flex-grow-1"
                                                    for="bonus_{{ $product->id }}_2" style="font-size: 0.75rem; cursor: pointer;">
                                                    <span class="badge bg-info">{{ $product->mf2 }}</span>
                                                    <strong
                                                        class="text-info">{{ $product->net_fiyat2 ? number_format($product->net_fiyat2, 2, ',', '.') . ' ₺' : '-' }}</strong>
                                                </label>
                                            </div>
                                        @endif
                                    @else
                                        <div class="row g-0 text-center">
                                            <div class="col-6">
                                                <small class="d-block text-muted mb-1" style="font-size: 0.65rem; font-weight: 600;">MAL
                                                    FAZLASI</small>
                                                <strong class="d-block" style="font-size: 1.1rem; color: #9ca3af;">-</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="d-block text-success fw-bold mb-1" style="font-size: 0.65rem;">NET
                                                    FİYAT</small>
                                                <strong class="d-block text-success"
                                                    style="font-size: 1.2rem;">{{ number_format($product->net_price, 2, ',', '.') }}
                                                    ₺</strong>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex gap-1">
                                    <div class="input-group input-group-sm flex-grow-1">
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="decreaseQuantity({{ $product->id }})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" id="qty-{{ $product->id }}"
                                            class="form-control text-center fw-bold qty-input" value="0" min="0"
                                            style="font-size: 0.9rem;" data-product-id="{{ $product->id }}"
                                            data-mf2bolunemez="{{ $product->mf2bolunemez ? '1' : '0' }}"
                                            data-mf2-step="{{ $mf2MinQty }}"
                                            oninput="checkBonusOptionOnQtyChange({{ $product->id }})"
                                            onblur="roundMf2Quantity({{ $product->id }})">
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="increaseQuantity({{ $product->id }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm" id="add-btn-{{ $product->id }}"
                                        onclick="addProductToCart({{ $product->id }})" title="Sepete Ekle" data-bs-toggle="tooltip"
                                        data-bs-placement="top" style="padding: 0.25rem 0.6rem;">
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
                            <th class="text-center mobile-combined-cell" style="width: 100px;"><span class="hide-on-mobile">KDV
                                    Dahil Net Fiyat</span><span class="show-on-mobile">Fiyat</span></th>
                            <th class="text-center mobile-qty-cart" style="width: 150px;"><span
                                    class="hide-on-mobile">Miktar</span><span class="show-on-mobile">Miktar / Sepet</span></th>
                            <th class="hide-on-mobile" style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr id="product-row-{{ $product->id }}"
                                class="{{ $product->ozel_liste ? 'campaign-product-row' : '' }}">
                                <td class="text-center hide-on-mobile">
                                    @if($product->ozel_liste)
                                        <span class="badge"
                                            style="background: linear-gradient(135deg, #f59e0b, #d97706); font-size: 0.85rem; font-weight: 500;">
                                            {{ $product->urun_kodu }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark border" style="font-size: 0.85rem; font-weight: 500;">
                                            {{ $product->urun_kodu }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="showProductModal({{ $product->id }})"
                                        class="text-decoration-none">
                                        @if($product->bakiye > 0)
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
                                        @if($product->hasImage())
                                            <span class="product-name-with-image desktop-only-hover"
                                                onmouseenter="showImagePreview(event, '{{ $product->image_url }}')"
                                                onmouseleave="hideImagePreview()" style="cursor: pointer;">
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
                                    @php
                                        // mf2'den minimum miktarı parse et (örn: "20+10" -> 20+10=30)
                                        $mf2MinQtyList = 0;
                                        if ($product->mf2 && str_contains($product->mf2, '+')) {
                                            $partsL = explode('+', $product->mf2);
                                            $mf2MinQtyList = (int) trim($partsL[0]) + (int) trim($partsL[1]);
                                        }
                                    @endphp
                                    @if($product->mf1 || $product->mf2)
                                        <div style="font-size: 0.8rem;">
                                            @if($product->mf1)
                                                <div class="d-flex align-items-center justify-content-center mb-1">
                                                    @if($product->mf1 && $product->mf2)
                                                        <input class="form-check-input me-1" type="radio"
                                                            name="bonus_option_list_{{ $product->id }}" id="bonus_list_{{ $product->id }}_1"
                                                            value="1" checked data-product-id="{{ $product->id }}" data-min-qty="0"
                                                            onchange="onBonusOption1SelectedList({{ $product->id }})" style="margin: 0;">
                                                    @endif
                                                    <label for="bonus_list_{{ $product->id }}_1" style="cursor: pointer;">
                                                        <span class="badge bg-success"
                                                            style="font-size: 0.75rem;">{{ $product->mf1 }}</span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if($product->mf2)
                                                <div class="d-flex align-items-center justify-content-center">
                                                    @if($product->mf1 && $product->mf2)
                                                        <input class="form-check-input me-1" type="radio"
                                                            name="bonus_option_list_{{ $product->id }}" id="bonus_list_{{ $product->id }}_2"
                                                            value="2" data-product-id="{{ $product->id }}" data-min-qty="{{ $mf2MinQtyList }}"
                                                            onchange="onBonusOption2SelectedList({{ $product->id }}, {{ $mf2MinQtyList }})"
                                                            style="margin: 0;">
                                                    @endif
                                                    <label for="bonus_list_{{ $product->id }}_2" style="cursor: pointer;">
                                                        <span class="badge bg-primary"
                                                            style="font-size: 0.75rem;">{{ $product->mf2 }}</span>
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end mobile-combined-cell">
                                    @if($product->mf1 || $product->mf2)
                                        <div class="desktop-price" style="font-size: 0.85rem;">
                                            @if($product->mf1)
                                                <div class="mb-1">
                                                    <strong
                                                        class="text-success">{{ $product->net_fiyat1 ? number_format($product->net_fiyat1, 2, ',', '.') . ' ₺' : '-' }}</strong>
                                                </div>
                                            @endif
                                            @if($product->mf2)
                                                <div>
                                                    <strong
                                                        class="text-primary">{{ $product->net_fiyat2 ? number_format($product->net_fiyat2, 2, ',', '.') . ' ₺' : '-' }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="desktop-price">
                                            <strong
                                                style="color: #198754; font-size: 1rem;">{{ number_format($product->net_price, 2, ',', '.') }}
                                                ₺</strong>
                                        </div>
                                    @endif
                                    <div class="mobile-price-mf">
                                        @if($product->mf1)
                                            <div class="mobile-mf-row">
                                                @if($product->mf1 && $product->mf2)
                                                    <input class="form-check-input mobile-mf-radio" type="radio"
                                                        name="bonus_option_mobile_{{ $product->id }}" id="bonus_mobile_{{ $product->id }}_1"
                                                        value="1" checked data-product-id="{{ $product->id }}" data-min-qty="0"
                                                        onchange="onBonusOption1SelectedList({{ $product->id }})">
                                                @endif
                                                <label for="bonus_mobile_{{ $product->id }}_1" class="mobile-mf-label">
                                                    <span class="badge bg-success">{{ $product->mf1 }}</span>
                                                    <strong
                                                        class="text-success">{{ $product->net_fiyat1 ? number_format($product->net_fiyat1, 2, ',', '.') . '₺' : '-' }}</strong>
                                                </label>
                                            </div>
                                        @endif
                                        @if($product->mf2)
                                            <div class="mobile-mf-row">
                                                @if($product->mf1 && $product->mf2)
                                                    <input class="form-check-input mobile-mf-radio" type="radio"
                                                        name="bonus_option_mobile_{{ $product->id }}" id="bonus_mobile_{{ $product->id }}_2"
                                                        value="2" data-product-id="{{ $product->id }}" data-min-qty="{{ $mf2MinQtyList }}"
                                                        onchange="onBonusOption2SelectedList({{ $product->id }}, {{ $mf2MinQtyList }})">
                                                @endif
                                                <label for="bonus_mobile_{{ $product->id }}_2" class="mobile-mf-label">
                                                    <span class="badge bg-primary">{{ $product->mf2 }}</span>
                                                    <strong
                                                        class="text-primary">{{ $product->net_fiyat2 ? number_format($product->net_fiyat2, 2, ',', '.') . '₺' : '-' }}</strong>
                                                </label>
                                            </div>
                                        @endif
                                        @if(!$product->mf1 && !$product->mf2)
                                            <div class="mobile-single-price">
                                                <strong>{{ number_format($product->net_price, 2, ',', '.') }}₺</strong>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="mobile-qty-cart-cell">
                                    <div class="qty-cart-container">
                                        <div class="input-group input-group-sm">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="decreaseQuantityList({{ $product->id }})">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" id="qty-list-{{ $product->id }}" class="form-control text-center"
                                                value="0" min="0" placeholder="Adet"
                                                data-mf2bolunemez="{{ $product->mf2bolunemez ? '1' : '0' }}"
                                                data-mf2-step="{{ $mf2MinQtyList }}"
                                                oninput="checkBonusOptionOnQtyChangeList({{ $product->id }})"
                                                onblur="roundMf2QuantityList({{ $product->id }})"
                                                onkeypress="if(event.key === 'Enter' && this.value > 0) { addProductToCartList({{ $product->id }}); }">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="increaseQuantityList({{ $product->id }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="mobile-cart-btn">
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="addProductToCartList({{ $product->id }})" title="Sepete Ekle"
                                                style="padding: 0.28rem 0.38rem; font-size: 0.75rem;">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="mobile-qty-cart-cell">
                                    <div class="desktop-cart">
                                        <button type="button" class="btn btn-primary btn-sm add-to-cart-btn"
                                            id="add-btn-list-{{ $product->id }}" onclick="addProductToCartList({{ $product->id }})"
                                            title="Sepete Ekle" data-bs-toggle="tooltip" data-bs-placement="top">
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
        document.addEventListener('DOMContentLoaded', function () {
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
            setTimeout(function () {
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
            existingTooltips.forEach(function (element) {
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

        document.addEventListener('DOMContentLoaded', function () {
            initTooltips();
        });

        // Katalog görünümü - Miktar artırma/azaltma (MF2Utils kullanır)
        function increaseQuantity(productId) {
            const input = document.getElementById('qty-' + productId);
            MF2Utils.increaseQuantity(input);
            checkBonusOptionOnQtyChange(productId);
        }

        function decreaseQuantity(productId) {
            const input = document.getElementById('qty-' + productId);
            MF2Utils.decreaseQuantity(input, 0);
            checkBonusOptionOnQtyChange(productId);
        }

        // Katalog görünümü - Blur'da bonus opsiyonu kontrol et (yuvarlama yapılmıyor)
        function roundMf2Quantity(productId) {
            // Yuvarlama yapılmıyor, sadece radio kontrolü
            checkBonusOptionOnQtyChange(productId);
        }

        // Liste görünümü - Miktar artırma/azaltma (MF2Utils kullanır)
        function increaseQuantityList(productId) {
            const input = document.getElementById('qty-list-' + productId);
            MF2Utils.increaseQuantity(input);
            checkBonusOptionOnQtyChangeList(productId);
        }

        function decreaseQuantityList(productId) {
            const input = document.getElementById('qty-list-' + productId);
            MF2Utils.decreaseQuantity(input, 0);
            checkBonusOptionOnQtyChangeList(productId);
        }

        // Liste görünümü - Bonus opsiyon 1 seçildiğinde miktarı 1 yap
        function onBonusOption1SelectedList(productId) {
            const qtyInput = document.getElementById('qty-list-' + productId);
            qtyInput.value = 1;
        }

        // Liste görünümü - Bonus opsiyon 2 seçildiğinde minimum miktarı ayarla
        function onBonusOption2SelectedList(productId, minQty) {
            const qtyInput = document.getElementById('qty-list-' + productId);
            // Her zaman minimum miktarı ayarla
            qtyInput.value = minQty;
        }

        // Liste görünümü - Miktar değiştiğinde bonus opsiyonu kontrol et
        function checkBonusOptionOnQtyChangeList(productId) {
            const qtyInput = document.getElementById('qty-list-' + productId);
            const currentQty = parseInt(qtyInput.value) || 0;

            // Desktop liste görünümü radio butonları
            const option2Radio = document.getElementById('bonus_list_' + productId + '_2');
            const option1Radio = document.getElementById('bonus_list_' + productId + '_1');

            // Mobil radio butonları
            const mobileOption2Radio = document.getElementById('bonus_mobile_' + productId + '_2');
            const mobileOption1Radio = document.getElementById('bonus_mobile_' + productId + '_1');

            // MF2Utils ile bonus opsiyonu kontrol et (desktop + mobil)
            MF2Utils.checkBonusOption(currentQty, option1Radio, option2Radio);
            MF2Utils.checkBonusOption(currentQty, mobileOption1Radio, mobileOption2Radio);
        }

        // Liste görünümü - Blur'da bonus opsiyonu kontrol et (yuvarlama yapılmıyor)
        function roundMf2QuantityList(productId) {
            // Yuvarlama yapılmıyor, sadece radio kontrolü
            checkBonusOptionOnQtyChangeList(productId);
        }

        // Bonus opsiyon 1 seçildiğinde miktarı 1 yap
        function onBonusOption1Selected(productId) {
            const qtyInput = document.getElementById('qty-' + productId);
            qtyInput.value = 1;
        }

        // Bonus opsiyon 2 seçildiğinde minimum miktarı ayarla
        function onBonusOption2Selected(productId, minQty) {
            const qtyInput = document.getElementById('qty-' + productId);
            // Her zaman minimum miktarı ayarla
            qtyInput.value = minQty;
        }

        // Miktar değiştiğinde bonus opsiyonu kontrol et
        function checkBonusOptionOnQtyChange(productId) {
            const qtyInput = document.getElementById('qty-' + productId);
            const currentQty = parseInt(qtyInput.value) || 0;

            // Opsiyon radio butonlarını bul
            const option2Radio = document.getElementById('bonus_' + productId + '_2');
            const option1Radio = document.getElementById('bonus_' + productId + '_1');

            // Eğer opsiyon 2 varsa
            if (option2Radio) {
                const minQty = parseInt(option2Radio.dataset.minQty) || 0;

                if (currentQty >= minQty && minQty > 0) {
                    // Miktar minimum miktara eşit veya fazlaysa opsiyon 2'yi seç
                    option2Radio.checked = true;
                } else if (option1Radio) {
                    // Miktar minimumun altındaysa opsiyon 1'e geç
                    option1Radio.checked = true;
                }
            }
        }

        function addProductToCart(productId) {
            const quantity = parseInt(document.getElementById('qty-' + productId).value) || 0;

            if (quantity === 0) {
                const button = document.getElementById('add-btn-' + productId);
                showWarningNotification(button, 'Lütfen miktar girin!');
                return;
            }

            // Seçili bonus opsiyonunu al
            const selectedBonus = document.querySelector('input[name="bonus_option_' + productId + '"]:checked');
            const bonusOption = selectedBonus ? parseInt(selectedBonus.value) : null;

            const button = document.getElementById('add-btn-' + productId);
            addToCartWithMF(productId, quantity, bonusOption, button);
        }

        // Liste görünümü için fonksiyonlar
        function addProductToCartList(productId) {
            const quantity = parseInt(document.getElementById('qty-list-' + productId).value) || 0;

            if (quantity === 0) {
                const button = document.getElementById('add-btn-list-' + productId);
                showWarningNotification(button, 'Lütfen miktar girin!');
                return;
            }

            // Seçili bonus opsiyonunu al (liste görünümünde de olabilir)
            const selectedBonus = document.querySelector('input[name="bonus_option_list_' + productId + '"]:checked');
            const bonusOption = selectedBonus ? parseInt(selectedBonus.value) : null;

            const button = document.getElementById('add-btn-list-' + productId);
            addToCartWithMF(productId, quantity, bonusOption, button);
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
                    success: function (response) {
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

                            response.products.forEach(function (product) {
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
                                if (product.mf1 || product.mf2) {
                                    html += '<div style="font-size: 0.85rem;">';
                                    // Parse mf2 min qty - artık toplam (10+5=15)
                                    var mf2MinQtyMuadil = 0;
                                    if (product.mf2 && product.mf2.includes('+')) {
                                        var parts = product.mf2.split('+');
                                        mf2MinQtyMuadil = (parseInt(parts[0].trim()) || 0) + (parseInt(parts[1].trim()) || 0);
                                    }
                                    if (product.mf1) {
                                        html += '<div class="d-flex align-items-center justify-content-center mb-1">';
                                        // Radio sadece ikisi de varsa göster
                                        if (product.mf1 && product.mf2) {
                                            html += '<input class="form-check-input me-1" type="radio" name="muadil_list_bonus_' + product.id + '" id="muadil_list_bonus_' + product.id + '_1" value="1" checked data-min-qty="0" onchange="onMuadilListBonusOption1Selected(' + product.id + ')" style="margin: 0;">';
                                        }
                                        html += '<label for="muadil_list_bonus_' + product.id + '_1" style="cursor: pointer;"><span class="badge bg-success" style="font-size: 0.8rem;">' + product.mf1 + '</span></label>';
                                        html += '</div>';
                                    }
                                    if (product.mf2) {
                                        html += '<div class="d-flex align-items-center justify-content-center">';
                                        // Radio sadece ikisi de varsa göster
                                        if (product.mf1 && product.mf2) {
                                            html += '<input class="form-check-input me-1" type="radio" name="muadil_list_bonus_' + product.id + '" id="muadil_list_bonus_' + product.id + '_2" value="2" data-min-qty="' + mf2MinQtyMuadil + '" onchange="onMuadilListBonusOption2Selected(' + product.id + ', ' + mf2MinQtyMuadil + ')" style="margin: 0;">';
                                        }
                                        html += '<label for="muadil_list_bonus_' + product.id + '_2" style="cursor: pointer;"><span class="badge bg-primary" style="font-size: 0.8rem;">' + product.mf2 + '</span></label>';
                                        html += '</div>';
                                    }
                                    html += '</div>';
                                } else {
                                    html += '<span class="text-muted">-</span>';
                                }
                                html += '</td>';
                                html += '<td class="text-end" style="width: 100px;">';
                                if (product.mf1 || product.mf2) {
                                    html += '<div style="font-size: 0.85rem;">';
                                    if (product.mf1) {
                                        html += '<div class="mb-1"><strong class="text-success">' + (product.net_fiyat1_formatted || '-') + '</strong></div>';
                                    }
                                    if (product.mf2) {
                                        html += '<div><strong class="text-primary">' + (product.net_fiyat2_formatted || '-') + '</strong></div>';
                                    }
                                    html += '</div>';
                                } else {
                                    html += '<strong style="color: #198754; font-size: 1rem;">' + product.net_fiyat_formatted + '</strong>';
                                }
                                html += '</td>';
                                html += '<td style="width: 140px;">';
                                html += '<div class="input-group input-group-sm">';
                                html += '<button type="button" class="btn btn-outline-secondary" onclick="decreaseMuadilListQty(' + product.id + ')"><i class="fas fa-minus"></i></button>';
                                html += '<input type="number" id="muadil-list-qty-' + product.id + '" class="form-control text-center" value="0" min="0" placeholder="Adet" oninput="checkMuadilListBonusOptionOnQtyChange(' + product.id + ')" onkeypress="if(event.key === \'Enter\' && this.value > 0) { addMuadilListToCart(' + product.id + ', this); }">';
                                html += '<button type="button" class="btn btn-outline-secondary" onclick="increaseMuadilListQty(' + product.id + ')"><i class="fas fa-plus"></i></button>';
                                html += '</div>';
                                html += '</td>';
                                html += '<td class="text-center" style="width: 50px;">';
                                html += '<button type="button" class="btn btn-primary btn-sm add-to-cart-btn" id="add-btn-muadil-list-' + product.id + '" onclick="addMuadilListToCart(' + product.id + ', document.getElementById(\'add-btn-muadil-list-' + product.id + '\'))" title="Sepete Ekle" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-cart-plus"></i></button>';
                                html += '</td>';
                                html += '</tr>';
                            });

                            html += '</tbody></table>';
                            muadilContent.html(html);
                            loadedMuadilProducts[cacheKey] = html;

                            // Tooltip'leri yeniden başlat
                            setTimeout(function () {
                                initTooltips();
                            }, 100);
                        } else {
                            muadilContent.html('<div class="alert alert-info mb-0">Muadil ürün bulunamadı.</div>');
                        }
                    },
                    error: function () {
                        muadilContent.html('<div class="alert alert-danger mb-0">Hata oluştu.</div>');
                    }
                });
            } else {
                // Daha önce yüklenmişse cache'den göster
                muadilContent.html(loadedMuadilProducts[cacheKey]);
                muadilRow.show();

                // Tooltip'leri yeniden başlat
                setTimeout(function () {
                    initTooltips();
                }, 100);
            }
        }

        // Bonus opsiyonu ile sepete ekleme
        function addToCartWithMF(productId, quantity, bonusOption, buttonElement) {
            $.ajax({
                url: '{{ route("cart.add") }}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    product_id: productId,
                    quantity: quantity,
                    bonus_option: bonusOption,
                    product_campaign_id: null
                },
                success: function (response) {
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
                error: function (xhr) {
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
            setTimeout(function () {
                notification.style.opacity = '1';
            }, 10);

            // Kaybol - YALNIZCA OPACITY, POZİSYON DEĞİŞMEZ
            setTimeout(function () {
                notification.style.opacity = '0';
            }, 2000); // 2 saniye bekle

            // Kaldır
            setTimeout(function () {
                notification.remove();
            }, 2500);
        }

        // Flying notification - butondan sepete animasyon
        function showFlyingNotification(buttonElement, message) {
            const buttonRect = buttonElement.getBoundingClientRect();

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
            notification.style.whiteSpace = 'nowrap';
            notification.style.opacity = '1';
            notification.style.transform = 'scale(1)';
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
            $('.modal-backdrop').each(function (index, element) {
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
                success: function (response) {
                    $('#modalContent').html(response);

                    // Tooltip'leri aktif et
                    setTimeout(function () {
                        initTooltips();
                    }, 100);

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
                success: function (response) {
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

        // Muadil ürünler için miktar kontrolü (MF2Utils kullanır)
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
                success: function (response) {
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

        // ============================================
        // Muadil Modal Ürünleri için Bonus Fonksiyonları
        // (Ürün detay modalındaki muadil ürünler)
        // ============================================

        // Muadil Modal - Bonus opsiyon 1 seçildiğinde miktarı 1 yap
        function onMuadilModalBonusOption1Selected(productId) {
            const input = document.getElementById('muadil-modal-qty-' + productId);
            if (input) {
                input.value = 1;
            }
        }

        // Muadil Modal - Bonus opsiyon 2 seçildiğinde minimum miktarı ayarla
        function onMuadilModalBonusOption2Selected(productId, minQty) {
            const input = document.getElementById('muadil-modal-qty-' + productId);
            if (input) {
                input.value = minQty;
            }
        }

        // Muadil Modal - Miktar değiştiğinde bonus opsiyonu kontrol et
        function checkMuadilModalBonusOptionOnQtyChange(productId) {
            const input = document.getElementById('muadil-modal-qty-' + productId);
            const currentQty = parseInt(input.value) || 0;

            const option2Radio = document.getElementById('muadil_modal_bonus_' + productId + '_2');
            const option1Radio = document.getElementById('muadil_modal_bonus_' + productId + '_1');

            if (option2Radio) {
                const minQty = parseInt(option2Radio.dataset.minQty) || 0;
                if (currentQty >= minQty && minQty > 0) {
                    option2Radio.checked = true;
                } else if (option1Radio) {
                    option1Radio.checked = true;
                }
            }
        }

        // Muadil Modal - Miktar artır
        function increaseMuadilModalQty(productId) {
            const input = document.getElementById('muadil-modal-qty-' + productId);
            const newValue = parseInt(input.value || 0) + 1;
            input.value = newValue;
            checkMuadilModalBonusOptionOnQtyChange(productId);
        }

        // Muadil Modal - Miktar azalt
        function decreaseMuadilModalQty(productId) {
            const input = document.getElementById('muadil-modal-qty-' + productId);
            const currentValue = parseInt(input.value || 0);
            if (currentValue > 0) {
                input.value = currentValue - 1;
                checkMuadilModalBonusOptionOnQtyChange(productId);
            }
        }

        // Muadil Modal - Seçili bonus opsiyonunu al
        function getSelectedMuadilModalBonusOption(productId) {
            const option2Radio = document.getElementById('muadil_modal_bonus_' + productId + '_2');
            if (option2Radio && option2Radio.checked) {
                return 2;
            }
            return 1;
        }

        // Muadil Modal - Sepete ekle
        function addMuadilModalToCart(productId, buttonElement) {
            const quantity = parseInt(document.getElementById('muadil-modal-qty-' + productId).value) || 0;

            if (quantity === 0) {
                if (buttonElement) {
                    showWarningNotification(buttonElement, 'Lütfen miktar girin!');
                } else {
                    showNotification('Lütfen miktar girin!', 'error');
                }
                return;
            }

            const bonusOption = getSelectedMuadilModalBonusOption(productId);

            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity,
                    bonus_option: bonusOption,
                    mf_satis: 0
                },
                success: function (response) {
                    if (response.success) {
                        if (typeof updateCartCount === 'function') {
                            updateCartCount();
                        }
                        if (buttonElement) {
                            showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                        } else {
                            showNotification(response.message || 'Ürün sepete eklendi!', 'success');
                        }
                        document.getElementById('muadil-modal-qty-' + productId).value = 0;
                    }
                },
                error: function (xhr) {
                    let message = 'Sepete eklenirken hata oluştu';
                    if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (buttonElement) {
                        showWarningNotification(buttonElement, message);
                    } else {
                        showNotification(message, 'error');
                    }
                }
            });
        }

        // ============================================
        // Muadil Liste Ürünleri için Bonus Fonksiyonları
        // (Ana sayfa liste görünümündeki muadil ürünler)
        // ============================================

        // Muadil List - Bonus opsiyon 1 seçildiğinde miktarı 1 yap
        function onMuadilListBonusOption1Selected(productId) {
            const input = document.getElementById('muadil-list-qty-' + productId);
            if (input) {
                input.value = 1;
            }
        }

        // Muadil List - Bonus opsiyon 2 seçildiğinde minimum miktarı ayarla
        function onMuadilListBonusOption2Selected(productId, minQty) {
            const input = document.getElementById('muadil-list-qty-' + productId);
            if (input) {
                input.value = minQty;
            }
        }

        // Muadil List - Miktar değiştiğinde bonus opsiyonu kontrol et
        function checkMuadilListBonusOptionOnQtyChange(productId) {
            const input = document.getElementById('muadil-list-qty-' + productId);
            const currentQty = parseInt(input.value) || 0;

            const option2Radio = document.getElementById('muadil_list_bonus_' + productId + '_2');
            const option1Radio = document.getElementById('muadil_list_bonus_' + productId + '_1');

            if (option2Radio) {
                const minQty = parseInt(option2Radio.dataset.minQty) || 0;
                if (currentQty >= minQty && minQty > 0) {
                    option2Radio.checked = true;
                } else if (option1Radio) {
                    option1Radio.checked = true;
                }
            }
        }

        // Muadil List - Miktar artır
        function increaseMuadilListQty(productId) {
            const input = document.getElementById('muadil-list-qty-' + productId);
            const newValue = parseInt(input.value || 0) + 1;
            input.value = newValue;
            checkMuadilListBonusOptionOnQtyChange(productId);
        }

        // Muadil List - Miktar azalt
        function decreaseMuadilListQty(productId) {
            const input = document.getElementById('muadil-list-qty-' + productId);
            const currentValue = parseInt(input.value || 0);
            if (currentValue > 0) {
                input.value = currentValue - 1;
                checkMuadilListBonusOptionOnQtyChange(productId);
            }
        }

        // Muadil List - Seçili bonus opsiyonunu al
        function getSelectedMuadilListBonusOption(productId) {
            const option2Radio = document.getElementById('muadil_list_bonus_' + productId + '_2');
            if (option2Radio && option2Radio.checked) {
                return 2;
            }
            return 1;
        }

        // Muadil List - Sepete ekle
        function addMuadilListToCart(productId, buttonElement) {
            const quantity = parseInt(document.getElementById('muadil-list-qty-' + productId).value) || 0;

            if (quantity === 0) {
                if (buttonElement) {
                    showWarningNotification(buttonElement, 'Lütfen miktar girin!');
                } else {
                    showNotification('Lütfen miktar girin!', 'error');
                }
                return;
            }

            const bonusOption = getSelectedMuadilListBonusOption(productId);

            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity,
                    bonus_option: bonusOption,
                    mf_satis: 0
                },
                success: function (response) {
                    if (response.success) {
                        if (typeof updateCartCount === 'function') {
                            updateCartCount();
                        }
                        if (buttonElement) {
                            showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                        } else {
                            showNotification(response.message || 'Ürün sepete eklendi!', 'success');
                        }
                        document.getElementById('muadil-list-qty-' + productId).value = 0;
                    }
                },
                error: function (xhr) {
                    let message = 'Sepete eklenirken hata oluştu';
                    if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (buttonElement) {
                        showWarningNotification(buttonElement, message);
                    } else {
                        showNotification(message, 'error');
                    }
                }
            });
        }

        // ============================================
        // Modal Ana Ürün için Bonus Fonksiyonları
        // (Ürün detay modalındaki ana ürün)
        // ============================================

        // Modal Product - Bonus opsiyon 1 seçildiğinde miktarı 1 yap
        function onModalProductBonusOption1Selected(productId) {
            const input = document.getElementById('modal-product-qty-' + productId);
            if (input) {
                input.value = 1;
            }
        }

        // Modal Product - Bonus opsiyon 2 seçildiğinde minimum miktarı ayarla
        function onModalProductBonusOption2Selected(productId, minQty) {
            const input = document.getElementById('modal-product-qty-' + productId);
            if (input) {
                input.value = minQty;
            }
        }

        // Modal Product - Miktar değiştiğinde bonus opsiyonu kontrol et
        function checkModalProductBonusOptionOnQtyChange(productId) {
            const input = document.getElementById('modal-product-qty-' + productId);
            const currentQty = parseInt(input.value) || 0;

            const option2Radio = document.getElementById('modal_product_bonus_' + productId + '_2');
            const option1Radio = document.getElementById('modal_product_bonus_' + productId + '_1');

            if (option2Radio) {
                const minQty = parseInt(option2Radio.dataset.minQty) || 0;
                if (currentQty >= minQty && minQty > 0) {
                    option2Radio.checked = true;
                } else if (option1Radio) {
                    option1Radio.checked = true;
                }
            }
        }

        // Modal Product - Miktar artır (MF2Utils kullanır)
        function increaseModalProductQty(productId) {
            const input = document.getElementById('modal-product-qty-' + productId);
            MF2Utils.increaseQuantity(input);
            checkModalProductBonusOptionOnQtyChange(productId);
        }

        // Modal Product - Miktar azalt (MF2Utils kullanır)
        function decreaseModalProductQty(productId) {
            const input = document.getElementById('modal-product-qty-' + productId);
            MF2Utils.decreaseQuantity(input, 0);
            checkModalProductBonusOptionOnQtyChange(productId);
        }

        // Modal Product - Blur'da bonus opsiyonu kontrol et (yuvarlama yapılmıyor)
        function roundMf2QuantityModal(productId) {
            // Yuvarlama yapılmıyor, sadece radio kontrolü
            checkModalProductBonusOptionOnQtyChange(productId);
        }

        // Modal Product - Seçili bonus opsiyonunu al
        function getSelectedModalProductBonusOption(productId) {
            const option2Radio = document.getElementById('modal_product_bonus_' + productId + '_2');
            if (option2Radio && option2Radio.checked) {
                return 2;
            }
            return 1;
        }

        // Modal Product - Sepete ekle
        function addModalProductToCart(productId, buttonElement) {
            const qtyInput = document.getElementById('modal-product-qty-' + productId);
            const quantity = qtyInput ? parseInt(qtyInput.value) || 0 : 0;

            if (quantity === 0) {
                if (buttonElement) {
                    showWarningNotification(buttonElement, 'Lütfen miktar girin!');
                } else {
                    showNotification('Lütfen miktar girin!', 'error');
                }
                return;
            }

            const bonusOption = getSelectedModalProductBonusOption(productId);

            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity,
                    bonus_option: bonusOption,
                    mf_satis: 0
                },
                success: function (response) {
                    if (response.success) {
                        if (typeof updateCartCount === 'function') {
                            updateCartCount();
                        }
                        if (buttonElement) {
                            showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                        } else {
                            showNotification(response.message || 'Ürün sepete eklendi!', 'success');
                        }
                        if (qtyInput) qtyInput.value = 0;
                    }
                },
                error: function (xhr) {
                    let message = 'Sepete eklenirken hata oluştu';
                    if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
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
    <div class="modal fade" id="specialCampaignModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border: 2px solid #f59e0b;">
                <div class="modal-header border-0 py-2"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #f59e0b 100%); box-shadow: 0 3px 10px rgba(245, 158, 11, 0.4);">
                    <h5 class="modal-title text-white fw-bold mb-0 d-flex align-items-center"
                        style="font-size: 1rem; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);">
                        <i class="fas fa-gift me-2" style="animation: bounce 1s ease-in-out infinite;"></i>
                        <span style="letter-spacing: 1px;">ÖZEL KAMPANYA</span>
                        <i class="fas fa-fire ms-2" style="animation: flicker 1.5s ease-in-out infinite;"></i>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
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
                                    <th class="text-center mobile-combined-cell" style="width: 100px;"><span
                                            class="hide-on-mobile">Net Fiyat</span><span class="show-on-mobile">Fiyat</span>
                                    </th>
                                    <th class="text-center mobile-qty-cart" style="width: 120px;"><span
                                            class="hide-on-mobile">Miktar</span><span class="show-on-mobile">Miktar /
                                            Sepet</span></th>
                                    <th class="hide-on-mobile" style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($specialCampaignProducts as $product)
                                    <tr class="special-campaign-row" id="campaign-product-{{ $product->id }}">
                                        <td class="text-center hide-on-mobile">
                                            <span class="badge bg-light text-dark border"
                                                style="font-size: 0.85rem; font-weight: 500;">
                                                {{ $product->urun_kodu }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" onclick="showProductModal({{ $product->id }})"
                                                class="text-decoration-none">
                                                @if($product->bakiye > 0)
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
                                                @if($product->hasImage())
                                                    <span class="product-name-with-image desktop-only-hover"
                                                        onmouseenter="showImagePreview(event, '{{ $product->image_url }}')"
                                                        onmouseleave="hideImagePreview()" style="cursor: pointer;">
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
                                            <span class="text-muted">{{ number_format($product->satis_fiyati, 2, ',', '.') }}
                                                ₺</span>
                                        </td>
                                        <td class="text-end hide-on-mobile">
                                            @if($product->depocu_fiyati)
                                                <span class="text-muted">{{ number_format($product->depocu_fiyati, 2, ',', '.') }}
                                                    ₺</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center hide-on-mobile">
                                            @if($product->mf1 || $product->mf2)
                                                @php
                                                    $mf2MinQtyCamp = 0;
                                                    if ($product->mf2 && str_contains($product->mf2, '+')) {
                                                        $partsCamp = explode('+', $product->mf2);
                                                        $mf2MinQtyCamp = (int) trim($partsCamp[0]) + (int) trim($partsCamp[1]);
                                                    }
                                                @endphp
                                                <div style="font-size: 0.85rem;">
                                                    @if($product->mf1)
                                                        <div class="d-flex align-items-center justify-content-center mb-1">
                                                            @if($product->mf1 && $product->mf2)
                                                                <input class="form-check-input me-1" type="radio"
                                                                    name="campaign_bonus_option_{{ $product->id }}"
                                                                    id="campaign_bonus_{{ $product->id }}_1" value="1" checked
                                                                    data-product-id="{{ $product->id }}" data-min-qty="0"
                                                                    onchange="onCampaignBonusOption1Selected({{ $product->id }})"
                                                                    style="margin: 0;">
                                                            @endif
                                                            <label for="campaign_bonus_{{ $product->id }}_1" style="cursor: pointer;">
                                                                <span class="badge bg-success"
                                                                    style="font-size: 0.8rem;">{{ $product->mf1 }}</span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                    @if($product->mf2)
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            @if($product->mf1 && $product->mf2)
                                                                <input class="form-check-input me-1" type="radio"
                                                                    name="campaign_bonus_option_{{ $product->id }}"
                                                                    id="campaign_bonus_{{ $product->id }}_2" value="2"
                                                                    data-product-id="{{ $product->id }}" data-min-qty="{{ $mf2MinQtyCamp }}"
                                                                    onchange="onCampaignBonusOption2Selected({{ $product->id }}, {{ $mf2MinQtyCamp }})"
                                                                    style="margin: 0;">
                                                            @endif
                                                            <label for="campaign_bonus_{{ $product->id }}_2" style="cursor: pointer;">
                                                                <span class="badge bg-primary"
                                                                    style="font-size: 0.8rem;">{{ $product->mf2 }}</span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end mobile-combined-cell">
                                            @php
                                                $netFiyat = $product->net_fiyat1 ?? $product->net_price;
                                            @endphp
                                            <div class="desktop-price">
                                                @if($product->mf1 || $product->mf2)
                                                    <div style="font-size: 0.85rem;">
                                                        @if($product->mf1)
                                                            <div class="mb-1">
                                                                <strong
                                                                    class="text-success">{{ $product->net_fiyat1 ? number_format($product->net_fiyat1, 2, ',', '.') . ' ₺' : '-' }}</strong>
                                                            </div>
                                                        @endif
                                                        @if($product->mf2)
                                                            <div>
                                                                <strong
                                                                    class="text-primary">{{ $product->net_fiyat2 ? number_format($product->net_fiyat2, 2, ',', '.') . ' ₺' : '-' }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <strong
                                                        style="color: #198754; font-size: 1rem;">{{ number_format($netFiyat, 2, ',', '.') }}
                                                        ₺</strong>
                                                @endif
                                            </div>
                                            <div class="mobile-price-mf">
                                                @if($product->mf1)
                                                    <div style="font-size: 0.75rem; color: #666; margin-bottom: 2px;">
                                                        <span class="badge bg-success"
                                                            style="font-size: 0.6rem; padding: 0.1rem 0.2rem;">{{ $product->mf1 }}</span>
                                                    </div>
                                                @endif
                                                @if($product->mf2)
                                                    <div style="font-size: 0.75rem; color: #666; margin-bottom: 2px;">
                                                        <span class="badge bg-primary"
                                                            style="font-size: 0.6rem; padding: 0.1rem 0.2rem;">{{ $product->mf2 }}</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong
                                                        style="color: #198754; font-size: 0.85rem;">{{ number_format($netFiyat, 2, ',', '.') }}
                                                        ₺</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="mobile-qty-cart-cell">
                                            <div class="qty-cart-container">
                                                <div class="input-group input-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        onclick="decreaseCampaignQty({{ $product->id }})">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" id="campaign-qty-{{ $product->id }}"
                                                        class="form-control text-center" value="0" min="0"
                                                        style="max-width: 80px;"
                                                        oninput="checkCampaignBonusOptionOnQtyChange({{ $product->id }})"
                                                        onkeypress="if(event.key === 'Enter' && this.value > 0) { addCampaignToCart({{ $product->id }}, this); }">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        onclick="increaseCampaignQty({{ $product->id }})">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="mobile-cart-btn">
                                                    <button type="button" class="btn btn-success btn-sm"
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
                                                <button type="button" class="btn btn-success btn-sm"
                                                    onclick="addCampaignToCart({{ $product->id }}, this)" title="Sepete Ekle"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    style="padding: 0.25rem 0.6rem;">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Muadil ürünler için placeholder satır -->
                                    <tr id="campaign-muadil-row-{{ $product->id }}" class="muadil-products-row"
                                        style="display: none;">
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
        document.addEventListener('DOMContentLoaded', function () {
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
            const newValue = parseInt(input.value || 0) + 1;
            input.value = newValue;
            // Bonus opsiyonunu kontrol et
            checkCampaignBonusOptionOnQtyChange(productId);
        }

        function decreaseCampaignQty(productId) {
            const input = document.getElementById('campaign-qty-' + productId);
            const currentValue = parseInt(input.value || 0);
            if (currentValue > 0) {
                input.value = currentValue - 1;
                // Bonus opsiyonunu kontrol et
                checkCampaignBonusOptionOnQtyChange(productId);
            }
        }

        // Kampanya - Bonus opsiyon 1 seçildiğinde miktarı 1 yap
        function onCampaignBonusOption1Selected(productId) {
            const input = document.getElementById('campaign-qty-' + productId);
            if (input) {
                input.value = 1;
            }
        }

        // Kampanya - Bonus opsiyon 2 seçildiğinde minimum miktarı ayarla
        function onCampaignBonusOption2Selected(productId, minQty) {
            const input = document.getElementById('campaign-qty-' + productId);
            if (input) {
                input.value = minQty;
            }
        }

        // Kampanya - Miktar değiştiğinde bonus opsiyonu kontrol et
        function checkCampaignBonusOptionOnQtyChange(productId) {
            const input = document.getElementById('campaign-qty-' + productId);
            const currentQty = parseInt(input.value) || 0;

            // Radio butonlarını bul
            const option2Radio = document.getElementById('campaign_bonus_' + productId + '_2');
            const option1Radio = document.getElementById('campaign_bonus_' + productId + '_1');

            // Eğer opsiyon 2 varsa
            if (option2Radio) {
                const minQty = parseInt(option2Radio.dataset.minQty) || 0;

                if (currentQty >= minQty && minQty > 0) {
                    // Miktar minimum miktara eşit veya fazlaysa opsiyon 2'yi seç
                    option2Radio.checked = true;
                } else if (option1Radio) {
                    // Miktar minimumun altındaysa opsiyon 1'e geç
                    option1Radio.checked = true;
                }
            }
        }

        // Kampanya - Seçili bonus opsiyonunu al
        function getSelectedCampaignBonusOption(productId) {
            const option1Radio = document.getElementById('campaign_bonus_' + productId + '_1');
            const option2Radio = document.getElementById('campaign_bonus_' + productId + '_2');

            if (option2Radio && option2Radio.checked) {
                return 2;
            }
            return 1;
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

            // Seçili bonus opsiyonunu al
            const bonusOption = getSelectedCampaignBonusOption(productId);

            // AJAX ile sepete ekle
            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity,
                    bonus_option: bonusOption,
                    mf_satis: 0
                },
                success: function (response) {
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
                    success: function (response) {
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

                            response.products.forEach(function (product) {
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
                                if (product.mf1 || product.mf2) {
                                    html += '<div style="font-size: 0.85rem;">';
                                    // Parse mf2 min qty - artık toplam (10+5=15)
                                    var mf2MinQtyMuadilCamp = 0;
                                    if (product.mf2 && product.mf2.includes('+')) {
                                        var parts = product.mf2.split('+');
                                        mf2MinQtyMuadilCamp = (parseInt(parts[0].trim()) || 0) + (parseInt(parts[1].trim()) || 0);
                                    }
                                    if (product.mf1) {
                                        html += '<div class="d-flex align-items-center justify-content-center mb-1">';
                                        // Radio sadece ikisi de varsa göster
                                        if (product.mf1 && product.mf2) {
                                            html += '<input class="form-check-input me-1" type="radio" name="muadil_campaign_bonus_' + product.id + '" id="muadil_campaign_bonus_' + product.id + '_1" value="1" checked data-min-qty="0" onchange="onMuadilCampaignBonusOption1Selected(' + product.id + ')" style="margin: 0;">';
                                        }
                                        html += '<label for="muadil_campaign_bonus_' + product.id + '_1" style="cursor: pointer;"><span class="badge bg-success" style="font-size: 0.8rem;">' + product.mf1 + '</span></label>';
                                        html += '</div>';
                                    }
                                    if (product.mf2) {
                                        html += '<div class="d-flex align-items-center justify-content-center">';
                                        // Radio sadece ikisi de varsa göster
                                        if (product.mf1 && product.mf2) {
                                            html += '<input class="form-check-input me-1" type="radio" name="muadil_campaign_bonus_' + product.id + '" id="muadil_campaign_bonus_' + product.id + '_2" value="2" data-min-qty="' + mf2MinQtyMuadilCamp + '" onchange="onMuadilCampaignBonusOption2Selected(' + product.id + ', ' + mf2MinQtyMuadilCamp + ')" style="margin: 0;">';
                                        }
                                        html += '<label for="muadil_campaign_bonus_' + product.id + '_2" style="cursor: pointer;"><span class="badge bg-primary" style="font-size: 0.8rem;">' + product.mf2 + '</span></label>';
                                        html += '</div>';
                                    }
                                    html += '</div>';
                                } else {
                                    html += '<span class="text-muted">-</span>';
                                }
                                html += '</td>';
                                html += '<td class="text-end mobile-combined-cell" style="width: 100px;">';
                                html += '<div class="desktop-price">';
                                if (product.mf1 || product.mf2) {
                                    html += '<div style="font-size: 0.85rem;">';
                                    if (product.mf1) {
                                        html += '<div class="mb-1"><strong class="text-success">' + (product.net_fiyat1_formatted || '-') + '</strong></div>';
                                    }
                                    if (product.mf2) {
                                        html += '<div><strong class="text-primary">' + (product.net_fiyat2_formatted || '-') + '</strong></div>';
                                    }
                                    html += '</div>';
                                } else {
                                    html += '<strong style="color: #198754; font-size: 1rem;">' + product.net_fiyat_formatted + '</strong>';
                                }
                                html += '</div>';
                                html += '<div class="mobile-price-mf" style="text-align: center;">';
                                if (product.mf1) {
                                    html += '<div style="font-size: 0.65rem; margin-bottom: 2px;"><span class="badge bg-success" style="font-size: 0.6rem; padding: 0.1rem 0.2rem;">' + product.mf1 + '</span></div>';
                                }
                                if (product.mf2) {
                                    html += '<div style="font-size: 0.65rem; margin-bottom: 2px;"><span class="badge bg-primary" style="font-size: 0.6rem; padding: 0.1rem 0.2rem;">' + product.mf2 + '</span></div>';
                                }
                                html += '<div><strong style="color: #198754; font-size: 0.85rem;">' + product.net_fiyat_formatted + '</strong></div>';
                                html += '</div>';
                                html += '</td>';
                                html += '<td class="mobile-qty-cart-cell" style="width: 150px;">';
                                html += '<div class="qty-cart-container">';
                                html += '<div class="input-group input-group-sm">';
                                html += '<button type="button" class="btn btn-outline-secondary" onclick="decreaseMuadilCampaignQty(' + product.id + ')"><i class="fas fa-minus"></i></button>';
                                html += '<input type="number" id="muadil-campaign-qty-' + product.id + '" class="form-control text-center" value="0" min="0" oninput="checkMuadilCampaignBonusOptionOnQtyChange(' + product.id + ')" onkeypress="if(event.key === \'Enter\' && this.value > 0) { addMuadilCampaignToCart(' + product.id + ', this); }">';
                                html += '<button type="button" class="btn btn-outline-secondary" onclick="increaseMuadilCampaignQty(' + product.id + ')"><i class="fas fa-plus"></i></button>';
                                html += '</div>';
                                html += '<div class="mobile-cart-btn">';
                                html += '<button type="button" class="btn btn-success btn-sm" onclick="addMuadilCampaignToCart(' + product.id + ', this)" title="Sepete Ekle" style="padding: 0.25rem 0.4rem; font-size: 0.75rem;">';
                                html += '<i class="fas fa-cart-plus"></i>';
                                html += '</button>';
                                html += '</div>';
                                html += '</div>';
                                html += '</td>';
                                html += '<td class="mobile-qty-cart-cell">';
                                html += '<div class="desktop-cart">';
                                html += '<button type="button" class="btn btn-primary btn-sm add-to-cart-btn" id="add-btn-muadil-campaign-' + product.id + '" onclick="addMuadilCampaignToCart(' + product.id + ', document.getElementById(\'add-btn-muadil-campaign-' + product.id + '\'))" title="Sepete Ekle">';
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
                    error: function () {
                        muadilContent.html('<div class="alert alert-danger mb-0">Muadil ürünler yüklenirken hata oluştu.</div>');
                    }
                });
            } else {
                // Daha önce yüklenmişse sadece göster
                muadilRow.show();
            }
        }

        // ============================================
        // Muadil Campaign Ürünleri için Bonus Fonksiyonları
        // ============================================

        // Muadil Campaign - Bonus opsiyon 1 seçildiğinde miktarı 1 yap
        function onMuadilCampaignBonusOption1Selected(productId) {
            const input = document.getElementById('muadil-campaign-qty-' + productId);
            if (input) {
                input.value = 1;
            }
        }

        // Muadil Campaign - Bonus opsiyon 2 seçildiğinde minimum miktarı ayarla
        function onMuadilCampaignBonusOption2Selected(productId, minQty) {
            const input = document.getElementById('muadil-campaign-qty-' + productId);
            if (input) {
                input.value = minQty;
            }
        }

        // Muadil Campaign - Miktar değiştiğinde bonus opsiyonu kontrol et
        function checkMuadilCampaignBonusOptionOnQtyChange(productId) {
            const input = document.getElementById('muadil-campaign-qty-' + productId);
            const currentQty = parseInt(input.value) || 0;

            const option2Radio = document.getElementById('muadil_campaign_bonus_' + productId + '_2');
            const option1Radio = document.getElementById('muadil_campaign_bonus_' + productId + '_1');

            if (option2Radio) {
                const minQty = parseInt(option2Radio.dataset.minQty) || 0;
                if (currentQty >= minQty && minQty > 0) {
                    option2Radio.checked = true;
                } else if (option1Radio) {
                    option1Radio.checked = true;
                }
            }
        }

        // Muadil Campaign - Miktar artır
        function increaseMuadilCampaignQty(productId) {
            const input = document.getElementById('muadil-campaign-qty-' + productId);
            const newValue = parseInt(input.value || 0) + 1;
            input.value = newValue;
            checkMuadilCampaignBonusOptionOnQtyChange(productId);
        }

        // Muadil Campaign - Miktar azalt
        function decreaseMuadilCampaignQty(productId) {
            const input = document.getElementById('muadil-campaign-qty-' + productId);
            const currentValue = parseInt(input.value || 0);
            if (currentValue > 0) {
                input.value = currentValue - 1;
                checkMuadilCampaignBonusOptionOnQtyChange(productId);
            }
        }

        // Muadil Campaign - Seçili bonus opsiyonunu al
        function getSelectedMuadilCampaignBonusOption(productId) {
            const option2Radio = document.getElementById('muadil_campaign_bonus_' + productId + '_2');
            if (option2Radio && option2Radio.checked) {
                return 2;
            }
            return 1;
        }

        // Muadil Campaign - Sepete ekle
        function addMuadilCampaignToCart(productId, buttonElement) {
            const quantity = parseInt(document.getElementById('muadil-campaign-qty-' + productId).value) || 0;

            if (quantity === 0) {
                if (buttonElement) {
                    showWarningNotification(buttonElement, 'Lütfen miktar girin!');
                } else {
                    showNotification('Lütfen miktar girin!', 'error');
                }
                return;
            }

            const bonusOption = getSelectedMuadilCampaignBonusOption(productId);

            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity,
                    bonus_option: bonusOption,
                    mf_satis: 0
                },
                success: function (response) {
                    if (response.success) {
                        if (typeof updateCartCount === 'function') {
                            updateCartCount();
                        }
                        if (buttonElement) {
                            showFlyingNotification(buttonElement, 'Ürün sepete eklendi');
                        } else {
                            showNotification(response.message || 'Ürün sepete eklendi!', 'success');
                        }
                        document.getElementById('muadil-campaign-qty-' + productId).value = 0;
                    }
                },
                error: function (xhr) {
                    let message = 'Sepete eklenirken hata oluştu';
                    if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (buttonElement) {
                        showWarningNotification(buttonElement, message);
                    } else {
                        showNotification(message, 'error');
                    }
                }
            });
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