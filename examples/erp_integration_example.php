<?php

/**
 * atakodb2b ERP Entegrasyon Örneği
 * 
 * Bu dosya, ERP sisteminizden atakodb2b API'sine veri aktarımı için örnek bir script'tir.
 */

class AtakoDB2BApiClient
{
    private $baseUrl;
    private $token;
    private $deviceName;

    public function __construct($baseUrl, $deviceName = 'ERP System')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->deviceName = $deviceName;
    }

    /**
     * Login ve token al
     */
    public function login($email, $password)
    {
        $response = $this->request('POST', '/auth/login', [
            'email' => $email,
            'password' => $password,
            'device_name' => $this->deviceName
        ], false);

        if (isset($response['token'])) {
            $this->token = $response['token'];
            return true;
        }

        return false;
    }

    /**
     * Kullanıcı senkronizasyonu
     */
    public function syncUsers(array $users)
    {
        return $this->request('POST', '/users/sync', [
            'users' => $users
        ]);
    }

    /**
     * Ürün senkronizasyonu
     */
    public function syncProducts(array $products)
    {
        return $this->request('POST', '/products/sync', [
            'products' => $products
        ]);
    }

    /**
     * Tek kullanıcı oluştur/güncelle
     */
    public function upsertUser($musteriKodu, $userData)
    {
        // Önce kullanıcıyı bul
        $existingUser = $this->findUserByCode($musteriKodu);

        if ($existingUser) {
            // Güncelle
            return $this->request('PUT', '/users/' . $existingUser['id'], $userData);
        } else {
            // Yeni oluştur
            return $this->request('POST', '/users', $userData);
        }
    }

    /**
     * Tek ürün oluştur/güncelle
     */
    public function upsertProduct($urunKodu, $productData)
    {
        // Önce ürünü bul
        $existingProduct = $this->findProductByCode($urunKodu);

        if ($existingProduct) {
            // Güncelle
            return $this->request('PUT', '/products/' . $existingProduct['id'], $productData);
        } else {
            // Yeni oluştur
            return $this->request('POST', '/products', $productData);
        }
    }

    /**
     * Kullanıcı koduna göre bul
     */
    public function findUserByCode($musteriKodu)
    {
        $response = $this->request('GET', '/users/find-by-code?musteri_kodu=' . urlencode($musteriKodu));
        return $response['data'] ?? null;
    }

    /**
     * Ürün koduna göre bul
     */
    public function findProductByCode($urunKodu)
    {
        $response = $this->request('GET', '/products/find-by-code?urun_kodu=' . urlencode($urunKodu));
        return $response['data'] ?? null;
    }

    /**
     * Stok güncelle
     */
    public function updateStock($productId, $bakiye)
    {
        return $this->request('PATCH', '/products/' . $productId . '/stock', [
            'bakiye' => $bakiye
        ]);
    }

    /**
     * Fiyat güncelle
     */
    public function updatePrice($productId, $priceData)
    {
        return $this->request('PATCH', '/products/' . $productId . '/price', $priceData);
    }

    /**
     * HTTP Request
     */
    private function request($method, $endpoint, $data = [], $useAuth = true)
    {
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if ($useAuth && $this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            throw new Exception("API Error: " . ($result['message'] ?? 'Unknown error') . " (HTTP $httpCode)");
        }

        return $result;
    }
}

// ============================================================================
// KULLANIM ÖRNEKLERİ
// ============================================================================

