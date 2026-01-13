# Windows Service Nasıl Kullanılır? 🚀

## ✅ Sizin İçin Hazır!

Tüm kodlar yazıldı ve **.NET 6.0**'a düşürüldü. Her Windows'ta çalışacak!

---

## 📝 Adım 1: Visual Studio ile Açın

### Yöntem 1: Visual Studio 2022 (Önerilen)
1. **Visual Studio 2022**'yi açın
2. **File** > **Open** > **Project/Solution**
3. `AtakoDB2B.WindowsService.sln` dosyasını seçin
4. Bekleyin, NuGet paketleri otomatik inecek

### Yöntem 2: Visual Studio Code
1. **VS Code**'u açın
2. `AtakoDB2B.WindowsService` klasörünü açın
3. Terminal'de: `dotnet restore`

### Yöntem 3: Sadece PowerShell (Visual Studio Yok)
Proje dizinine gidin ve:
```powershell
cd AtakoDB2B.WindowsService
dotnet restore
```

---

## 🔨 Adım 2: Derleme (Çok Kolay!)

### Visual Studio 2022'de:
1. **Build** menüsünden **Build Solution** (veya **Ctrl+Shift+B**)
2. Hata varsa aşağıda gösterilir, yoksa "Build başarılı" yazacak

### PowerShell'de (Daha Kolay):
```powershell
cd AtakoDB2B.WindowsService
.\publish.ps1
```

Bu script:
- ✅ Temizlik yapar
- ✅ NuGet paketlerini indirir
- ✅ Projeyi derler
- ✅ Tek EXE dosyası oluşturur (self-contained)
- ✅ `bin\Release\net6.0\win-x64\publish\` klasörüne koyar

---

## ⚙️ Adım 3: Ayarları Yapın

`bin\Release\net6.0\win-x64\publish\appsettings.json` dosyasını düzenleyin:

```json
{
  "Netsis": {
    "ConnectionString": "Server=SUNUCU_ADI;Database=NETSIS;User Id=KULLANICI;Password=SIFRE;TrustServerCertificate=True;"
  },
  "Api": {
    "BaseUrl": "https://siteniz.com/api",
    "Email": "admin@siteniz.com",
    "Password": "api_sifresi"
  },
  "Schedules": {
    "UserSync": "0 0 2 * * ?",     // Her gece 02:00
    "ProductSync": "0 0 3 * * ?",  // Her gece 03:00
    "StockSync": "0 */30 * * * ?"  // Her 30 dakikada
  }
}
```

### 🎯 Önemli Ayarlar:

**Netsis Bağlantısı:**
- `Server`: SQL Server adresi (örn: `192.168.1.10` veya `localhost`)
- `Database`: `NETSIS` (Netsis veritabanı adı)
- `User Id`: SQL Server kullanıcı adı
- `Password`: SQL Server şifre

**API Bağlantısı:**
- `BaseUrl`: atakodb2b API adresi (örn: `https://atakodb2b.com/api`)
- `Email`: Admin kullanıcı email
- `Password`: Admin kullanıcı şifre

---

## 🎬 Adım 4: Windows Service Olarak Kurun

**PowerShell'i YÖNETİCİ olarak açın:**

```powershell
cd C:\xampp\htdocs\atakodb2b\AtakoDB2B.WindowsService
.\install-service.ps1
```

Bu script:
- ✅ Eski servisi kaldırır (varsa)
- ✅ Yeni servisi yükler
- ✅ Otomatik başlangıç ayarlar
- ✅ Servisi başlatır

---

## 📊 Adım 5: Kontrol Edin

### Servis Durumu:
```powershell
Get-Service -Name atakodb2bSyncService
```

### Logları İzleyin:
```powershell
Get-Content .\logs\atakodb2b-service-*.txt -Tail 50 -Wait
```

### Windows Services'dan:
1. `Win + R` > `services.msc` > Enter
2. "atakodb2b Sync Service" bulun
3. Sağ tık > **Start/Stop/Restart**

---

## 🛠️ Sık Kullanılan Komutlar

