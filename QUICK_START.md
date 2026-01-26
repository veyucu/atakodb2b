# atakodb2b - Netsis ERP Entegrasyonu Hızlı Başlangıç

## 🎯 Genel Bakış

Bu proje iki ana bileşenden oluşur:

### 1. **atakodb2b (Laravel API)** - Ana Uygulama
- Web-based B2B e-ticaret platformu
- RESTful API servisleri
- Kullanıcı ve ürün yönetimi

### 2. **AtakoDB2B.WindowsService (.NET C#)** - Netsis Entegrasyonu
- Windows Server'da çalışan background service
- Netsis ERP'den veri çeker
- atakodb2b API'sine senkronize eder

---

## 📦 1. Laravel API Kurulumu

### Gereksinimler
- PHP >= 8.1
- MySQL/MariaDB
- Composer
- Laravel >= 10.x

### Kurulum Adımları

```bash
# 1. Bağımlılıkları yükle
composer install

# 2. .env dosyasını yapılandır
cp .env.example .env
# Veritabanı bilgilerini düzenle

# 3. Uygulama key'i oluştur
php artisan key:generate

# 4. Migration'ları çalıştır
php artisan migrate

# 5. Sanctum migration (API için)
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

# 6. Admin kullanıcısı oluştur
php artisan tinker
```

```php
// Tinker içinde
$user = User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
    'user_type' => 'admin',
    'is_active' => true
]);

// API Token oluştur (Netsis service için)
$token = $user->createToken('Netsis Service')->plainTextToken;
echo $token; // Bu token'ı kaydedin!
```

### API Test

```bash
# Login test
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password123"}'
```

**Detaylı API Dokümantasyonu:** `API_DOCUMENTATION.md`

---

## 🖥️ 2. Windows Service Kurulumu

### Gereksinimler
- Windows Server 2012 R2+ (veya Windows 10+)
- .NET 6.0 Runtime (self-contained, otomatik)
- SQL Server (Netsis veritabanına erişim)
- Visual Studio 2022 (veya sadece PowerShell)

### Kurulum Adımları

#### A. Projeyi Açın

```bash
cd AtakoDB2B.WindowsService
```

Visual Studio ile `AtakoDB2B.WindowsService.sln` açın veya VS Code kullanın.

#### B. Projeyi Derleyin

**PowerShell ile (Önerilen):**
```powershell
cd AtakoDB2B.WindowsService
.\publish.ps1
```

**Manuel:**
```bash
dotnet restore
dotnet build -c Release
dotnet publish -c Release -r win-x64 --self-contained true
```

#### C. Yapılandırma

`bin\Release\net6.0\win-x64\publish\appsettings.json` dosyasını düzenleyin:

```json
{
  "Netsis": {
    "ConnectionString": "Server=YOUR_SQL_SERVER;Database=NETSIS;User Id=YOUR_USER;Password=YOUR_PASSWORD;TrustServerCertificate=True;"
  },
  "Api": {
    "BaseUrl": "https://your-domain.com/api",
    "Email": "admin@example.com",
    "Password": "password123"
  },
  "Schedules": {
    "UserSync": "0 0 2 * * ?",
    "ProductSync": "0 0 3 * * ?",
    "StockSync": "0 */30 * * * ?"
  }
}
```

#### D. Windows Service'i Kurun

```powershell
# PowerShell'i YÖNETİCİ OLARAK çalıştırın!
cd AtakoDB2B.WindowsService
.\install-service.ps1
```

#### E. Servisi Kontrol Edin

```powershell
# Servis durumu
Get-Service -Name atakodb2bSyncService

# Loglar
Get-Content .\logs\atakodb2b-service-*.txt -Tail 50 -Wait
```

---

## 🔄 3. Senkronizasyon Akışı

```
┌─────────────────┐         ┌──────────────────────┐         ┌─────────────────┐
│  Netsis ERP     │         │  Windows Service     │         │  atakodb2b API  │
│  (SQL Server)   │────────>│  (.NET C# Service)   │────────>│  (Laravel)      │
└─────────────────┘         └──────────────────────┘         └─────────────────┘
                                      │
                                      │ Zamanlı Job'lar:
                                      │ • UserSyncJob
                                      │ • ProductSyncJob
                                      │ • StockSyncJob
                                      │
                                      ▼
                            ┌──────────────────┐
                            │   Serilog        │
                            │   (Logs)         │
                            └──────────────────┘
```

### Senkronizasyon Job'ları

1. **UserSyncJob**: Netsis müşterilerini atakodb2b'ye aktarır
   - Varsayılan: Her gece 02:00
   - Kaynak: `CARI_HESAPLAR` tablosu
   - Hedef: `users` tablosu

2. **ProductSyncJob**: Netsis ürünlerini atakodb2b'ye aktarır
   - Varsayılan: Her gece 03:00
   - Kaynak: `STOKLAR` tablosu
   - Hedef: `products` tablosu

3. **StockSyncJob**: Stok seviyelerini günceller
   - Varsayılan: Her 30 dakikada
   - Kaynak: `STOK_HAREKETLERI` tablosu
   - Hedef: `products.bakiye` alanı

---

## 🧪 4. Test

### API Testi

