<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CariEkstreController extends Controller
{
    /**
     * Show the account statement page.
     */
    public function index(Request $request)
    {
        // Tarih aralığı varsayılan olarak son 30 gün
        $baslangicTarihi = $request->get('baslangic_tarihi', Carbon::now()->subDays(30)->format('Y-m-d'));
        $bitisTarihi = $request->get('bitis_tarihi', Carbon::now()->format('Y-m-d'));

        // Müşteri kodunu al (plasiyer/admin için seçilen müşteri, diğerleri için kendi kodu)
        $musteriKodu = $this->getMusteriKodu();
        $musteriAdi = $this->getMusteriAdi();

        // Demo veriler (gerçek ERP entegrasyonunda API'den çekilecek)
        $hareketler = $this->getEkstreVerileri($musteriKodu, $baslangicTarihi, $bitisTarihi);

        // Toplamları hesapla
        $toplamBorc = collect($hareketler)->sum('borc');
        $toplamAlacak = collect($hareketler)->sum('alacak');
        $genelBakiye = $toplamBorc - $toplamAlacak;

        return view('cari-ekstre', compact(
            'hareketler',
            'baslangicTarihi',
            'bitisTarihi',
            'musteriKodu',
            'musteriAdi',
            'toplamBorc',
            'toplamAlacak',
            'genelBakiye'
        ));
    }

    /**
     * Get customer code for the current user/session.
     */
    protected function getMusteriKodu()
    {
        $user = Auth::user();

        // Plasiyer veya admin ise, seçili müşterinin kodunu kullan
        if (($user->isPlasiyer() || $user->isAdmin()) && session()->has('selected_customer_id')) {
            $selectedCustomer = \App\Models\User::find(session('selected_customer_id'));
            return $selectedCustomer ? $selectedCustomer->musteri_kodu : $user->musteri_kodu;
        }

        return $user->musteri_kodu;
    }

    /**
     * Get customer name for the current user/session.
     */
    protected function getMusteriAdi()
    {
        $user = Auth::user();

        // Plasiyer veya admin ise, seçili müşterinin adını kullan
        if (($user->isPlasiyer() || $user->isAdmin()) && session()->has('selected_customer_name')) {
            return session('selected_customer_name');
        }

        return $user->musteri_adi ?? $user->name;
    }

    /**
     * Get account statement data.
     * Demo verileri - gerçek implementasyonda ERP API'den çekilecek.
     */
    protected function getEkstreVerileri($musteriKodu, $baslangicTarihi, $bitisTarihi)
    {
        // Demo veriler - Gerçek ERP entegrasyonunda burası API çağrısı ile değiştirilecek
        // Müşteri koduna göre farklı demo veriler
        $now = Carbon::now();

        // Müşteri koduna göre farklı veri setleri
        $demoVerilerMap = [
            '120-001' => [
                [
                    'tarih' => $now->copy()->subDays(28)->format('Y-m-d'),
                    'belge_no' => 'FAT-2026-0101',
                    'hareket_turu' => 'Satış Faturası',
                    'aciklama' => 'Aralık ayı satış faturası',
                    'borc' => 25000.00,
                    'alacak' => 0.00,
                    'bakiye' => 25000.00,
                    'kalemler' => [
                        ['urun_kodu' => 'URN-001', 'urun_adi' => 'Parol 500mg Tablet', 'miktar' => 50, 'birim_fiyat' => 150.00, 'tutar' => 7500.00],
                        ['urun_kodu' => 'URN-002', 'urun_adi' => 'Majezik 100mg Film Tablet', 'miktar' => 100, 'birim_fiyat' => 85.00, 'tutar' => 8500.00],
                        ['urun_kodu' => 'URN-003', 'urun_adi' => 'Nurofen Cold Flu', 'miktar' => 75, 'birim_fiyat' => 120.00, 'tutar' => 9000.00],
                    ],
                ],
                [
                    'tarih' => $now->copy()->subDays(22)->format('Y-m-d'),
                    'belge_no' => 'TAH-2026-0102',
                    'hareket_turu' => 'Tahsilat',
                    'aciklama' => 'Havale ile ödeme',
                    'borc' => 0.00,
                    'alacak' => 15000.00,
                    'bakiye' => 10000.00,
                ],
                [
                    'tarih' => $now->copy()->subDays(15)->format('Y-m-d'),
                    'belge_no' => 'FAT-2026-0103',
                    'hareket_turu' => 'Satış Faturası',
                    'aciklama' => 'Ocak ayı ilk sipariş',
                    'borc' => 18500.00,
                    'alacak' => 0.00,
                    'bakiye' => 28500.00,
                    'kalemler' => [
                        ['urun_kodu' => 'URN-004', 'urun_adi' => 'Aspirin 100mg', 'miktar' => 200, 'birim_fiyat' => 45.00, 'tutar' => 9000.00],
                        ['urun_kodu' => 'URN-005', 'urun_adi' => 'Voltaren Emulgel', 'miktar' => 40, 'birim_fiyat' => 237.50, 'tutar' => 9500.00],
                    ],
                ],
                [
                    'tarih' => $now->copy()->subDays(8)->format('Y-m-d'),
                    'belge_no' => 'TAH-2026-0104',
                    'hareket_turu' => 'Tahsilat',
                    'aciklama' => 'Kredi kartı ile ödeme',
                    'borc' => 0.00,
                    'alacak' => 20000.00,
                    'bakiye' => 8500.00,
                ],
                [
                    'tarih' => $now->copy()->subDays(3)->format('Y-m-d'),
                    'belge_no' => 'FAT-2026-0105',
                    'hareket_turu' => 'Satış Faturası',
                    'aciklama' => 'Acil sipariş faturası',
                    'borc' => 7200.00,
                    'alacak' => 0.00,
                    'bakiye' => 15700.00,
                    'kalemler' => [
                        ['urun_kodu' => 'URN-006', 'urun_adi' => 'Augmentin BID 1000mg', 'miktar' => 30, 'birim_fiyat' => 180.00, 'tutar' => 5400.00],
                        ['urun_kodu' => 'URN-007', 'urun_adi' => 'Cipro 500mg', 'miktar' => 20, 'birim_fiyat' => 90.00, 'tutar' => 1800.00],
                    ],
                ],
            ],
            '120-002' => [
                [
                    'tarih' => $now->copy()->subDays(30)->format('Y-m-d'),
                    'belge_no' => 'FAT-2026-0201',
                    'hareket_turu' => 'Satış Faturası',
                    'aciklama' => 'Toplu sipariş faturası',
                    'borc' => 45000.00,
                    'alacak' => 0.00,
                    'bakiye' => 45000.00,
                    'kalemler' => [
                        ['urun_kodu' => 'URN-010', 'urun_adi' => 'Nexium 40mg', 'miktar' => 100, 'birim_fiyat' => 180.00, 'tutar' => 18000.00],
                        ['urun_kodu' => 'URN-011', 'urun_adi' => 'Pantpas 40mg', 'miktar' => 80, 'birim_fiyat' => 125.00, 'tutar' => 10000.00],
                        ['urun_kodu' => 'URN-012', 'urun_adi' => 'Coraspin 100mg', 'miktar' => 150, 'birim_fiyat' => 65.00, 'tutar' => 9750.00],
                        ['urun_kodu' => 'URN-013', 'urun_adi' => 'Beloc 50mg', 'miktar' => 60, 'birim_fiyat' => 120.83, 'tutar' => 7250.00],
                    ],
                ],
                [
                    'tarih' => $now->copy()->subDays(25)->format('Y-m-d'),
                    'belge_no' => 'İAD-2026-0202',
                    'hareket_turu' => 'İade Faturası',
                    'aciklama' => 'Kısmi ürün iadesi',
                    'borc' => 0.00,
                    'alacak' => 5000.00,
                    'bakiye' => 40000.00,
                    'kalemler' => [
                        ['urun_kodu' => 'URN-011', 'urun_adi' => 'Pantpas 40mg', 'miktar' => 40, 'birim_fiyat' => 125.00, 'tutar' => 5000.00],
                    ],
                ],
                [
                    'tarih' => $now->copy()->subDays(18)->format('Y-m-d'),
                    'belge_no' => 'TAH-2026-0203',
                    'hareket_turu' => 'Tahsilat',
                    'aciklama' => 'EFT ile ödeme',
                    'borc' => 0.00,
                    'alacak' => 30000.00,
                    'bakiye' => 10000.00,
                ],
                [
                    'tarih' => $now->copy()->subDays(10)->format('Y-m-d'),
                    'belge_no' => 'FAT-2026-0204',
                    'hareket_turu' => 'Satış Faturası',
                    'aciklama' => 'Ek sipariş',
                    'borc' => 12000.00,
                    'alacak' => 0.00,
                    'bakiye' => 22000.00,
                    'kalemler' => [
                        ['urun_kodu' => 'URN-014', 'urun_adi' => 'Cipralex 10mg', 'miktar' => 50, 'birim_fiyat' => 140.00, 'tutar' => 7000.00],
                        ['urun_kodu' => 'URN-015', 'urun_adi' => 'Xanax 0.5mg', 'miktar' => 25, 'birim_fiyat' => 200.00, 'tutar' => 5000.00],
                    ],
                ],
                [
                    'tarih' => $now->copy()->subDays(5)->format('Y-m-d'),
                    'belge_no' => 'TAH-2026-0205',
                    'hareket_turu' => 'Tahsilat',
                    'aciklama' => 'Nakit tahsilat',
                    'borc' => 0.00,
                    'alacak' => 10000.00,
                    'bakiye' => 12000.00,
                ],
                [
                    'tarih' => $now->copy()->subDays(1)->format('Y-m-d'),
                    'belge_no' => 'FAT-2026-0206',
                    'hareket_turu' => 'Satış Faturası',
                    'aciklama' => 'Günlük sipariş',
                    'borc' => 8500.00,
                    'alacak' => 0.00,
                    'bakiye' => 20500.00,
                    'kalemler' => [
                        ['urun_kodu' => 'URN-016', 'urun_adi' => 'Delix 5mg', 'miktar' => 40, 'birim_fiyat' => 112.50, 'tutar' => 4500.00],
                        ['urun_kodu' => 'URN-017', 'urun_adi' => 'Concor 5mg', 'miktar' => 50, 'birim_fiyat' => 80.00, 'tutar' => 4000.00],
                    ],
                ],
            ],
            '120-003' => [
                [
                    'tarih' => $now->copy()->subDays(27)->format('Y-m-d'),
                    'belge_no' => 'FAT-2026-0301',
                    'hareket_turu' => 'Satış Faturası',
                    'aciklama' => 'Dönem başı faturası',
                    'borc' => 32000.00,
                    'alacak' => 0.00,
                    'bakiye' => 32000.00,
                    'kalemler' => [
                        ['urun_kodu' => 'URN-020', 'urun_adi' => 'Lantus Solostar', 'miktar' => 20, 'birim_fiyat' => 850.00, 'tutar' => 17000.00],
                        ['urun_kodu' => 'URN-021', 'urun_adi' => 'Novorapid Flexpen', 'miktar' => 15, 'birim_fiyat' => 600.00, 'tutar' => 9000.00],
                        ['urun_kodu' => 'URN-022', 'urun_adi' => 'Glucophage 1000mg', 'miktar' => 100, 'birim_fiyat' => 60.00, 'tutar' => 6000.00],
                    ],
                ],
                [
                    'tarih' => $now->copy()->subDays(20)->format('Y-m-d'),
                    'belge_no' => 'TAH-2026-0302',
                    'hareket_turu' => 'Tahsilat',
                    'aciklama' => 'Çek tahsilatı',
                    'borc' => 0.00,
                    'alacak' => 25000.00,
                    'bakiye' => 7000.00,
                ],
                [
                    'tarih' => $now->copy()->subDays(12)->format('Y-m-d'),
                    'belge_no' => 'FAT-2026-0303',
                    'hareket_turu' => 'Satış Faturası',
                    'aciklama' => 'Haftalık sipariş',
                    'borc' => 15000.00,
                    'alacak' => 0.00,
                    'bakiye' => 22000.00,
                    'kalemler' => [
                        ['urun_kodu' => 'URN-023', 'urun_adi' => 'Euthyrox 100mcg', 'miktar' => 200, 'birim_fiyat' => 35.00, 'tutar' => 7000.00],
                        ['urun_kodu' => 'URN-024', 'urun_adi' => 'Synthroid 50mcg', 'miktar' => 100, 'birim_fiyat' => 80.00, 'tutar' => 8000.00],
                    ],
                ],
                [
                    'tarih' => $now->copy()->subDays(6)->format('Y-m-d'),
                    'belge_no' => 'İAD-2026-0304',
                    'hareket_turu' => 'İade Faturası',
                    'aciklama' => 'Son kullanma tarihi geçmiş ürün iadesi',
                    'borc' => 0.00,
                    'alacak' => 2500.00,
                    'bakiye' => 19500.00,
                    'kalemler' => [
                        ['urun_kodu' => 'URN-023', 'urun_adi' => 'Euthyrox 100mcg (SKT Geçmiş)', 'miktar' => 50, 'birim_fiyat' => 35.00, 'tutar' => 1750.00],
                        ['urun_kodu' => 'URN-022', 'urun_adi' => 'Glucophage 1000mg (SKT Geçmiş)', 'miktar' => 12, 'birim_fiyat' => 62.50, 'tutar' => 750.00],
                    ],
                ],
                [
                    'tarih' => $now->copy()->subDays(2)->format('Y-m-d'),
                    'belge_no' => 'TAH-2026-0305',
                    'hareket_turu' => 'Tahsilat',
                    'aciklama' => 'Kısmi ödeme',
                    'borc' => 0.00,
                    'alacak' => 10000.00,
                    'bakiye' => 9500.00,
                ],
            ],
        ];

        // Müşteri koduna göre veri seç, yoksa varsayılan veri kullan
        $demoVeriler = $demoVerilerMap[$musteriKodu] ?? $demoVerilerMap['120-001'];

        // Tarih aralığına göre filtrele ve vade tarihini ekle
        $baslangic = Carbon::parse($baslangicTarihi);
        $bitis = Carbon::parse($bitisTarihi);

        return collect($demoVeriler)->filter(function ($hareket) use ($baslangic, $bitis) {
            $hareketTarihi = Carbon::parse($hareket['tarih']);
            return $hareketTarihi->between($baslangic, $bitis);
        })->map(function ($hareket) {
            // Vade tarihini ekle: Fatura için 30 gün, tahsilat için aynı gün
            $tarih = Carbon::parse($hareket['tarih']);
            if (str_contains($hareket['hareket_turu'], 'Fatura')) {
                $hareket['vade_tarihi'] = $tarih->copy()->addDays(30)->format('Y-m-d');
            } else {
                $hareket['vade_tarihi'] = $hareket['tarih'];
            }
            return $hareket;
        })->values()->toArray();
    }
}
