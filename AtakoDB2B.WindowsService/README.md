# atakodb2b Windows Service

Netsis ERP sisteminden atakodb2b API'sine otomatik veri senkronizasyonu yapan Windows servisi.

## 🎯 Özellikler

- ✅ **Netsis ERP Entegrasyonu**: SQL Server üzerinden Netsis verilerine erişim
- ✅ **Otomatik Senkronizasyon**: Zamanlanmış görevlerle otomatik veri aktarımı
- ✅ **Müşteri Senkronizasyonu**: Netsis müşteri kartlarını atakodb2b'ye aktarır
- ✅ **Ürün Senkronizasyonu**: Netsis stok kartlarını atakodb2b'ye aktarır
- ✅ **Stok Senkronizasyonu**: Anlık stok seviyelerini günceller
- ✅ **Windows Service**: Arka planda sürekli çalışır
- ✅ **Güçlü Loglama**: Detaylı log kayıtları (Serilog)
- ✅ **Hata Toleransı**: Retry mekanizması ve circuit breaker
- ✅ **Yapılandırılabilir**: appsettings.json ile kolay yapılandırma

## 📋 Gereksinimler

- Windows Server 2012 R2 veya üzeri / Windows 10 veya üzeri
- .NET 6.0 Runtime (otomatik yüklenir, self-contained)
- SQL Server (Netsis veritabanına erişim)
- atakodb2b API erişimi

## 🚀 Kurulum

### 1. Projeyi Derleyin

```powershell
# Proje dizininde
.\publish.ps1
```

Bu script:
- Projeyi temizler
- NuGet paketlerini yükler
- Release modunda derler
- Self-contained Windows x64 binary oluşturur

### 2. Yapılandırma

`bin\Release\net6.0\win-x64\publish\appsettings.json` dosyasını düzenleyin:

```json
{
  "Netsis": {
    "ConnectionString": "Server=YOUR_SQL_SERVER;Database=NETSIS;User Id=sa;Password=YOUR_PASSWORD;TrustServerCertificate=True;"
  },
  "Api": {
    "BaseUrl": "https://your-atakodb2b-domain.com/api",
    "Email": "your-admin@email.com",
    "Password": "your-api-password"
  },
  "Schedules": {
    "UserSync": "0 0 2 * * ?",
    "ProductSync": "0 0 3 * * ?",
    "StockSync": "0 */30 * * * ?"
  }
}
```

### 3. Windows Service'i Kurun

```powershell
# PowerShell'i Yönetici olarak çalıştırın
.\install-service.ps1
```

Bu script:
- Mevcut servisi durdurur (varsa)
- Yeni servisi yükler
- Otomatik başlangıç olarak ayarlar
- Servisi başlatır

### 4. Servisi Kontrol Edin

```powershell
# Servis durumunu kontrol et
Get-Service -Name atakodb2bSyncService

# Logları görüntüle
Get-Content .\logs\atakodb2b-service-*.txt -Tail 50

# Veya Windows Services yönetim aracını kullanın
services.msc
```

## ⚙️ Yapılandırma Detayları

### Netsis Bağlantısı

```json
{
  "Netsis": {
    "ConnectionString": "SQL Server connection string",
    "Timeout": 30,
    "EnableRetry": true,
    "MaxRetryCount": 3
  }
}
```

### API Bağlantısı

```json
{
  "Api": {
    "BaseUrl": "https://yourdomain.com/api",
    "Email": "admin@example.com",
    "Password": "password123",
    "DeviceName": "Netsis Windows Service",
    "Timeout": 60,
    "MaxRetryCount": 3
  }
}
```

### Zamanlama (Cron Expressions)

```json
{
  "Schedules": {
    "UserSync": "0 0 2 * * ?",     // Her gece 02:00
    "ProductSync": "0 0 3 * * ?",  // Her gece 03:00
    "StockSync": "0 */30 * * * ?"  // Her 30 dakikada
  }
}
```

**Cron Format:** `Saniye Dakika Saat GünAy Ay GünHafta`

Örnekler:
- `0 0 2 * * ?` - Her gün 02:00
- `0 0 */6 * * ?` - Her 6 saatte bir
- `0 */30 * * * ?` - Her 30 dakikada
- `0 0 2 * * MON` - Her Pazartesi 02:00

### Senkronizasyon Ayarları

```json
{
  "SyncSettings": {
    "BatchSize": 100,
    "EnableUserSync": true,
    "EnableProductSync": true,
    "EnableStockSync": true,
    "SyncOnlyActive": true,
    "SyncDeletedRecords": false
  }
}
```

## 📊 Senkronizasyon İşlemleri

### 1. Kullanıcı Senkronizasyonu

**Kaynak:** Netsis `CARI_HESAPLAR` tablosu  
**Hedef:** atakodb2b `users` tablosu  
**Varsayılan Zamanlama:** Her gece 02:00

**Çekilen Bilgiler:**
- Müşteri kodu
- Müşteri adı ve ünvanı
- Adres bilgileri
- İletişim bilgileri (telefon, e-posta)
- Vergi dairesi ve numarası
- GLN numarası
- Grup ve plasiyer kodları

### 2. Ürün Senkronizasyonu

**Kaynak:** Netsis `STOKLAR` tablosu  
**Hedef:** atakodb2b `products` tablosu  
**Varsayılan Zamanlama:** Her gece 03:00

**Çekilen Bilgiler:**
- Ürün kodu ve adı
- Barkod
- Muadil kodu
- Fiyat bilgileri (liste, KDV, iskontolar)
- Stok miktarı
- Marka ve grup bilgileri

### 3. Stok Senkronizasyonu