try {
    // API Client oluştur
    $api = new AtakoDB2BApiClient('https://yourdomain.com/api', 'ERP System');

    // Login
    if (!$api->login('admin@example.com', 'password123')) {
        die("Login başarısız!\n");
    }

    echo "✓ Login başarılı\n";

    // ========================================================================
    // ÖRNEK 1: Toplu Kullanıcı Senkronizasyonu
    // ========================================================================
    
    echo "\n--- Kullanıcı Senkronizasyonu ---\n";

    // ERP'nizden kullanıcı verilerini çekin
    $users = [
        [
            'musteri_kodu' => 'M001',
            'name' => 'Eczane A',
            'email' => 'eczanea@example.com',
            'password' => 'password123', // İlk oluşturmada gerekli
            'user_type' => 'musteri',
            'musteri_adi' => 'A Eczanesi',
            'telefon' => '0555 123 4567',
            'il' => 'İstanbul',
            'ilce' => 'Kadıköy',
            'adres' => 'Test Mahallesi, Test Sokak No:1',
            'vergi_dairesi' => 'Kadıköy VD',
            'vergi_kimlik_numarasi' => '1234567890',
            'is_active' => true
        ],
        [
            'musteri_kodu' => 'M002',
            'name' => 'Eczane B',
            'email' => 'eczaneb@example.com',
            'user_type' => 'musteri',
            'telefon' => '0555 987 6543',
            'il' => 'Ankara',
            'is_active' => true
        ]
    ];

    $result = $api->syncUsers($users);
    echo "Kullanıcı Sync: {$result['created']} oluşturuldu, {$result['updated']} güncellendi\n";

    // ========================================================================
    // ÖRNEK 2: Toplu Ürün Senkronizasyonu
    // ========================================================================
    
    echo "\n--- Ürün Senkronizasyonu ---\n";

    // ERP'nizden ürün verilerini çekin
    $products = [
        [
            'urun_kodu' => 'U001',
            'urun_adi' => 'Aspirin 100mg',
            'barkod' => '8690123456789',
            'satis_fiyati' => 100.00,
            'kdv_orani' => 18.00,
            'kurum_iskonto' => 10.00,
            'eczaci_kari' => 5.00,
            'ticari_iskonto' => 3.00,
            'bakiye' => 150.00,
            'marka' => 'BAYER',
            'grup' => 'ANALJEZIK',
            'muadil_kodu' => 'MU001',
            'is_active' => true
        ],
        [
            'urun_kodu' => 'U002',
            'urun_adi' => 'Paracetamol 500mg',
            'barkod' => '8690987654321',
            'satis_fiyati' => 50.00,
            'kdv_orani' => 18.00,
            'bakiye' => 200.00,
            'marka' => 'NOBEL',
            'grup' => 'ANALJEZIK',
            'is_active' => true
        ]
    ];

    $result = $api->syncProducts($products);
    echo "Ürün Sync: {$result['created']} oluşturuldu, {$result['updated']} güncellendi\n";

    // ========================================================================
    // ÖRNEK 3: Tek Kullanıcı Oluştur/Güncelle
    // ========================================================================
    
    echo "\n--- Tek Kullanıcı İşlemi ---\n";

    $userData = [
        'musteri_kodu' => 'M003',
        'name' => 'Eczane C',
        'email' => 'eczanec@example.com',
        'password' => 'password123',
        'user_type' => 'musteri',
        'telefon' => '0555 111 2233',
        'is_active' => true
    ];

    $result = $api->upsertUser('M003', $userData);
    echo "Kullanıcı işlendi: " . $result['data']['name'] . "\n";

    // ========================================================================
    // ÖRNEK 4: Tek Ürün Oluştur/Güncelle
    // ========================================================================
    
    echo "\n--- Tek Ürün İşlemi ---\n";

    $productData = [
        'urun_kodu' => 'U003',
        'urun_adi' => 'Vitamin C 1000mg',
        'satis_fiyati' => 75.00,
        'bakiye' => 100.00,
        'marka' => 'SUPRADYN',
        'is_active' => true
    ];

    $result = $api->upsertProduct('U003', $productData);
    echo "Ürün işlendi: " . $result['data']['urun_adi'] . "\n";

    // ========================================================================
    // ÖRNEK 5: Sadece Stok Güncelleme
    // ========================================================================
    
    echo "\n--- Stok Güncelleme ---\n";

    // Önce ürünü bul
    $product = $api->findProductByCode('U001');
    if ($product) {
        $result = $api->updateStock($product['id'], 250.00);
        echo "Stok güncellendi: " . $result['data']['urun_adi'] . " -> " . $result['data']['bakiye'] . "\n";
    }

    // ========================================================================
    // ÖRNEK 6: Sadece Fiyat Güncelleme
    // ========================================================================
    
    echo "\n--- Fiyat Güncelleme ---\n";

    $product = $api->findProductByCode('U001');
    if ($product) {
        $result = $api->updatePrice($product['id'], [
            'satis_fiyati' => 110.00,
            'kurum_iskonto' => 12.00
        ]);
        echo "Fiyat güncellendi: " . $result['data']['urun_adi'] . " -> " . $result['data']['satis_fiyati'] . " TL\n";
    }

    // ========================================================================
    // ÖRNEK 7: Periyodik Senkronizasyon (Cron Job için)
    // ========================================================================
    
    echo "\n--- Periyodik Senkronizasyon Örneği ---\n";

    // Bu kısım cron job olarak çalıştırılabilir
    function periodicSync($api) {
        // ERP'den son 24 saatte değişen kullanıcıları çek
        $changedUsers = getChangedUsersFromERP(); // Kendi ERP fonksiyonunuz
        
        if (!empty($changedUsers)) {
            $result = $api->syncUsers($changedUsers);
            echo date('Y-m-d H:i:s') . " - Kullanıcı sync: {$result['created']} yeni, {$result['updated']} güncelleme\n";
        }

        // ERP'den son 24 saatte değişen ürünleri çek
        $changedProducts = getChangedProductsFromERP(); // Kendi ERP fonksiyonunuz
        
        if (!empty($changedProducts)) {
            $result = $api->syncProducts($changedProducts);
            echo date('Y-m-d H:i:s') . " - Ürün sync: {$result['created']} yeni, {$result['updated']} güncelleme\n";
        }
    }

    // Örnek ERP fonksiyonları (kendi sisteminize göre uyarlayın)
    function getChangedUsersFromERP() {
        // ERP veritabanınızdan son 24 saatte değişen kullanıcıları çekin
        // Örnek:
        // SELECT * FROM customers WHERE updated_at > NOW() - INTERVAL 1 DAY
        return [];
    }

    function getChangedProductsFromERP() {
        // ERP veritabanınızdan son 24 saatte değişen ürünleri çekin
        // Örnek:
        // SELECT * FROM products WHERE updated_at > NOW() - INTERVAL 1 DAY
        return [];
    }

    echo "\n✓ Tüm işlemler başarıyla tamamlandı!\n";

} catch (Exception $e) {
    echo "✗ Hata: " . $e->getMessage() . "\n";
    exit(1);
}

// ============================================================================
// CRON JOB KURULUMU
// ============================================================================

/*
Linux/Unix sistemlerde cron job olarak çalıştırmak için:

1. Crontab düzenle:
   crontab -e

2. Aşağıdaki satırı ekleyin (her gece 02:00'de çalışır):
   0 2 * * * /usr/bin/php /path/to/erp_integration_example.php >> /var/log/atakodb2b_sync.log 2>&1

3. Her 6 saatte bir çalışması için:
   0 */6 * * * /usr/bin/php /path/to/erp_integration_example.php >> /var/log/atakodb2b_sync.log 2>&1

Windows Task Scheduler için:
1. Task Scheduler'ı açın
2. "Create Basic Task" seçin
3. Trigger: Daily veya Custom
4. Action: Start a program
5. Program: C:\xampp\php\php.exe
6. Arguments: C:\path\to\erp_integration_example.php
*/







