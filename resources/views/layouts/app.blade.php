<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', optional($siteSettings)->site_name ?? config('app.name', 'atakodb2b'))</title>

    @stack('head')

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --success-color: #27ae60;
            --hover-color: #34495e;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Remove number input spin buttons */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
            appearance: textfield;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: var(--secondary-color) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand .site-logo {
            height: 36px;
            width: auto;
        }

        .navbar {
            padding: 1rem 0;
            background: linear-gradient(to bottom, #ffffff, #f8f9fa) !important;
        }

        .navbar.sticky-top {
            backdrop-filter: blur(10px);
        }

        /* Admin Menu Hizalama */
        .dropdown-menu .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .dropdown-menu .dropdown-item i.fa-fw {
            width: 20px;
            text-align: center;
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: #f8f9fa;
            padding-left: 1.5rem;
        }

        /* User Link Dikey Ortalama */
        .nav-link.user-link {
            display: flex;
            align-items: center;
            min-height: 40px;
        }

        .nav-link.user-link span {
            display: inline-flex;
            align-items: center;
        }

        /* Kampanya Butonu */
        .btn-campaign-navbar {
            display: flex;
            align-items: center;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white !important;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .btn-campaign-navbar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.5);
            color: white !important;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .btn-campaign-navbar i {
            animation: pulse-gift 2s infinite;
        }

        @keyframes pulse-gift {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Kullanıcı Dropdown - Uzun isim desteği (max 2 satır) */
        .user-link {
            max-width: 220px;
            font-size: 0.85rem;
        }

        .user-link span {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
            line-height: 1.3;
            vertical-align: middle;
        }

        /* Büyük ekranlarda biraz daha geniş */
        @media (min-width: 1200px) {
            .user-link {
                max-width: 280px;
            }
            
            .user-link span {
                max-width: 220px;
            }
        }

        /* Kompakt Sepet Tasarımı */
        .cart-link-compact {
            display: flex;
            align-items-center;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
            font-size: 0.95rem;
        }

        .cart-link-compact:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white !important;
        }

        .cart-badge-compact {
            background: rgba(255, 255, 255, 0.3);
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-right: 8px;
        }

        .cart-divider {
            margin: 0 8px;
            opacity: 0.5;
        }

        /* Muadil Ürün Satırları */
        .muadil-row {
            transition: all 0.2s ease;
        }

        .muadil-row:hover {
            background-color: #f8f9fa;
        }

        /* Özel Kampanya Tablosu */
        .campaign-table {
            background: white;
            font-size: 0.9rem;
            border-collapse: collapse !important;
            border-spacing: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
        }

        .campaign-table thead {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: white;
        }

        .campaign-table thead,
        .campaign-table thead *,
        .campaign-table thead tr,
        .campaign-table thead tr *,
        .campaign-table thead th,
        .campaign-table thead th * {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: #ffffff !important;
            border: 0 !important;
            border-width: 0 !important;
            text-align: center;
            vertical-align: middle;
            padding: 6px 8px;
            font-weight: 700;
            font-size: 0.75rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .campaign-table tbody tr {
            border-bottom: 1px solid #fef3c7;
        }

        .campaign-table tbody tr:last-child {
            border-bottom: none;
        }

        /* Özel Kampanya Popup Ürün Satırları */
        .special-campaign-row {
            transition: all 0.2s ease;
        }

        .special-campaign-row:hover {
            background: linear-gradient(to right, #fffbeb, #fef3c7);
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.15);
        }

        /* Kampanya Animasyonları */
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        @keyframes flicker {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        /* Modal Z-Index Ayarları */
        #specialCampaignModal {
            z-index: 1050;
        }

        #productModal {
            z-index: 1060;
        }

        .modal-backdrop.show {
            z-index: 1049;
        }

        /* User Link */
        .user-link {
            padding: 0.5rem 1rem !important;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .user-link:hover {
            background-color: #f8f9fa;
        }

        /* Nav Links */
        .nav-link {
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            transform: translateY(-1px);
        }

        .nav-link.rounded-pill:hover {
            background-color: #e9ecef;
        }

        /* Theme Toggle */
        #theme-toggle {
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #theme-toggle:hover {
            background-color: #f8f9fa;
        }

        /* Mobil İkonlar */
        .mobile-icons-wrapper {
            gap: 8px;
        }

        @media (max-width: 991.98px) {
            .navbar-brand span {
                font-size: 1.2rem;
            }

            .mobile-icons-wrapper button,
            .mobile-icons-wrapper a button {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--hover-color);
            border-color: var(--hover-color);
        }

        .btn-danger {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .product-card {
            transition: all 0.3s ease;
            border: none !important;
            border-radius: 10px !important;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .product-card .card-img-top {
            transition: transform 0.3s ease;
        }

        .product-card:hover .card-img-top {
            transform: scale(1.03);
        }

        [data-theme="dark"] .product-card {
            background-color: #2d2d2d !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        [data-theme="dark"] .product-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.5);
        }

        [data-theme="dark"] .product-card .card-body div[style*="background: #f8f9fa"] {
            background: #383838 !important;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .quantity-input {
            width: 80px;
            text-align: center;
        }

        .spinner-container {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .spinner-btn {
            width: 30px;
            height: 30px;
            padding: 0;
            font-size: 18px;
        }

        .list-view-table {
            background: white;
            font-size: 0.9rem;
            border-collapse: collapse !important;
            border-spacing: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .list-view-table thead {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            color: white;
        }

        .list-view-table thead,
        .list-view-table thead *,
        .list-view-table thead tr,
        .list-view-table thead tr *,
        .list-view-table thead th,
        .list-view-table thead th * {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            color: #ffffff !important;
            border: 0 !important;
            border-width: 0 !important;
            border-style: none !important;
            outline: none !important;
        }

        .list-view-table th {
            font-size: 0.85rem;
            font-weight: 700;
            padding: 8px;
            vertical-align: middle;
            text-transform: capitalize;
            letter-spacing: normal;
            border: none !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            border-bottom: none !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Ana tablo başlık satırında hover efekti olmasın - çok güçlü */
        .list-view-table thead tr,
        .list-view-table thead tr:hover,
        .list-view-table thead tr:focus,
        .list-view-table.table-hover thead tr:hover {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            transform: none !important;
            box-shadow: none !important;
            transition: none !important;
        }

        .list-view-table thead th,
        .list-view-table thead th:hover,
        .list-view-table thead th:focus,
        .list-view-table.table-hover thead th:hover {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            transform: none !important;
            box-shadow: none !important;
            transition: none !important;
            color: #ffffff !important;
            border: 0 !important;
            border-color: transparent !important;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            cursor: default !important;
            pointer-events: none;
        }

        .list-view-table thead {
            pointer-events: none;
        }

        .list-view-table td,
        .list-view-table tbody td,
        .list-view-table tbody tr td {
            padding: 12px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef !important;
            border-left: 0 !important;
            border-right: 0 !important;
            border-top: 0 !important;
            border-left-width: 0 !important;
            border-right-width: 0 !important;
            border-top-width: 0 !important;
            border-left-style: none !important;
            border-right-style: none !important;
            border-top-style: none !important;
            outline: none !important;
            transition: all 0.2s ease;
        }

        .list-view-table tbody tr {
            transition: all 0.2s ease;
            background-color: white;
        }

        .list-view-table tbody tr:hover {
            background-color: #f0f7ff;
            transform: scale(1.01);
            box-shadow: 0 2px 12px rgba(102, 126, 234, 0.15);
        }

        .list-view-table tbody tr:hover td,
        .list-view-table.table-hover tbody tr:hover td {
            border-left: 0 !important;
            border-right: 0 !important;
            border-top: 0 !important;
            outline: none !important;
        }

        /* Muadil ürünler satırında hover efekti olmasın */
        .list-view-table tbody tr.muadil-products-row:hover {
            background-color: inherit;
            transform: none;
            box-shadow: none;
        }

        .list-view-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Tablo içindeki fiyat alanlarını vurgula */
        .list-view-table td strong {
            color: #198754;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Dark theme için liste görünümü */
        [data-theme="dark"] .list-view-table {
            background: #2d2d2d;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            border-collapse: collapse !important;
        }

        /* Dark theme - Başlık zemini ve yazı rengi - ÇOK GÜÇLÜ */
        html[data-theme="dark"] .list-view-table thead,
        html[data-theme="dark"] .table.list-view-table thead,
        body[data-theme="dark"] .list-view-table thead {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
        }

        html[data-theme="dark"] .list-view-table thead th,
        html[data-theme="dark"] .table.list-view-table thead th,
        body[data-theme="dark"] .list-view-table thead th {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            color: #ffffff !important;
        }

        [data-theme="dark"] .list-view-table thead {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
        }

        [data-theme="dark"] .list-view-table thead,
        [data-theme="dark"] .list-view-table thead *,
        [data-theme="dark"] .list-view-table thead tr,
        [data-theme="dark"] .list-view-table thead tr *,
        [data-theme="dark"] .list-view-table thead th,
        [data-theme="dark"] .list-view-table thead th * {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            color: #ffffff !important;
            border: 0 !important;
            border-width: 0 !important;
            border-style: none !important;
            outline: none !important;
        }

        /* Dark theme'de ana tablo başlık satırında hover efekti olmasın - çok güçlü */
        [data-theme="dark"] .list-view-table thead tr,
        [data-theme="dark"] .list-view-table thead tr:hover,
        [data-theme="dark"] .list-view-table thead tr:focus,
        [data-theme="dark"] .list-view-table.table-hover thead tr:hover {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            transform: none !important;
            box-shadow: none !important;
            transition: none !important;
        }

        [data-theme="dark"] .list-view-table thead th,
        [data-theme="dark"] .list-view-table thead th:hover,
        [data-theme="dark"] .list-view-table thead th:focus,
        [data-theme="dark"] .list-view-table.table-hover thead th:hover {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            transform: none !important;
            box-shadow: none !important;
            transition: none !important;
            color: #ffffff !important;
            border: 0 !important;
            border-color: transparent !important;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            cursor: default !important;
            pointer-events: none;
        }

        [data-theme="dark"] .list-view-table thead {
            pointer-events: none;
        }

        [data-theme="dark"] .list-view-table tbody tr {
            background-color: #2d2d2d;
        }

        [data-theme="dark"] .list-view-table tbody tr:hover {
            background-color: #3a3a3a;
            box-shadow: 0 2px 12px rgba(102, 126, 234, 0.25);
        }

        [data-theme="dark"] .list-view-table tbody tr:hover td,
        [data-theme="dark"] .list-view-table.table-hover tbody tr:hover td {
            border-left: 0 !important;
            border-right: 0 !important;
            border-top: 0 !important;
            border-left-width: 0 !important;
            border-right-width: 0 !important;
            border-top-width: 0 !important;
            border-left-style: none !important;
            border-right-style: none !important;
            border-top-style: none !important;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            border-top-color: transparent !important;
            outline: none !important;
            box-shadow: none !important;
        }

        /* Dark theme'de muadil ürünler satırında hover efekti olmasın */
        [data-theme="dark"] .list-view-table tbody tr.muadil-products-row:hover {
            background-color: inherit;
            transform: none;
            box-shadow: none;
        }

        [data-theme="dark"] .list-view-table td,
        [data-theme="dark"] .list-view-table tbody td,
        [data-theme="dark"] .list-view-table tbody tr td {
            border-bottom: 1px solid #444 !important;
            border-left: 0 !important;
            border-right: 0 !important;
            border-top: 0 !important;
            border-left-width: 0 !important;
            border-right-width: 0 !important;
            border-top-width: 0 !important;
            border-left-style: none !important;
            border-right-style: none !important;
            border-top-style: none !important;
            outline: none !important;
            color: #e0e0e0;
        }

        [data-theme="dark"] .list-view-table td strong {
            color: #5cb85c;
        }

        /* Badge'ler için dark theme */
        [data-theme="dark"] .badge.bg-light {
            background-color: #3a3a3a !important;
            color: #e0e0e0 !important;
            border-color: #555 !important;
        }

        [data-theme="dark"] .badge.bg-warning {
            background-color: #f59e0b !important;
            color: #1a1a1a !important;
        }

        .image-icon {
            cursor: pointer;
            color: var(--primary-color);
            margin-right: 8px;
        }

        .product-name-with-image {
            cursor: pointer;
            color: inherit;
        }

        .product-name-with-image:hover {
            text-decoration: underline;
            color: var(--primary-color);
        }

        .muadil-icon {
            cursor: pointer;
            color: #0d6efd;
            font-size: 1.1em;
        }

        .muadil-icon:hover {
            color: #0a58ca;
        }

        .muadil-products-row {
            background-color: #f8f9fa;
            border: 3px solid #007bff !important;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
            transition: none !important;
        }

        .muadil-products-row:hover {
            transform: none !important;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15) !important;
        }

        .muadil-products-row td {
            padding: 5px !important;
        }

        .muadil-products-row>td {
            border-left: 3px solid #007bff;
            border-right: 3px solid #007bff;
            border-bottom: 3px solid #007bff;
        }

        .muadil-products-row .table {
            margin: 0;
            background-color: #f8f9fa;
            border-collapse: collapse !important;
            border: none;
        }

        /* Muadil ürünler için başlık stilleri */
        .muadil-products-row .table thead {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            border-color: #0056b3 !important;
        }

        .muadil-products-row .table thead,
        .muadil-products-row .table thead *,
        .muadil-products-row .table thead tr,
        .muadil-products-row .table thead tr *,
        .muadil-products-row .table thead th,
        .muadil-products-row .table thead th * {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            color: #ffffff !important;
            border: 0 !important;
            border-width: 0 !important;
            border-style: none !important;
            outline: none !important;
        }

        .muadil-products-row .table thead th {
            padding: 8px !important;
            font-size: 0.85rem;
            font-weight: 700;
            line-height: 1.2;
            vertical-align: middle;
            border: none !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            border-bottom: none !important;
            text-transform: capitalize;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Muadil başlık satırında hover efekti olmasın - çok güçlü */
        .muadil-products-row .table thead tr,
        .muadil-products-row .table thead tr:hover,
        .muadil-products-row .table thead tr:focus,
        .muadil-products-row .table.table-hover thead tr:hover {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            transform: none !important;
            box-shadow: none !important;
            transition: none !important;
        }

        .muadil-products-row .table thead th,
        .muadil-products-row .table thead th:hover,
        .muadil-products-row .table thead th:focus,
        .muadil-products-row .table.table-hover thead th:hover {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            transform: none !important;
            box-shadow: none !important;
            transition: none !important;
            color: #ffffff !important;
            border: 0 !important;
            border-color: transparent !important;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            cursor: default !important;
            pointer-events: none;
        }

        .muadil-products-row .table thead {
            pointer-events: none;
        }

        .muadil-products-row .table tbody td,
        .muadil-products-row .table tbody tr td {
            padding: 10px 8px !important;
            vertical-align: middle;
            border-top: 1px solid #dee2e6 !important;
            border-left: 0 !important;
            border-right: 0 !important;
            border-left-width: 0 !important;
            border-right-width: 0 !important;
            border-left-style: none !important;
            border-right-style: none !important;
            outline: none !important;
        }

        .muadil-products-row .table tbody tr {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            transition: all 0.2s ease;
        }

        .muadil-products-row .table tbody tr:hover {
            background-color: #f0f7ff;
            transform: scale(1.01);
            box-shadow: 0 2px 12px rgba(102, 126, 234, 0.15);
        }

        .muadil-products-row .table tbody tr:hover td,
        .muadil-products-row .table.table-hover tbody tr:hover td {
            border-left: 0 !important;
            border-right: 0 !important;
            outline: none !important;
        }

        /* Dark theme için muadil renkleri */
        html[data-theme="dark"] .muadil-products-row {
            background-color: #2d2d2d;
            border: 3px solid #0d6efd !important;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
            transition: none !important;
        }

        html[data-theme="dark"] .muadil-products-row:hover {
            transform: none !important;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3) !important;
        }

        /* Dark theme - Muadil başlık zemini ve yazı rengi - ÇOK GÜÇLÜ */
        html[data-theme="dark"] .muadil-products-row .table thead,
        body[data-theme="dark"] .muadil-products-row .table thead {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
        }

        html[data-theme="dark"] .muadil-products-row .table thead th,
        body[data-theme="dark"] .muadil-products-row .table thead th {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            color: #ffffff !important;
        }

        html[data-theme="dark"] .muadil-products-row>td {
            border-left: 3px solid #0d6efd;
            border-right: 3px solid #0d6efd;
            border-bottom: 3px solid #0d6efd;
        }

        html[data-theme="dark"] .muadil-products-row .table {
            background-color: #2d2d2d;
            color: #e0e0e0;
            border-collapse: collapse !important;
            margin: 0;
            border: none;
        }

        /* Dark theme için muadil başlık stilleri */
        html[data-theme="dark"] .muadil-products-row .table thead {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            border-color: #0056b3 !important;
        }

        html[data-theme="dark"] .muadil-products-row .table thead,
        html[data-theme="dark"] .muadil-products-row .table thead *,
        html[data-theme="dark"] .muadil-products-row .table thead tr,
        html[data-theme="dark"] .muadil-products-row .table thead tr *,
        html[data-theme="dark"] .muadil-products-row .table thead th,
        html[data-theme="dark"] .muadil-products-row .table thead th * {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            color: #ffffff !important;
            border: 0 !important;
            border-width: 0 !important;
            border-style: none !important;
            outline: none !important;
        }

        html[data-theme="dark"] .muadil-products-row .table thead th {
            padding: 8px !important;
            font-size: 0.85rem;
            font-weight: 700;
            line-height: 1.2;
            vertical-align: middle;
            border: none !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            border-bottom: none !important;
            text-transform: capitalize;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Dark theme'de muadil başlık satırında hover efekti olmasın - çok güçlü */
        html[data-theme="dark"] .muadil-products-row .table thead tr,
        html[data-theme="dark"] .muadil-products-row .table thead tr:hover,
        html[data-theme="dark"] .muadil-products-row .table thead tr:focus,
        html[data-theme="dark"] .muadil-products-row .table.table-hover thead tr:hover {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            transform: none !important;
            box-shadow: none !important;
            transition: none !important;
        }

        html[data-theme="dark"] .muadil-products-row .table thead th,
        html[data-theme="dark"] .muadil-products-row .table thead th:hover,
        html[data-theme="dark"] .muadil-products-row .table thead th:focus,
        html[data-theme="dark"] .muadil-products-row .table.table-hover thead th:hover {
            background: #0056b3 !important;
            background-color: #0056b3 !important;
            transform: none !important;
            box-shadow: none !important;
            transition: none !important;
            color: #ffffff !important;
            border: 0 !important;
            border-color: transparent !important;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            cursor: default !important;
            pointer-events: none;
        }

        html[data-theme="dark"] .muadil-products-row .table thead {
            pointer-events: none;
        }

        html[data-theme="dark"] .muadil-products-row .table tbody td,
        html[data-theme="dark"] .muadil-products-row .table tbody tr td {
            padding: 10px 8px !important;
            vertical-align: middle;
            border-top: 1px solid #444 !important;
            border-left: 0 !important;
            border-right: 0 !important;
            border-bottom: 1px solid #444 !important;
            border-left-width: 0 !important;
            border-right-width: 0 !important;
            border-left-style: none !important;
            border-right-style: none !important;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            outline: none !important;
        }

        html[data-theme="dark"] .muadil-products-row .table tbody tr {
            background-color: #2d2d2d;
            border-color: #444;
            transition: all 0.2s ease;
        }

        html[data-theme="dark"] .muadil-products-row .table tbody tr:hover {
            background-color: #3a3a3a;
            transform: scale(1.01);
            box-shadow: 0 2px 12px rgba(102, 126, 234, 0.25);
        }

        html[data-theme="dark"] .muadil-products-row .table tbody tr:hover td,
        html[data-theme="dark"] .muadil-products-row .table.table-hover tbody tr:hover td {
            border-left: 0 !important;
            border-right: 0 !important;
            border-left-width: 0 !important;
            border-right-width: 0 !important;
            border-left-style: none !important;
            border-right-style: none !important;
            border-left-color: transparent !important;
            border-right-color: transparent !important;
            outline: none !important;
        }

        .image-preview {
            position: fixed;
            z-index: 9999;
            display: none;
            max-width: 300px;
            background: white;
            border: 1px solid #ddd;
            padding: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .slider-container {
            margin-bottom: 30px;
        }

        .carousel-item img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .view-toggle {
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .carousel-item img {
                height: 250px;
            }
        }

        /* Dark Theme Styles */
        [data-theme="dark"] {
            background-color: #1a1a1a !important;
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] body {
            background-color: #1a1a1a !important;
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .navbar {
            background: linear-gradient(to bottom, #2d2d2d, #1a1a1a) !important;
            border-bottom: 1px solid #444;
        }

        [data-theme="dark"] .navbar-brand,
        [data-theme="dark"] .nav-link {
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .cart-link-compact {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%) !important;
        }

        [data-theme="dark"] .btn-campaign-navbar {
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%) !important;
        }

        [data-theme="dark"] .btn-campaign-navbar:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
        }

        [data-theme="dark"] .user-link:hover,
        [data-theme="dark"] .nav-link.rounded-pill:hover {
            background-color: #3d3d3d !important;
        }

        [data-theme="dark"] #theme-toggle:hover {
            background-color: #3d3d3d !important;
        }

        /* Mobil İkonlar - Dark Theme */
        [data-theme="dark"] .mobile-icons-wrapper .dropdown button {
            background: #3d3d3d !important;
            border-color: #555 !important;
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .mobile-icons-wrapper .badge {
            background-color: #dc3545 !important;
        }

        [data-theme="dark"] .card {
            background-color: #2d2d2d !important;
            border-color: #444 !important;
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .bg-light {
            background-color: #3d3d3d !important;
        }

        /* Resim Hazırlanıyor placeholder için dark tema */
        [data-theme="dark"] div[style*="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)"] {
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%) !important;
            border-color: #4a5568 !important;
        }

        [data-theme="dark"] div[style*="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)"] .text-secondary {
            color: #cbd5e0 !important;
        }

        [data-theme="dark"] div[style*="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)"] .text-muted {
            color: #a0aec0 !important;
        }

        /* Sepetteki ürün adı linki için dark tema */
        [data-theme="dark"] a .text-dark {
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] a:hover .text-dark {
            color: #66b3ff !important;
        }

        [data-theme="dark"] .table {
            background-color: #fff !important;
            color: #212529 !important;
        }

        [data-theme="dark"] .table thead th {
            color: #212529 !important;
            background-color: #f8f9fa !important;
            border-color: #dee2e6 !important;
        }

        [data-theme="dark"] .table tbody td,
        [data-theme="dark"] .table tbody th {
            color: #212529 !important;
            border-color: #dee2e6 !important;
        }

        [data-theme="dark"] .table tbody strong {
            color: #212529 !important;
        }

        [data-theme="dark"] .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #f8f9fa !important;
        }

        [data-theme="dark"] .table-hover>tbody>tr:hover {
            background-color: #e9ecef !important;
        }

        [data-theme="dark"] .table-primary {
            background-color: rgba(13, 110, 253, 0.3) !important;
            color: #212529 !important;
        }

        /* Tablo footer - zemin beyaz/açık olduğu için yazılar siyah kalmalı */
        [data-theme="dark"] .table tfoot td,
        [data-theme="dark"] .table tfoot th,
        [data-theme="dark"] .table tfoot strong {
            color: #212529 !important;
            background-color: #f8f9fa !important;
        }

        [data-theme="dark"] .table tfoot .table-primary td,
        [data-theme="dark"] .table tfoot .table-primary th {
            color: #212529 !important;
            background-color: rgba(13, 110, 253, 0.3) !important;
        }

        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background-color: #3d3d3d !important;
            border-color: #555 !important;
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .form-control::placeholder {
            color: #aaa !important;
            opacity: 1 !important;
        }

        [data-theme="dark"] .text-muted,
        [data-theme="dark"] small.text-muted {
            color: #bbb !important;
        }

        [data-theme="dark"] .text-primary {
            color: #66b3ff !important;
        }

        [data-theme="dark"] .text-success {
            color: #66ff99 !important;
        }

        [data-theme="dark"] .text-danger {
            color: #ff6666 !important;
        }

        [data-theme="dark"] .text-warning {
            color: #ffcc66 !important;
        }

        [data-theme="dark"] .text-info {
            color: #66ccff !important;
        }

        [data-theme="dark"] .badge {
            color: #fff !important;
        }

        [data-theme="dark"] .card-title,
        [data-theme="dark"] .card-text,
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6,
        [data-theme="dark"] p,
        [data-theme="dark"] strong {
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .dropdown-menu {
            background-color: #2d2d2d !important;
        }

        [data-theme="dark"] .dropdown-item {
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] footer {
            background-color: #2d2d2d !important;
            border-top: 1px solid #444 !important;
        }

        [data-theme="dark"] .btn-primary {
            background-color: #0066cc !important;
            border-color: #0066cc !important;
            color: #fff !important;
        }

        [data-theme="dark"] .btn-primary:hover {
            background-color: #0052a3 !important;
            border-color: #0052a3 !important;
        }

        [data-theme="dark"] .btn-outline-primary {
            color: #66b3ff !important;
            border-color: #66b3ff !important;
        }

        [data-theme="dark"] .btn-outline-primary:hover {
            background-color: #66b3ff !important;
            color: #1a1a1a !important;
        }

        /* Success button styles for Özel Liste */
        .btn-outline-success.active {
            background-color: #198754 !important;
            border-color: #198754 !important;
            color: #fff !important;
        }

        [data-theme="dark"] .btn-outline-success {
            color: #28a745 !important;
            border-color: #28a745 !important;
        }

        [data-theme="dark"] .btn-outline-success:hover {
            background-color: #28a745 !important;
            color: #1a1a1a !important;
        }

        [data-theme="dark"] .btn-outline-success.active {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: #fff !important;
        }

        /* Product Detail Page Dark Theme */
        [data-theme="dark"] .card.border-0.shadow .card-header {
            background: linear-gradient(135deg, #2a5298 0%, #3d6bb3 100%) !important;
        }

        [data-theme="dark"] .card.border-0.shadow {
            background-color: #2d2d2d !important;
            border-color: #444 !important;
        }

        [data-theme="dark"] .card.border-0.shadow .card-body {
            background-color: #2d2d2d !important;
        }

        /* Product Detail Image Box */
        [data-theme="dark"] .card.border-0.shadow .border {
            background-color: #3d3d3d !important;
            border-color: #555 !important;
        }

        /* Product Detail Tables - Override general table styles */
        [data-theme="dark"] .card.border-0.shadow .table {
            background-color: transparent !important;
        }

        [data-theme="dark"] .card.border-0.shadow .table thead {
            background-color: #3d3d3d !important;
        }

        [data-theme="dark"] .card.border-0.shadow .table thead th {
            background-color: #3d3d3d !important;
            color: #aaa !important;
            border-color: #555 !important;
        }

        [data-theme="dark"] .card.border-0.shadow .table tbody td {
            background-color: #2d2d2d !important;
            color: #e0e0e0 !important;
            border-color: #555 !important;
        }

        /* Highlight Table Header (Navy Blue) */
        [data-theme="dark"] .card.border-0.shadow .table thead[style*="background: #1e3c72"] {
            background: linear-gradient(135deg, #2a5298 0%, #3d6bb3 100%) !important;
        }

        [data-theme="dark"] .card.border-0.shadow .table thead[style*="background: #1e3c72"] th {
            background: transparent !important;
            color: #ffffff !important;
        }

        /* Yellow Box (Mal Fazlası) */
        [data-theme="dark"] .card.border-0.shadow .table tbody td[style*="background: #fff9e6"] {
            background-color: #4a4020 !important;
        }

        [data-theme="dark"] .card.border-0.shadow .table tbody td[style*="background: #fff9e6"] h3 {
            color: #ffd666 !important;
        }

        /* Green Box (Net Fiyat) */
        [data-theme="dark"] .card.border-0.shadow .table tbody td[style*="background: #e8f5e9"] {
            background-color: #1e4a2f !important;
        }

        [data-theme="dark"] .card.border-0.shadow .table tbody td[style*="background: #e8f5e9"] h3 {
            color: #66ff99 !important;
        }

        /* Gray Box (Miktar) */
        [data-theme="dark"] .card.border-0.shadow .table tbody td[style*="background: #f8f9fa"] {
            background-color: #3d3d3d !important;
        }

        /* Price Values (Blue in light mode) */
        [data-theme="dark"] .card.border-0.shadow .table tbody td h4[style*="color: #1e3c72"],
        [data-theme="dark"] .card.border-0.shadow .table tbody td h5[style*="color: #1e3c72"] {
            color: #66b3ff !important;
        }

        /* Form Controls in Product Detail */
        [data-theme="dark"] .card.border-0.shadow .form-control {
            background-color: #2d2d2d !important;
            border-color: #555 !important;
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .card.border-0.shadow .btn-outline-dark {
            background-color: #3d3d3d !important;
            border-color: #555 !important;
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .card.border-0.shadow .btn-outline-dark:hover {
            background-color: #4d4d4d !important;
            border-color: #666 !important;
        }

        /* Cart Button in Product Detail */
        [data-theme="dark"] .card.border-0.shadow .btn[style*="background: #1e3c72"] {
            background: linear-gradient(135deg, #2a5298 0%, #3d6bb3 100%) !important;
        }

        [data-theme="dark"] .card.border-0.shadow .btn[style*="background: #1e3c72"]:hover {
            background: linear-gradient(135deg, #3d6bb3 0%, #4d7bc3 100%) !important;
        }

        /* Product Detail Text Colors */
        [data-theme="dark"] .card.border-0.shadow h2,
        [data-theme="dark"] .card.border-0.shadow h3,
        [data-theme="dark"] .card.border-0.shadow h4,
        [data-theme="dark"] .card.border-0.shadow h5 {
            color: #e0e0e0 !important;
        }

        /* Muadil Products Card Dark Theme */
        [data-theme="dark"] .card.shadow-sm.mt-4 {
            background-color: #2d2d2d !important;
            border-color: #444 !important;
        }

        [data-theme="dark"] .card.shadow-sm.mt-4 .card-header {
            background-color: #3d3d3d !important;
            border-color: #555 !important;
        }

        [data-theme="dark"] .card.shadow-sm.mt-4 .card-header h5 {
            color: #e0e0e0 !important;
        }

        /* Catalog View - Price Info Dark Theme */
        [data-theme="dark"] .product-card h6 {
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .product-card h6 a {
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .product-card p[style*="color: #6c757d"] {
            color: #aaa !important;
        }

        [data-theme="dark"] .product-card .text-muted {
            color: #aaa !important;
        }

        [data-theme="dark"] .product-card strong[style*="color: #495057"] {
            color: #e0e0e0 !important;
        }

        [data-theme="dark"] .product-card .text-success {
            color: #66ff99 !important;
        }

        [data-theme="dark"] .product-card strong[style*="color: #f59e0b"] {
            color: #ffd666 !important;
        }

        /* Product Code Badge Dark Theme */
        [data-theme="dark"] .product-card .badge[style*="background: rgba(255,255,255,0.95)"] {
            background: rgba(45, 45, 45, 0.95) !important;
            color: #e0e0e0 !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        #theme-toggle {
            border: none;
            background: none;
            cursor: pointer;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}" id="navbar-home-link">
                @if($siteSettings?->logo_url)
                    <img src="{{ $siteSettings->logo_url }}" alt="{{ $siteSettings->site_name }}" class="site-logo">
                @else
                    <i class="fas fa-store"></i>
                @endif
                <span class="fw-bold">{{ $siteSettings->site_name ?? config('app.name', 'atakodb2b') }}</span>
            </a>

            <!-- Mobil İkonlar (Sadece mobilde görünür) -->
            @auth
                <div class="d-flex d-lg-none align-items-center mobile-icons-wrapper me-2">
                    <!-- Kampanya Butonu - Mobil -->
                    <button class="btn btn-sm btn-campaign-navbar p-2" onclick="openCampaignModal()" title="Kampanyalar"
                        style="border-radius: 50%; width: 38px; height: 38px;">
                        <i class="fas fa-gift"></i>
                    </button>

                    <!-- Sepet - Mobil -->
                    <a class="position-relative" href="{{ route('cart.index') }}" title="Sepetim"
                        style="text-decoration: none;">
                        <button class="btn btn-sm p-2"
                            style="border-radius: 50%; width: 38px; height: 38px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.65rem;" id="cart-count-mobile">
                            {{ $initialCartCount ?? 0 }}
                        </span>
                    </a>

                    <!-- Kullanıcı Dropdown - Mobil -->
                    <div class="dropdown">
                        <button class="btn btn-sm p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-radius: 50%; width: 38px; height: 38px; background: #f8f9fa; border: 1px solid #dee2e6;">
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="{{ route('orders.history') }}" class="dropdown-item d-flex align-items-center">
                                    <i class="fas fa-clipboard-list fa-fw me-2"></i>
                                    <span>Siparişlerim</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('cari.ekstre') }}" class="dropdown-item d-flex align-items-center">
                                    <i class="fas fa-file-invoice-dollar fa-fw me-2"></i>
                                    <span>Cari Ekstre</span>
                                </a>
                            </li>
                            @if((auth()->user()->isPlasiyer() || auth()->user()->isAdmin()) && session()->has('selected_customer_name'))
                                <li>
                                    <form action="{{ route('plasiyer.clearCustomer') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center">
                                            <i class="fas fa-exchange-alt fa-fw me-2"></i>
                                            <span>Müşteri Değiştir</span>
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @endif
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center">
                                        <i class="fas fa-sign-out-alt fa-fw me-2"></i>
                                        <span>Çıkış Yap</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @endauth

            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Admin Panel
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-chart-line fa-fw me-2"></i>
                                            <span>Dashboard</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('plasiyer.selectCustomer') }}">
                                            <i class="fas fa-user-check fa-fw me-2"></i>
                                            <span>Müşteri Seçimi</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('plasiyer.customerActivities') }}">
                                            <i class="fas fa-chart-line fa-fw me-2"></i>
                                            <span>Müşteri Aktiviteleri</span>
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('admin.orders.index') }}">
                                            <i class="fas fa-shopping-cart fa-fw me-2"></i>
                                            <span>Sipariş Yönetimi</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('admin.products.index') }}">
                                            <i class="fas fa-box fa-fw me-2"></i>
                                            <span>Ürün Yönetimi</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('admin.users.index') }}">
                                            <i class="fas fa-users fa-fw me-2"></i>
                                            <span>Kullanıcı Yönetimi</span>
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('admin.sliders.index') }}">
                                            <i class="fas fa-images fa-fw me-2"></i>
                                            <span>Slider Yönetimi</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('admin.settings.index') }}">
                                            <i class="fas fa-cog fa-fw me-2"></i>
                                            <span>Site Ayarları</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @elseif(auth()->user()->isPlasiyer())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('plasiyer.dashboard') }}">
                                    <i class="fas fa-briefcase"></i> Panel
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link rounded-pill px-3" href="{{ route('home') }}">
                                <i class="fas fa-home me-1"></i> Ana Sayfa
                            </a>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link btn btn-link rounded-circle" id="theme-toggle" onclick="toggleTheme()"
                                title="Tema Değiştir">
                                <i class="fas fa-moon" id="theme-icon"></i>
                            </button>
                        </li>

                        <!-- Desktop Menü Öğeleri (Mobilde gizli) -->
                        <li class="nav-item d-none d-lg-block">
                            <button class="btn btn-campaign-navbar me-2" onclick="openCampaignModal()"
                                title="Özel Kampanyalar">
                                <i class="fas fa-gift me-1"></i>
                                <span class="d-none d-md-inline">Kampanyalar</span>
                            </button>
                        </li>

                        <li class="nav-item d-none d-lg-block">
                            <a class="nav-link cart-link-compact px-3" href="{{ route('cart.index') }}" title="Sepetim"
                                style="min-width: 180px;">
                                <i class="fas fa-shopping-cart me-2"></i>
                                <span class="cart-badge-compact" id="cart-count"
                                    style="display: inline-block; min-width: 20px; text-align: center;">{{ $initialCartCount ?? 0 }}</span>
                                <span class="cart-divider">|</span>
                                <strong><span id="cart-total"
                                        style="display: inline-block; min-width: 60px; text-align: right;">{{ $initialCartTotal ?? '0,00' }}</span>
                                    ₺</strong>
                            </a>
                        </li>

                        <li class="nav-item dropdown d-none d-lg-block">
                            <a class="nav-link dropdown-toggle user-link" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fs-5 me-1"></i>
                                @if((auth()->user()->isPlasiyer() || auth()->user()->isAdmin()) && session()->has('selected_customer_name'))
                                    <span>{{ session('selected_customer_name') }}</span>
                                @else
                                    <span>{{ auth()->user()->name }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('orders.history') }}" class="dropdown-item d-flex align-items-center">
                                        <i class="fas fa-clipboard-list fa-fw me-2"></i>
                                        <span>Siparişlerim</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('cari.ekstre') }}" class="dropdown-item d-flex align-items-center">
                                        <i class="fas fa-file-invoice-dollar fa-fw me-2"></i>
                                        <span>Cari Ekstre</span>
                                    </a>
                                </li>
                                @if((auth()->user()->isPlasiyer() || auth()->user()->isAdmin()) && session()->has('selected_customer_name'))
                                    <li>
                                        <form action="{{ route('plasiyer.clearCustomer') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item d-flex align-items-center">
                                                <i class="fas fa-exchange-alt fa-fw me-2"></i>
                                                <span>Müşteri Değiştir</span>
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                @endif
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center">
                                            <i class="fas fa-sign-out-alt fa-fw me-2"></i>
                                            <span>Çıkış Yap</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        @if(session('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-4 py-2">
        <div class="container">
            @php
                $siteSettings = \App\Models\SiteSetting::getSettings();
            @endphp
            <!-- Firma Bilgileri -->
            <div class="d-flex flex-wrap gap-3 justify-content-center align-items-center mb-1"
                style="font-size: 0.85rem;">
                @if($siteSettings->company_name)
                    <strong>{{ $siteSettings->company_name }}</strong>
                @endif

                @if($siteSettings->company_address)
                    <span>
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ $siteSettings->company_address }}
                    </span>
                @endif

                @if($siteSettings->company_phone || $siteSettings->company_email)
                    <span>
                        @if($siteSettings->company_phone)
                            <i class="fas fa-phone me-1"></i>
                            <a href="tel:{{ str_replace([' ', '(', ')', '-'], '', $siteSettings->company_phone) }}"
                                class="text-white text-decoration-none">
                                {{ $siteSettings->company_phone }}
                            </a>
                        @endif

                        @if($siteSettings->company_phone && $siteSettings->company_email)
                            <span class="mx-2">|</span>
                        @endif

                        @if($siteSettings->company_email)
                            <i class="fas fa-envelope me-1"></i>
                            <a href="mailto:{{ $siteSettings->company_email }}" class="text-white text-decoration-none">
                                {{ $siteSettings->company_email }}
                            </a>
                        @endif
                    </span>
                @endif
            </div>

            <!-- Copyright -->
            <div class="text-center">
                <small class="text-white-50">&copy; {{ date('Y') }}
                    {{ $siteSettings->site_name ?? config('app.name', 'atakodb2b') }}. Tüm hakları saklıdır.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // CSRF token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Sepet güncelleme fonksiyonu
        function updateCartCount() {
            $.get('{{ route("cart.count") }}', function (data) {
                $('#cart-count').text(data.count);
                $('#cart-total').text(data.total);
                $('#cart-count-mobile').text(data.count);
            });
        }

        // Kampanya Modal'ı aç
        function openCampaignModal() {
            // Eğer specialCampaignModal varsa (ana sayfadayız)
            if ($('#specialCampaignModal').length) {
                var campaignModal = new bootstrap.Modal(document.getElementById('specialCampaignModal'));
                campaignModal.show();
            } else {
                // Ana sayfada değilsek, ana sayfaya yönlendir ve modal'ı aç
                window.location.href = '{{ route("home") }}?show_campaign=1';
            }
        }

        // Add to cart function
        function addToCart(productId, quantity, productCampaignId) {
            $.ajax({
                url: '{{ route("cart.add") }}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    product_id: productId,
                    quantity: quantity,
                    product_campaign_id: productCampaignId || null
                },
                success: function (response) {
                    if (response.success) {
                        updateCartCount(); // Hem count hem total'ı güncelle
                        showNotification(response.message, 'success');
                    }
                },
                error: function (xhr) {
                    console.error('Add to cart error:', xhr);
                    let message = 'Sepete eklenirken hata oluştu';

                    if (xhr.status === 419) {
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

                    showNotification(message, 'error');
                }
            });
        }

        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Update icon
            const icon = document.getElementById('theme-icon');
            if (newTheme === 'dark') {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }

        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            const icon = document.getElementById('theme-icon');
            if (savedTheme === 'dark' && icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        });

        function showNotification(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alert = `
                <div class="alert ${alertClass} alert-dismissible fade show notification-alert" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            $('main').prepend(alert);

            setTimeout(function () {
                $('.notification-alert').fadeOut(function () {
                    $(this).remove();
                });
            }, 3000);
        }

        // Ana sayfa linkini görünüm tercihi ile yönlendir
        document.addEventListener('DOMContentLoaded', function () {
            const homeLink = document.getElementById('navbar-home-link');
            if (homeLink) {
                homeLink.addEventListener('click', function (e) {
                    const preferredView = localStorage.getItem('preferred_view');
                    if (preferredView) {
                        e.preventDefault();
                        window.location.href = '{{ route("home") }}?view=' + preferredView;
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>