**Kaynak:** Netsis `STOK_HAREKETLERI` tablosu  
**Hedef:** atakodb2b `products.bakiye`  
**Varsayılan Zamanlama:** Her 30 dakikada

Sadece stok seviyelerini günceller, hızlı işlem.

## 🔍 Netsis Tablo Yapısı

Service, Netsis'in standart tablo yapısını kullanır:

### CARI_HESAPLAR (Müşteri Kartları)
- `cari_kod`: Müşteri kodu (PRIMARY KEY)
- `cari_isim`: Müşteri adı
- `cari_unvan1`: Ünvan
- `cari_hareket_tipi`: 0=Müşteri, 1=Tedarikçi
- `cari_pasif_mi`: 0=Aktif, 1=Pasif

### STOKLAR (Ürün Kartları)
- `sto_kod`: Stok kodu (PRIMARY KEY)
- `sto_isim`: Ürün adı
- `barkod`: Barkod numarası
- `sto_kdv_dahil_perakende`: KDV dahil satış fiyatı
- `sto_miktar`: Stok miktarı
- `sto_pasif_mi`: 0=Aktif, 1=Pasif

### STOK_HAREKETLERI (Stok Hareketleri)
- `sto_kod`: Stok kodu
- `har_miktar`: Hareket miktarı
- `har_depo_kodu`: Depo kodu
- `har_tarihi`: Hareket tarihi

## 📝 Loglama

Loglar `logs/` dizininde günlük dosyalar halinde saklanır:

```
logs/
  atakodb2b-service-20241204.txt
  atakodb2b-service-20241205.txt
  ...
```

**Log Seviyeleri:**
- `Information`: Normal işlemler
- `Warning`: Uyarılar
- `Error`: Hatalar
- `Fatal`: Kritik hatalar

**Log Görüntüleme:**
```powershell
# Son 50 satır
Get-Content .\logs\atakodb2b-service-*.txt -Tail 50 -Wait

# Belirli bir gün
Get-Content .\logs\atakodb2b-service-20241204.txt

# Hataları filtrele
Get-Content .\logs\atakodb2b-service-*.txt | Select-String "Error"
```

## 🛠️ Servis Yönetimi

### PowerShell Komutları

```powershell
# Servisi başlat
Start-Service -Name atakodb2bSyncService

# Servisi durdur
Stop-Service -Name atakodb2bSyncService

# Servisi yeniden başlat
Restart-Service -Name atakodb2bSyncService

# Servis durumunu kontrol et
Get-Service -Name atakodb2bSyncService

# Servis detaylarını görüntüle
Get-Service -Name atakodb2bSyncService | Format-List *
```

### Services.msc (Windows Services)

1. `Win + R` tuşlarına basın
2. `services.msc` yazın ve Enter
3. "atakodb2b Sync Service" servisini bulun
4. Sağ tık > Properties ile yapılandırın

### Event Viewer

Windows Event Viewer'da servis loglarını görüntüleyebilirsiniz:
1. `eventvwr.msc` açın
2. Windows Logs > Application
3. Source: "atakodb2bSyncService" filtreleyin

## 🔧 Sorun Giderme

### Servis Başlamıyor

1. **Log dosyalarını kontrol edin:**
```powershell
Get-Content .\logs\atakodb2b-service-*.txt -Tail 100
```

2. **appsettings.json doğru mu?**
   - SQL Server connection string
   - API URL ve kimlik bilgileri

3. **Yetki problemleri:**
   - Servis LOCAL SYSTEM hesabıyla çalışır
   - SQL Server erişimi var mı?
   - API'ye erişim var mı?

### Senkronizasyon Çalışmıyor

1. **Manuel test:**
```csharp
// Test kodu Program.cs'e ekleyebilirsiniz
```

2. **Cron expression doğru mu?**
   - Zamanlamayı kontrol edin
   - [Cron expression tester](https://crontab.guru/) kullanın

3. **Veritabanı bağlantısı:**
```powershell
# SQL Server Management Studio ile test edin
```

### Performans Sorunları

1. **Batch size'ı ayarlayın:**
```json
{
  "SyncSettings": {
    "BatchSize": 50  // Daha küçük batch'ler
  }
}
```

2. **Zamanlamayı optimize edin:**
   - Yoğun saatlerde çalıştırmayın
   - Job'ları farklı zamanlara yayın

3. **Logları kontrol edin:**
   - Hangi işlem yavaş?
   - Timeout var mı?

## 🔄 Güncelleme

1. Servisi durdurun:
```powershell
Stop-Service -Name atakodb2bSyncService
```

2. Yeni sürümü publish edin:
```powershell
.\publish.ps1
```

3. Dosyaları kopyalayın (appsettings.json'ı koruyun)

4. Servisi başlatın:
```powershell
Start-Service -Name atakodb2bSyncService
```

## ❌ Kaldırma

```powershell
# PowerShell'i Yönetici olarak çalıştırın
.\uninstall-service.ps1
```

Bu script:
- Servisi durdurur
- Servisi sistemden kaldırır
- Dosyalar ve loglar kalır (manuel silebilirsiniz)

## 📞 Destek

- Logları kontrol edin: `logs/`
- Event Viewer'ı kontrol edin
- API dokümantasyonuna bakın: `API_DOCUMENTATION.md`

## 📄 Lisans

Bu proje özel lisans altındadır.

## 🔐 Güvenlik Notları

- `appsettings.json` hassas bilgiler içerir, güvenli tutun
- SQL Server kullanıcısına minimum yetki verin (READ-ONLY)
- API token'ları güvenli saklayın
- Production ortamında Windows Firewall kuralları ekleyin
- Servis hesabını sınırlandırın

---

**atakodb2b Windows Service** - Netsis ERP Entegrasyonu  
Version: 1.0.0  
Date: 2024-12-04