```bash
# 1. Login
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password123"}'

# Token'ı kaydedin, sonraki isteklerde kullanın

# 2. Kullanıcı listesi
curl -X GET http://localhost/api/users \
  -H "Authorization: Bearer YOUR_TOKEN"

# 3. Ürün listesi
curl -X GET http://localhost/api/products \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Windows Service Testi

```powershell
# 1. Bağlantıları kontrol et
# Service loglarında şunları arayın:
Get-Content .\logs\*.txt | Select-String "Netsis veritabanı bağlantısı"
Get-Content .\logs\*.txt | Select-String "API Login"

# 2. Manuel senkronizasyon tetikle
# Service'i yeniden başlat (job'lar hemen çalışacak)
Restart-Service -Name atakodb2bSyncService

# 3. Sonuçları kontrol et
Get-Content .\logs\*.txt -Tail 100
```

---

## 📊 5. Monitoring (İzleme)

### Log Dosyaları

**Laravel (atakodb2b):**
```bash
tail -f storage/logs/laravel.log
```

**Windows Service:**
```powershell
Get-Content .\AtakoDB2B.WindowsService\logs\atakodb2b-service-*.txt -Tail 50 -Wait
```

### Windows Services

```powershell
# Services yönetim konsolu
services.msc

# Event Viewer
eventvwr.msc
```

### Senkronizasyon Durumu

```sql
-- atakodb2b veritabanında

-- Son eklenen kullanıcılar
SELECT * FROM users ORDER BY created_at DESC LIMIT 10;

-- Son eklenen ürünler
SELECT * FROM products ORDER BY created_at DESC LIMIT 10;

-- Toplam sayılar
SELECT 
    (SELECT COUNT(*) FROM users WHERE user_type = 'musteri') as total_customers,
    (SELECT COUNT(*) FROM products) as total_products;
```

---

## 🔧 6. Sorun Giderme

### Laravel API Çalışmıyor

```bash
# Logları kontrol et
tail -f storage/logs/laravel.log

# Cache temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Veritabanı bağlantısını test et
php artisan tinker
>>> DB::connection()->getPdo();
```

### Windows Service Çalışmıyor

```powershell
# Service durumunu kontrol et
Get-Service -Name atakodb2bSyncService | Format-List *

# Logları kontrol et
Get-Content .\logs\*.txt -Tail 100

# Service'i yeniden başlat
Restart-Service -Name atakodb2bSyncService

# Event Viewer'ı kontrol et
eventvwr.msc
# Windows Logs > Application > atakodb2bSyncService
```

### Senkronizasyon Çalışmıyor

1. **Netsis bağlantısı:**
```sql
-- SQL Server Management Studio ile test edin
SELECT TOP 10 * FROM CARI_HESAPLAR;
SELECT TOP 10 * FROM STOKLAR;
```

2. **API bağlantısı:**
```bash
curl -X GET https://your-domain.com/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

3. **Zamanlama:**
```json
// appsettings.json - Cron expression'ları kontrol edin
{
  "Schedules": {
    "UserSync": "0 0 2 * * ?"  // Doğru mu?
  }
}
```

---

## 📚 7. Dokümantasyon

- **API Dokümantasyonu**: `API_DOCUMENTATION.md`
- **API Hızlı Başlangıç**: `API_README.md`
- **Windows Service**: `AtakoDB2B.WindowsService/README.md`
- **Postman Collection**: `atakodb2b_API.postman_collection.json`
- **PHP Entegrasyon Örneği**: `examples/erp_integration_example.php`

---

## 🎓 8. Cron Expression Örnekleri

```
"0 0 2 * * ?"      # Her gün 02:00
"0 0 */6 * * ?"    # Her 6 saatte bir
"0 */30 * * * ?"   # Her 30 dakikada
"0 0 2 * * MON"    # Her Pazartesi 02:00
"0 0 9-17 * * ?"   # Her gün 09:00-17:00 arası her saat başı
"0 0 2 1 * ?"      # Her ayın 1'i 02:00
```

Cron expression test: https://crontab.guru/

---

## 🚀 9. Production Deployment

### Laravel API

```bash
# 1. Optimizasyon
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. .env production ayarları
APP_ENV=production
APP_DEBUG=false
```

### Windows Service

```powershell
# 1. Release build
.\publish.ps1

# 2. Production appsettings
# Gerçek connection string'ler
# HTTPS API URL'i

# 3. Service'i kur
.\install-service.ps1

# 4. Güvenlik
# - SQL kullanıcısına minimum yetki
# - Firewall kuralları
# - SSL/TLS sertifikaları
```

---

## 💡 10. İpuçları

### Performans

- Batch size'ı ayarlayın (varsayılan: 100)
- Zamanlamayı yoğun olmayan saatlere ayarlayın
- Sadece değişen kayıtları senkronize edin

### Güvenlik

- API token'larını güvenli saklayın
- SQL Server kullanıcısına minimum yetki verin
- HTTPS kullanın (HTTP yeterli değil!)
- appsettings.json'ı güvenli tutun

### Bakım

- Logları düzenli kontrol edin
- Disk alanını izleyin
- Senkronizasyon sürelerini analiz edin
- Hata oranlarını takip edin

---

## 📞 Destek

- Laravel API: `storage/logs/laravel.log`
- Windows Service: `AtakoDB2B.WindowsService/logs/`
- Event Viewer: `eventvwr.msc`

---

**Başarılar! 🎉**

atakodb2b + Netsis ERP Entegrasyonu artık hazır!