```powershell
# Servisi başlat
Start-Service -Name atakodb2bSyncService

# Servisi durdur
Stop-Service -Name atakodb2bSyncService

# Servisi yeniden başlat
Restart-Service -Name atakodb2bSyncService

# Servis durumu
Get-Service -Name atakodb2bSyncService | Format-List *

# Logları oku (son 100 satır)
Get-Content .\logs\*.txt -Tail 100

# Canlı log izle
Get-Content .\logs\*.txt -Tail 50 -Wait
```

---

## 🔄 Kodu Değiştirdikten Sonra

1. **Servisi durdurun:**
   ```powershell
   Stop-Service -Name atakodb2bSyncService
   ```

2. **Yeniden derleyin:**
   ```powershell
   cd AtakoDB2B.WindowsService
   .\publish.ps1
   ```

3. **Servisi başlatın:**
   ```powershell
   Start-Service -Name atakodb2bSyncService
   ```

---

## ❌ Servisi Kaldırma

```powershell
# PowerShell'i YÖNETİCİ olarak açın
cd AtakoDB2B.WindowsService
.\uninstall-service.ps1
```

---

## 📁 Proje Yapısı

```
AtakoDB2B.WindowsService/
├── Program.cs                 # Ana program
├── appsettings.json          # Ayarlar
├── Jobs/                     # Zamanlanmış görevler
│   ├── UserSyncJob.cs       # Kullanıcı senkronizasyonu
│   ├── ProductSyncJob.cs    # Ürün senkronizasyonu
│   └── StockSyncJob.cs      # Stok senkronizasyonu
├── Services/                 # Servisler
│   ├── AtakoDB2BApiService.cs     # API iletişimi
│   ├── NetsisDbService.cs         # Netsis veritabanı
│   └── SyncService.cs             # Senkronizasyon mantığı
├── Models/                   # Veri modelleri
│   ├── NetsisConfig.cs
│   └── NetsisModels.cs
├── publish.ps1              # Derleme scripti
├── install-service.ps1      # Kurulum scripti
└── uninstall-service.ps1    # Kaldırma scripti
```

---

## 🐛 Sorun mu Var?

### 1. Build Hatası?
```powershell
# Temizlik yapın
dotnet clean
dotnet restore
dotnet build
```

### 2. Servis Başlamıyor?
```powershell
# Logları kontrol edin
Get-Content .\logs\*.txt -Tail 100

# Event Viewer'a bakın
eventvwr.msc
# Windows Logs > Application > atakodb2bSyncService
```

### 3. Netsis Bağlanamıyor?
- SQL Server Management Studio ile test edin
- Connection string doğru mu?
- SQL kullanıcısının yetkisi var mı?

### 4. API Bağlanamıyor?
- BaseUrl doğru mu? (`/api` ile bitmeli)
- Email ve password doğru mu?
- HTTPS sertifikası geçerli mi?

---

## 💡 İpuçları

1. **İlk test:** Servisi kurduktan sonra logları izleyin
2. **Zamanlama:** İlk test için zamanlamayı kısa tutun (örn: her 5 dakika)
3. **Batch size:** Çok fazla veri varsa batch size'ı küçültün (50-100)
4. **Yedekleme:** appsettings.json'ı yedekleyin!

---

## 🎓 Netsis Tabloları

Servisin kullandığı Netsis tabloları:

| Tablo | Açıklama | Hedef |
|-------|----------|-------|
| `CARI_HESAPLAR` | Müşteri kartları | `users` tablosu |
| `STOKLAR` | Ürün kartları | `products` tablosu |
| `STOK_HAREKETLERI` | Stok hareketleri | `products.bakiye` |

---

## 📞 Yardım

- **Loglar:** `AtakoDB2B.WindowsService\logs\`
- **Event Viewer:** `eventvwr.msc`
- **Services:** `services.msc`

---

**Başarılar! 🎉**

Herhangi bir sorun olursa logları kontrol edin.
Kod üzerinde değişiklik yapmak isterseniz, ben kodları yazıyorum, siz sadece derliyorsunuz! 😊






