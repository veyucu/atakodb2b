# AtakoErpService - Müşteri Kurulum Rehberi

## Gereksinimler

1. **Windows Server 2016+** veya Windows 10/11
2. **.NET 6.0 Runtime** - [İndir](https://dotnet.microsoft.com/download/dotnet/6.0)
3. **SQL Server erişimi** (Netsis veritabanı)
4. **Açık portlar**: 5000 (HTTP) veya 5001 (HTTPS)

---

## Kurulum Adımları

### 1. Dosyaları Kopyalayın

`publish-customer` klasörünü müşteri sunucusuna kopyalayın:
```
C:\AtakoErpService\
```

### 2. appsettings.json Düzenleyin

Dosyayı açın ve aşağıdaki değerleri güncelleyin:

```json
{
  "ConnectionStrings": {
    "ErpDatabase": "Server=MUSTERI_SQL_SERVER;Database=NETSIS_DB;User Id=sa;Password=SIFRE;TrustServerCertificate=true;"
  },
  "LaravelApi": {
    "BaseUrl": "https://doganarib2b.com",
    "ApiKey": "3f0a161b-72c5-4c53-9f9f-263e55bad2be"
  },
  "Netsis": {
    "RestApiUrl": "http://localhost:7070",
    "DbType": "vtMSSQL",
    "DbName": "MUSTERI_NETSIS_DB",
    "DbUser": "TEMELSET",
    "DbPassword": "",
    "NetsisUser": "NETSIS",
    "NetsisPassword": "SIFRE",
    "BranchCode": 0
  },
  "Security": {
    "ApiKey": "3f0a161b-72c5-4c53-9f9f-263e55bad2be"
  }
}
```

### 3. Test Çalıştırma

Komut satırında:
```cmd
cd C:\AtakoErpService
AtakoErpService.exe
```

Tarayıcıda açın: `http://localhost:5000/api/health`

### 4. Windows Servisi Olarak Kurulum

Yönetici olarak PowerShell açın:

```powershell
# Servis oluştur
New-Service -Name "AtakoErpService" -BinaryPathName "C:\AtakoErpService\AtakoErpService.exe" -DisplayName "Atako ERP Sync Service" -StartupType Automatic -Description "Netsis ERP ve B2B senkronizasyon servisi"

# Servisi başlat
Start-Service -Name "AtakoErpService"

# Durumu kontrol et
Get-Service -Name "AtakoErpService"
```

### 5. Firewall Ayarı

```powershell
New-NetFirewallRule -DisplayName "AtakoErpService" -Direction Inbound -LocalPort 5000 -Protocol TCP -Action Allow
```

---

## API Endpoints

| Endpoint | Açıklama |
|----------|----------|
| `GET /api/health` | Servis durumu |
| `POST /api/UserSync/sync` | Kullanıcı senkronizasyonu |
| `POST /api/ProductSync/sync` | Ürün senkronizasyonu |
| `POST /api/OrderSync/send` | Sipariş gönderimi |

---

## Logs

Log dosyaları: `C:\AtakoErpService\Logs\`

---

## Sorun Giderme

1. **Servis başlamıyor**: Event Viewer kontrol edin
2. **Veritabanı bağlantısı yok**: Connection string kontrol edin
3. **API erişim hatası**: Firewall kurallarını kontrol edin
