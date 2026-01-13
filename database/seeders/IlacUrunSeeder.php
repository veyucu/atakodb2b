<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class IlacUrunSeeder extends Seeder
{
    public function run()
    {
        // Önce mevcut verileri sil
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('order_items')->truncate();
        \DB::table('orders')->truncate();
        \DB::table('carts')->truncate();
        Product::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $markalar = ['Bayer', 'Pfizer', 'Roche', 'Novartis', 'Sanofi', 'GSK', 'AstraZeneca', 'Abbott', 'Merck', 'Johnson & Johnson', 'Lilly', 'Amgen', 'Abdi İbrahim', 'Eczacıbaşı', 'Deva', 'Bilim', 'Nobel', 'Mustafa Nevzat'];
        
        $gruplar = ['Antibiyotik', 'Ağrı Kesici', 'Vitamin', 'Grip İlacı', 'Tansiyon', 'Diyabet', 'Kalp', 'Mide', 'Alerji', 'Hormon', 'Göz Damlası', 'Sirup', 'Merhem', 'Sprey'];
        
        // Gerçekçi ilaç isimleri
        $ilacOnekleri = ['Para', 'İbu', 'Aspi', 'Amoxi', 'Cipro', 'Metro', 'Diclo', 'Napro', 'Indo', 'Keto', 'Ome', 'Panto', 'Levo', 'Cefe', 'Azithro', 'Clari', 'Eryth', 'Doxy', 'Tetra', 'Vanco'];
        $ilacSonekleri = ['rin', 'cin', 'xen', 'zol', 'dol', 'flex', 'tabs', 'fort', 'plus', 'max', 'forte', 'retard', 'sr', 'mr', 'cr', 'duo', 'tri', 'comp'];
        
        $dozlar = ['250mg', '500mg', '1000mg', '5mg', '10mg', '20mg', '40mg', '50mg', '100mg', '200mg', '400mg', '800mg'];
        $formlar = ['Tablet', 'Kapsül', 'Sirup', 'Ampul', 'Flakon', 'Film Tablet', 'Damla', 'Krem', 'Merhem', 'Sprey'];
        
        // Muadil grupları (her grup aynı muadil koduna sahip olacak)
        $muadilGruplari = [
            'MUA001' => ['Parol', 'Minoset', 'Acetalgin', 'Aprol'],
            'MUA002' => ['Majezik', 'Rapidol', 'Dolarit', 'Dolofen'],
            'MUA003' => ['Augmentin', 'Amoklin', 'Klamoks', 'Largopen'],
            'MUA004' => ['Cipro', 'Ciproktan', 'Cipronex', 'Cifran'],
            'MUA005' => ['Voltaren', 'Dikloron', 'Dikloflam', 'Arthrex'],
            'MUA006' => ['Losec', 'Omeprol', 'Ulcemex', 'Gasec'],
            'MUA007' => ['Zoloft', 'Sertral', 'Lustral', 'Sertralin'],
            'MUA008' => ['Lipitor', 'Atoris', 'Atorvox', 'Lipomax'],
            'MUA009' => ['Norvasc', 'Amlovas', 'Amlodil', 'Vasodil'],
            'MUA010' => ['Lantus', 'Glargin', 'Abasaglar', 'Toujeo'],
        ];
        
        $products = [];
        $usedBarcodes = [];
        
        for ($i = 1; $i <= 1000; $i++) {
            // Benzersiz barkod oluştur
            do {
                $barkod = '869' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            } while (in_array($barkod, $usedBarcodes));
            $usedBarcodes[] = $barkod;
            
            // Ürün kodu = Barkod
            $urunKodu = $barkod;
            
            // Muadil grup mu?
            $muadilKodu = null;
            if ($i <= 40) { // İlk 40 ürün muadil gruplara dahil
                $muadilKeys = array_keys($muadilGruplari);
                $muadilIndex = (int)(($i - 1) / 4);
                if ($muadilIndex < count($muadilKeys)) {
                    $muadilKodu = $muadilKeys[$muadilIndex];
                    $muadilGrup = $muadilGruplari[$muadilKodu];
                    $ilacAdi = $muadilGrup[($i - 1) % count($muadilGrup)];
                }
            }
            
            // Normal ürün adı
            if (!isset($ilacAdi)) {
                $onek = $ilacOnekleri[array_rand($ilacOnekleri)];
                $sonek = $ilacSonekleri[array_rand($ilacSonekleri)];
                $ilacAdi = $onek . $sonek;
            }
            
            $doz = $dozlar[array_rand($dozlar)];
            $form = $formlar[array_rand($formlar)];
            $urunAdi = $ilacAdi . ' ' . $doz . ' ' . $form;
            
            $marka = $markalar[array_rand($markalar)];
            $grup = $gruplar[array_rand($gruplar)];
            
            // Fiyatlar
            $satisFiyati = rand(50, 5000) / 10; // 5 TL - 500 TL
            $kurumIskonto = rand(5, 20);
            $eczaciKari = rand(10, 40);
            $ticariIskonto = rand(2, 10);
            $kdvOrani = rand(1, 10) <= 8 ? 10 : 20; // %80 ihtimalle 10%, %20 ihtimalle 20%
            
            // Net fiyat hesapla (ZORUNLU)
            $netFiyat = $satisFiyati * (1 - $kurumIskonto/100) * (1 - $eczaciKari/100) * (1 - $ticariIskonto/100);
            $netFiyat = round($netFiyat * (1 + $kdvOrani/100), 2);
            
            $depocuFiyati = $netFiyat * rand(105, 115) / 100;
            
            // Mal fazlası (bazı ürünlerde)
            $mf = null;
            if (rand(1, 10) <= 6) { // %60 ihtimalle
                $malFazlasiMiktar = [5, 10, 15, 20][array_rand([5, 10, 15, 20])];
                $alisMiktari = $malFazlasiMiktar * [2, 3, 4, 5][array_rand([2, 3, 4, 5])];
                $mf = $alisMiktari . '+' . $malFazlasiMiktar;
            }
            
            // Stok (Gerçekçi dağılım)
            $stokRand = rand(1, 100);
            if ($stokRand <= 15) {
                // %15 stokta yok
                $bakiye = 0;
            } elseif ($stokRand <= 35) {
                // %20 az stokta (1-20 adet)
                $bakiye = rand(1, 20);
            } elseif ($stokRand <= 70) {
                // %35 orta stokta (21-100 adet)
                $bakiye = rand(21, 100);
            } else {
                // %30 bol stokta (101-500 adet)
                $bakiye = rand(101, 500);
            }
            
            // Özel liste (her 5 üründen 1'i)
            $ozelListe = ($i % 5 == 0);
            
            // Resim URL (pharmaceutical themed)
            $imageUrls = [
                'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&h=300&fit=crop', // Pills
                'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=400&h=300&fit=crop', // Medicine
                'https://images.unsplash.com/photo-1585435557343-3b092031a831?w=400&h=300&fit=crop', // Tablets
                'https://images.unsplash.com/photo-1471864190281-a93a3070b6de?w=400&h=300&fit=crop', // Pills bottle
                'https://images.unsplash.com/photo-1550572017-4586e8a28a78?w=400&h=300&fit=crop', // Medicine bottle
                'https://images.unsplash.com/photo-1587854680352-936b22b91030?w=400&h=300&fit=crop', // Capsules
                'https://images.unsplash.com/photo-1563213126-a4273aed2016?w=400&h=300&fit=crop', // Pills close up
                'https://images.unsplash.com/photo-1584017911766-d451b3d0e843?w=400&h=300&fit=crop', // Medicine pack
            ];
            $imageUrl = $imageUrls[array_rand($imageUrls)];
            
            $products[] = [
                'urun_kodu' => $urunKodu,
                'urun_adi' => $urunAdi,
                'barkod' => $barkod,
                'satis_fiyati' => $satisFiyati,
                'kdv_orani' => $kdvOrani,
                'marka' => $marka,
                'grup' => $grup,
                'kurum_iskonto' => $kurumIskonto,
                'eczaci_kari' => $eczaciKari,
                'ticari_iskonto' => $ticariIskonto,
                'depocu_fiyati' => round($depocuFiyati, 2),
                'net_fiyat_manuel' => $netFiyat,
                'mf' => $mf,
                'muadil_kodu' => $muadilKodu,
                'bakiye' => $bakiye,
                'ozel_liste' => $ozelListe,
                'urun_resmi' => $imageUrl,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Her 100 üründe bir batch insert yap (performans için)
            if (count($products) >= 100) {
                Product::insert($products);
                $products = [];
                $this->command->info("İlk " . $i . " ürün eklendi...");
            }
            
            // Değişkenleri temizle
            unset($ilacAdi);
        }
        
        // Kalan ürünleri ekle
        if (count($products) > 0) {
            Product::insert($products);
        }
        
        $this->command->info("✅ 1000 ilaç ürünü başarıyla eklendi!");
        $this->command->info("✅ 10 muadil grup oluşturuldu (40 ürün)");
        $this->command->info("✅ 200 ürün özel listeye eklendi");
        $this->command->info("✅ Tüm ürünlerde net fiyat mevcut");
    }
}

