# 🚀 Hızlı Başlangıç - Windows Service

## ✨ 3 Adımda Çalıştırın!

### 1️⃣ Derleyin
```powershell
cd AtakoDB2B.WindowsService
.\publish.ps1
```

### 2️⃣ Ayarları Yapın
`bin\Release\net6.0\win-x64\publish\appsettings.json` düzenleyin:
- Netsis SQL Server bağlantı bilgileri
- atakodb2b API adresi ve şifre

### 3️⃣ Kurun (Yönetici olarak)
```powershell
.\install-service.ps1
```

---

## 📊 Kontrol

```powershell
# Servis durumu
Get-Service -Name atakodb2bSyncService

# Logları izle
Get-Content .\logs\*.txt -Tail 50 -Wait
```

---

## 📖 Detaylı Bilgi

- **Kullanım Kılavuzu:** `NASIL_KULLANILIR.md`
- **Teknik Detaylar:** `README.md`
- **Tüm Proje:** `../QUICK_START.md`

---

## ⚙️ Ne Yapar?

1. **Kullanıcı Sync:** Netsis müşterileri → atakodb2b (Her gece 02:00)
2. **Ürün Sync:** Netsis ürünleri → atakodb2b (Her gece 03:00)
3. **Stok Sync:** Stok seviyeleri → atakodb2b (Her 30 dakika)

---

## 🔧 Sık Kullanılan

```powershell
# Başlat
Start-Service -Name atakodb2bSyncService

# Durdur
Stop-Service -Name atakodb2bSyncService

# Yeniden Başlat
Restart-Service -Name atakodb2bSyncService

# Kaldır (Yönetici)
.\uninstall-service.ps1
```

---

**Framework:** .NET 6.0 (Her Windows'ta çalışır!)  
**Mod:** Self-Contained (Runtime içinde, kurulum gerektirmez)

✅ Tüm kodlar hazır, siz sadece derleyip kullanın! 🎉






