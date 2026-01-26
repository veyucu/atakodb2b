<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CariEkstreController extends Controller
{
    /**
     * .NET Backend API URL
     */
    protected $erpApiUrl;
    protected $erpApiKey;

    public function __construct()
    {
        $this->erpApiUrl = config('services.erp.url', 'http://localhost:5000');
        $this->erpApiKey = config('services.erp.api_key', env('ERP_API_KEY', ''));
    }

    /**
     * Show the account statement page.
     */
    public function index(Request $request)
    {
        // Tarih aralığı varsayılan olarak yılın başından bugüne
        $baslangicTarihi = $request->get('baslangic_tarihi', Carbon::now()->startOfYear()->format('Y-m-d'));
        $bitisTarihi = $request->get('bitis_tarihi', Carbon::now()->format('Y-m-d'));

        // Müşteri kodunu al (plasiyer/admin için seçilen müşteri, diğerleri için kendi kodu)
        $musteriKodu = $this->getMusteriKodu();
        $musteriAdi = $this->getMusteriAdi();

        // Debug log
        \Log::info('Cari Ekstre isteği', [
            'musteriKodu' => $musteriKodu,
            'baslangicTarihi' => $baslangicTarihi,
            'bitisTarihi' => $bitisTarihi
        ]);

        // Müşteri kodu boş ise boş veri döndür
        if (empty($musteriKodu)) {
            \Log::warning('Cari Ekstre: Müşteri kodu boş');
            $hareketler = [];
            $toplamBorc = 0;
            $toplamAlacak = 0;
            $genelBakiye = 0;
            $devirBakiye = 0;
        } else {
            // ERP API'den cari ekstre verilerini al
            $ekstreData = $this->getEkstreFromApi($musteriKodu, $baslangicTarihi, $bitisTarihi);

            // Hareketleri formatla
            $hareketler = $ekstreData['hareketler'] ?? [];
            $toplamBorc = $ekstreData['toplamBorc'] ?? 0;
            $toplamAlacak = $ekstreData['toplamAlacak'] ?? 0;
            $genelBakiye = $ekstreData['genelBakiye'] ?? 0;
            $devirBakiye = $ekstreData['devirBakiye'] ?? 0;
        }

        return view('cari-ekstre', compact(
            'hareketler',
            'baslangicTarihi',
            'bitisTarihi',
            'musteriKodu',
            'musteriAdi',
            'toplamBorc',
            'toplamAlacak',
            'genelBakiye',
            'devirBakiye'
        ));
    }

    /**
     * Fatura detayını getir (AJAX için)
     */
    public function getFaturaDetay(Request $request)
    {
        $belgeNo = $request->get('belge_no');
        $musteriKodu = $this->getMusteriKodu();

        if (empty($belgeNo)) {
            return response()->json(['success' => false, 'message' => 'Belge no zorunludur']);
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(['X-API-Key' => $this->erpApiKey])
                ->get("{$this->erpApiUrl}/api/FaturaDetay", [
                    'belgeNo' => $belgeNo,
                    'musteriKodu' => $musteriKodu
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Fatura detay alınamadı'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API hatası: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ERP API'den cari ekstre verilerini al
     */
    protected function getEkstreFromApi($musteriKodu, $baslangicTarihi, $bitisTarihi)
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['X-API-Key' => $this->erpApiKey])
                ->get("{$this->erpApiUrl}/api/CariEkstre", [
                    'musteriKodu' => $musteriKodu,
                    'baslangicTarihi' => $baslangicTarihi,
                    'bitisTarihi' => $bitisTarihi
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] ?? false) {
                    // API'den gelen hareketleri view formatına dönüştür
                    $hareketler = collect($data['hareketler'] ?? [])->map(function ($hareket) {
                        return [
                            'tarih' => $hareket['tarih'] ?? '',
                            'vade_tarihi' => $hareket['vadeTarihi'] ?? $hareket['tarih'],
                            'belge_no' => $hareket['belgeNo'] ?? '',
                            'hareket_turu' => $hareket['hareketAdi'] ?? $hareket['hareketTuru'] ?? '',
                            'hareket_turu_kod' => $hareket['hareketTuru'] ?? '',
                            'aciklama' => $hareket['aciklama'] ?? '',
                            'borc' => $hareket['borc'] ?? 0,
                            'alacak' => $hareket['alacak'] ?? 0,
                            'bakiye' => $hareket['bakiye'] ?? 0,
                            'ent_ref_key' => $hareket['entRefKey'] ?? '',
                        ];
                    })->toArray();

                    return [
                        'hareketler' => $hareketler,
                        'toplamBorc' => $data['toplamBorc'] ?? 0,
                        'toplamAlacak' => $data['toplamAlacak'] ?? 0,
                        'genelBakiye' => $data['genelBakiye'] ?? 0,
                        'devirBakiye' => $data['devirBakiye'] ?? 0,
                    ];
                }
            }

            // API hatası durumunda boş döndür
            \Log::warning('ERP API hatası', [
                'musteriKodu' => $musteriKodu,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

        } catch (\Exception $e) {
            \Log::error('ERP API bağlantı hatası', [
                'musteriKodu' => $musteriKodu,
                'error' => $e->getMessage()
            ]);
        }

        // Hata durumunda boş veri döndür
        return [
            'hareketler' => [],
            'toplamBorc' => 0,
            'toplamAlacak' => 0,
            'genelBakiye' => 0,
            'devirBakiye' => 0,
        ];
    }

    /**
     * Get customer code for the current user/session.
     * username = Netsis cari kodu
     */
    protected function getMusteriKodu()
    {
        $user = Auth::user();

        // Plasiyer veya admin ise, seçili müşterinin kodunu kullan
        if (($user->isPlasiyer() || $user->isAdmin()) && session()->has('selected_customer_id')) {
            $selectedCustomer = \App\Models\User::find(session('selected_customer_id'));
            return $selectedCustomer?->username;
        }

        return $user->username;
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
}
