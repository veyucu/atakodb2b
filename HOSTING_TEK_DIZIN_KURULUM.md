# 🚀 Tek Dizin (Root) Kurulum Rehberi

## ⚠️ DİKKAT: GÜVENLİK RİSKİ!

Public klasörünü root'a taşımak **güvenlik riski** oluşturur çünkü `.env`, `config/`, `database/` gibi dosyalar web'den erişilebilir hale gelir. 

**Ancak**, bazı hosting sağlayıcılar tek dizin sunar. Bu durumda aşağıdaki adımları takip edin.

---

## 📋 Adım Adım Kurulum

### 1️⃣ Dosyaları Düzenleyin

#### A) index.php Değişiklikleri

**ÖNCESİ** (public/index.php):
```php
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

**SONRASI** (root/index.php):
```php
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

**Özet:** Tüm `/../` kısımlarını `/` yapın!

#### B) .htaccess Güncellemesi

Root'a şu `.htaccess`'i ekleyin (güvenlik önlemleri ile):

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# GÜVENLİK: .env dosyasına erişimi engelle
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>

# GÜVENLİK: Önemli dosyaları koru
<FilesMatch "^(composer\.(json|lock)|package\.json|\.gitignore|artisan)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# GÜVENLİK: Laravel klasörlerine direkt erişimi engelle
RedirectMatch 403 ^/(storage|bootstrap|database|app|config|routes|resources|tests|vendor)/.*$
```

### 2️⃣ Dosya Taşıma İşlemleri

```bash
# 1. public/ içindekileri root'a taşı
public/index.php         → root/index.php (düzenlenmiş hali)
public/.htaccess         → root/.htaccess (güvenlik eklenmiş hali)
public/css/              → root/css/
public/js/               → root/js/
public/images/           → root/images/
public/favicon.ico       → root/favicon.ico
public/robots.txt        → root/robots.txt

# 2. public/ klasörünü SİL veya yeniden adlandır
# (Artık gerekli değil)
```

### 3️⃣ Son Dosya Yapısı

```
public_html/  (veya root/)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env                ← GÜVENLİKLE KORUNMALI!
├── .htaccess           ← Güvenlik kuralları ile
├── index.php           ← Düzenlenmiş
├── artisan
├── composer.json
├── css/                ← public'ten taşındı
├── js/                 ← public'ten taşındı
├── images/             ← public'ten taşındı
└── storage (symlink)   ← php artisan storage:link
```

---

## 🔒 Güvenlik Önlemleri (MUTLAKA YAPIN!)

### 1. .env Dosyasını Koruma

`.htaccess` dosyasına ekleyin:
```apache
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>
```

**Test edin:** `https://yourdomain.com/.env` → 403 Forbidden olmalı!

### 2. Önemli Klasörleri Koruma

```apache
# storage, app, config gibi klasörlere erişimi engelle
RedirectMatch 403 ^/(storage|bootstrap|database|app|config|routes|resources|tests|vendor)/.*$
```

**Test edin:** 
- `https://yourdomain.com/app/` → 403 olmalı
- `https://yourdomain.com/config/database.php` → 403 olmalı
- `https://yourdomain.com/storage/logs/` → 403 olmalı

### 3. Hassas Dosyaları Koruma

```apache
<FilesMatch "^(composer\.(json|lock)|package\.json|\.gitignore|artisan)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

**Test edin:** `https://yourdomain.com/composer.json` → 403 olmalı!

### 4. robots.txt Güncellemesi

`robots.txt` oluşturun/düzenleyin:

```
User-agent: *
Disallow: /storage/
Disallow: /vendor/
Disallow: /bootstrap/
Disallow: /config/
Disallow: /database/
Disallow: /routes/
Disallow: /resources/
Disallow: /tests/
Disallow: /.env
```

---

## ✅ Kurulum Sonrası Kontroller

### 1. Güvenlik Testleri

Tarayıcıda deneyin:
- ❌ `https://yourdomain.com/.env` → 403 Forbidden
- ❌ `https://yourdomain.com/composer.json` → 403 Forbidden
- ❌ `https://yourdomain.com/app/` → 403 Forbidden
- ❌ `https://yourdomain.com/config/database.php` → 403 Forbidden
- ❌ `https://yourdomain.com/storage/logs/` → 403 Forbidden
- ✅ `https://yourdomain.com/` → Ana sayfa açılmalı
- ✅ `https://yourdomain.com/css/app.css` → CSS yüklenmeli
- ✅ `https://yourdomain.com/js/app.js` → JS yüklenmeli

### 2. Fonksiyon Testleri

- [ ] Ana sayfa yükleniyor
- [ ] Login çalışıyor
- [ ] Resimler görünüyor
- [ ] CSS/JS yükleniyor
- [ ] Admin paneli açılıyor
- [ ] Veritabanı bağlantısı çalışıyor

### 3. Log Kontrolü

Hata olursa kontrol edin:
```
storage/logs/laravel.log
```

---

## 🆘 Sorun Giderme

### 500 Internal Server Error
```bash
# İzinleri kontrol edin
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Cache temizleyin
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### CSS/JS Yüklenmiyor
- `APP_URL` .env'de doğru mu kontrol edin
- Tarayıcı console'da hata var mı bakın
- `css/`, `js/` klasörleri root'ta mı?

### .env Dosyası Okunmuyor
```bash
# .env dosyası root'ta olmalı
# İzinleri kontrol edin
chmod 644 .env

# Cache'i temizleyin
php artisan config:clear
```

### Storage Link Hatası
```bash
# Symlink oluşturun
php artisan storage:link

# Veya manuel:
# storage/app/public → ../../../public_html/storage
```

---

## 📦 Hızlı Başlatma Komutları

```bash
# 1. Cache oluştur
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Storage link
php artisan storage:link

# 3. İzinler
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod 644 .env

# 4. Test
php artisan route:list
```

---

## 🎯 Özet Checklist

- [ ] `index.php` path'leri düzenledim (/../ → /)
- [ ] `.htaccess` güvenlik kuralları ile güncelledim
- [ ] `public/` içindekileri root'a taşıdım
- [ ] `.env` erişime kapalı (403 test ettim)
- [ ] `composer.json` erişime kapalı (403 test ettim)
- [ ] `storage/`, `app/`, `config/` erişime kapalı (403 test ettim)
- [ ] `storage/` ve `bootstrap/cache/` izinleri 775
- [ ] `.env` dosyası 644 izinli
- [ ] Cache oluşturdum (config, route, view)
- [ ] `storage:link` yaptım
- [ ] Ana sayfa açılıyor ✅
- [ ] Login çalışıyor ✅
- [ ] CSS/JS yükleniyor ✅

---

## 💡 Öneriler

1. **Mümkünse subdomain kullanın:**
   - Ana domain: `yourdomain.com` → `public_html/public/`
   - Laravel: `public_html/` (Laravel dosyaları)

2. **Veya hosting değiştirin:**
   - VPS/Cloud hosting alın
   - Document root'u `public/` olarak ayarlayın

3. **Düzenli yedek alın:**
   - Veritabanı
   - `.env` dosyası
   - `storage/app/` (yüklenen dosyalar)

4. **Logları izleyin:**
   - `storage/logs/laravel.log`
   - Hosting error_log

---

## ⚠️ Son Uyarı

Bu yöntem **ideal değildir**. Mümkünse:
- Subdomain kullanın
- VPS alın
- Document root'u düzenleyin

Ama tek seçenek buysa, yukarıdaki güvenlik önlemlerini **MUTLAKA** alın!
















