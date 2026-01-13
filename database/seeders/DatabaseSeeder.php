<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\SettingSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@atakodb2b.com',
            'password' => Hash::make('admin123'),
            'user_type' => 'admin',
            'is_active' => true,
        ]);

        // Create Customer User
        User::create([
            'name' => 'Müşteri Kullanıcı',
            'email' => 'musteri@atakodb2b.com',
            'password' => Hash::make('musteri123'),
            'user_type' => 'musteri',
            'musteri_kodu' => 'MUST001',
            'musteri_adi' => 'Örnek Müşteri A.Ş.',
            'adres' => 'Örnek Mahallesi, Örnek Sokak No:1',
            'ilce' => 'Kadıköy',
            'il' => 'İstanbul',
            'telefon' => '0212 555 0000',
            'mail_adresi' => 'musteri@ornek.com',
            'vergi_dairesi' => 'Kadıköy Vergi Dairesi',
            'vergi_kimlik_numarasi' => '1234567890',
            'is_active' => true,
        ]);

        // Create Plasiyer User
        User::create([
            'name' => 'Plasiyer Kullanıcı',
            'email' => 'plasiyer@atakodb2b.com',
            'password' => Hash::make('plasiyer123'),
            'user_type' => 'plasiyer',
            'plasiyer_kodu' => 'PLS001',
            'is_active' => true,
        ]);

        // Create sample products (Pharmaceutical products)
        $products = [
            [
                'urun_kodu' => 'ILC001',
                'urun_adi' => 'Parol 500mg Tablet - 20 Tablet',
                'barkod' => '8690000000001',
                'satis_fiyati' => 45.50,
                'kdv_orani' => 8,
                'marka' => 'Atabay',
                'grup' => 'Ağrı Kesici',
                'is_active' => true,
            ],
            [
                'urun_kodu' => 'ILC002',
                'urun_adi' => 'Arveles 25mg Tablet - 30 Tablet',
                'barkod' => '8690000000002',
                'satis_fiyati' => 89.90,
                'kdv_orani' => 8,
                'marka' => 'Bayer',
                'grup' => 'Antienflamatuar',
                'is_active' => true,
            ],
            [
                'urun_kodu' => 'ILC003',
                'urun_adi' => 'Majezik 500mg Tablet - 20 Tablet',
                'barkod' => '8690000000003',
                'satis_fiyati' => 65.00,
                'kdv_orani' => 8,
                'marka' => 'Sanovel',
                'grup' => 'Ağrı Kesici',
                'is_active' => true,
            ],
            [
                'urun_kodu' => 'ILC004',
                'urun_adi' => 'Calpol 120mg/5ml Şurup - 100ml',
                'barkod' => '8690000000004',
                'satis_fiyati' => 35.00,
                'kdv_orani' => 8,
                'marka' => 'GSK',
                'grup' => 'Çocuk İlaçları',
                'is_active' => true,
            ],
            [
                'urun_kodu' => 'ILC005',
                'urun_adi' => 'Augmentin 1000mg Tablet - 14 Tablet',
                'barkod' => '8690000000005',
                'satis_fiyati' => 150.00,
                'kdv_orani' => 8,
                'marka' => 'GSK',
                'grup' => 'Antibiyotik',
                'is_active' => true,
            ],
            [
                'urun_kodu' => 'ILC006',
                'urun_adi' => 'Coraspin 100mg Tablet - 30 Tablet',
                'barkod' => '8690000000006',
                'satis_fiyati' => 42.00,
                'kdv_orani' => 8,
                'marka' => 'Bayer',
                'grup' => 'Kardiyovasküler',
                'is_active' => true,
            ],
            [
                'urun_kodu' => 'ILC007',
                'urun_adi' => 'Ventolin İnhaler - 100mcg',
                'barkod' => '8690000000007',
                'satis_fiyati' => 95.00,
                'kdv_orani' => 8,
                'marka' => 'GSK',
                'grup' => 'Solunum',
                'is_active' => true,
            ],
            [
                'urun_kodu' => 'ILC008',
                'urun_adi' => 'Desferal 500mg Flakon',
                'barkod' => '8690000000008',
                'satis_fiyati' => 285.00,
                'kdv_orani' => 8,
                'marka' => 'Novartis',
                'grup' => 'Özel İlaçlar',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create sample sliders
        Slider::create([
            'title' => 'Hoş Geldiniz',
            'description' => 'İlaç ve Sağlık Ürünleri B2B Platformu',
            'image' => 'sliders/slider1.jpg',
            'order' => 1,
            'is_active' => true,
        ]);

        Slider::create([
            'title' => 'Geniş Ürün Yelpazesi',
            'description' => 'Binlerce ilaç ve sağlık ürünü tek platformda',
            'image' => 'sliders/slider2.jpg',
            'order' => 2,
            'is_active' => true,
        ]);

        $this->call(SettingSeeder::class);
    }
}


