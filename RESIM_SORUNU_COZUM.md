# 🖼️ Resim Sorunu Çözüm Rehberi

## SSH Yoksa Manuel Çözüm:

### Adım 1: Storage Klasörünü Kopyala

Hosting dosya yöneticisinden:

```
storage/app/public/
```

içindeki TÜM klasörleri ve dosyaları:

```
public/storage/
```

içine kopyalayın.

**Klasör yapısı şöyle olmalı:**
```
public/storage/
├── products/
│   ├── resim1.jpg
│   └── resim2.jpg
├── sliders/
│   ├── slider1.jpg
│   └── slider2.jpg
└── .gitignore
```

### Adım 2: .env Dosyasını Kontrol

```env
APP_URL=https://yourdomain.com  ← DOĞRU DOMAIN!
```

**Önemli:** 
- Sonunda `/` yok
- HTTP veya HTTPS doğru
- Alt domain varsa onu yaz

### Adım 3: Cache Temizle

Hosting terminal veya SSH varsa:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

SSH yoksa şu dosyaları SİL:
```
bootstrap/cache/config.php
bootstrap/cache/routes-v7.php
bootstrap/cache/services.php
```

### Adım 4: İzinleri Kontrol

Hosting dosya yöneticisinden:

```
storage/             → 775 (rwxrwxr-x)
storage/app/         → 775
storage/app/public/  → 775
public/storage/      → 755 (rwxr-xr-x)
```

---

## 🔍 Sorun Tespiti

### Tarayıcıda F12 Basın

1. **Network** sekmesine gidin
2. **Images** filtresi seçin
3. Sayfayı yenileyin (F5)
4. Kırmızı (hata) olan resimlere tıklayın

**Hata mesajlarına göre:**

#### 404 Not Found
```
https://yourdomain.com/storage/products/resim.jpg → 404
```
**Çözüm:** Storage link eksik → Adım 1'i yapın

#### 403 Forbidden
```
https://yourdomain.com/storage/products/resim.jpg → 403
```
**Çözüm:** İzin sorunu → Adım 4'ü yapın

#### Mixed Content (HTTP/HTTPS karışık)
```
Mixed Content: The page at 'https://...' was loaded over HTTPS, 
but requested an insecure image 'http://...'
```
**Çözüm:** .env'de APP_URL'i HTTPS yapın

#### Yanlış Domain
```
https://localhost/storage/products/resim.jpg
```
**Çözüm:** .env'de APP_URL'i düzeltin

---

## ✅ Test Listesi

- [ ] `storage/app/public/` klasörü var mı?
- [ ] `public/storage/` klasörü var mı?
- [ ] `public/storage/` içinde resimler var mı?
- [ ] `.env` dosyasında APP_URL doğru mu?
- [ ] APP_URL sonunda `/` yok mu?
- [ ] HTTPS kullanıyorsanız APP_URL'de HTTPS var mı?
- [ ] Tarayıcıda F12 > Network > resim URL'leri doğru mu?
- [ ] Storage klasörü izinleri 775 mi?
- [ ] Cache temizlendi mi?

---

## 🎯 Hızlı Komutlar (SSH varsa)

```bash
# Storage link oluştur
php artisan storage:link

# Cache temizle
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# İzinler
chmod -R 775 storage
chmod -R 755 public/storage

# Test
ls -la public/storage
ls -la storage/app/public
```

---

## 🆘 Hala Çalışmıyorsa

### 1. Resim Yollarını Kodda Kontrol Edin

Blade dosyalarında:
```php
<!-- YANLIŞ -->
<img src="/storage/products/{{ $product->urun_resmi }}">

<!-- DOĞRU -->
<img src="{{ asset('storage/products/' . $product->urun_resmi) }}">
```

### 2. Veritabanında Resim Yollarını Kontrol

phpMyAdmin'de `products` tablosuna bakın:

```sql
SELECT urun_kodu, urun_resmi FROM products LIMIT 5;
```

**Doğru formatlar:**
```
products/resim.jpg          ✅
storage/products/resim.jpg  ❌ (storage/ eklenmemeli)
/products/resim.jpg         ❌ (başta / olmamalı)
```

### 3. URL Helper Test Edin

Laravel tinker ile test:

```bash
php artisan tinker
```

```php
echo asset('storage/products/test.jpg');
// Çıktı: https://yourdomain.com/storage/products/test.jpg olmalı
```

---

## 🔧 Özel Durumlar

### Subdomain Kullanıyorsanız

```env
APP_URL=https://panel.yourdomain.com
```

### CDN Kullanıyorsanız

```env
ASSET_URL=https://cdn.yourdomain.com
```

### Root Kurulum (public klasörü root'taysa)

`.env` dosyası:
```env
APP_URL=https://yourdomain.com
ASSET_URL=https://yourdomain.com
```

**Ve storage linkini yeniden yapın!**

---

## 📱 Son Kontrol

Tarayıcıda direkt açın:
```
https://yourdomain.com/storage/products/test.jpg
```

- ✅ **Resim açılıyor** → Sorun Laravel'de değil, view dosyalarında
- ❌ **404** → Storage link sorunu
- ❌ **403** → İzin sorunu
- ❌ **Bağlantı hatası** → Domain/URL sorunu

---

## 💡 Pro İpuçları

1. **Her zaman asset() kullanın:**
   ```php
   {{ asset('storage/products/' . $filename) }}
   ```

2. **Cache'i düzenli temizleyin:**
   ```bash
   php artisan optimize:clear
   ```

3. **Güvenlik için:**
   - `storage/app/public/` → Asıl dosyalar
   - `public/storage/` → Symlink veya kopya (sadece okunabilir)

4. **Yedek alın:**
   - Resim yüklemeden önce
   - Büyük değişikliklerden önce

---

Başarılar! 🚀
















