# 🚀 Hosting Kurulum Rehberi - atakodb2b

## 📋 Gereksinimler
- PHP 8.1 veya üstü ✅ (Sizde 8.2 var)
- MySQL 5.7+
- Composer
- Apache/Nginx

## 1️⃣ Dosyaları Yükleyin

### Dosya Yapısı (Önerilen)
```
root/
├── atakodb2b/              # Ana Laravel klasörü
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
└── public_html/            # Web root
    ├── index.php
    ├── .htaccess
    ├── css/
    ├── js/
    └── ...
```

## 2️⃣ .env Dosyası

`.env` dosyasını oluşturun veya `.env.example`'dan kopyalayın:

```bash
cp .env.example .env
```

### Kritik Ayarlar:

```env
APP_NAME=atakodb2b
APP_ENV=production
APP_KEY=base64:XXXXX  # php artisan key:generate
APP_DEBUG=false       # ÖNEMLİ: Production'da false!
APP_URL=https://yourdomain.com

# Veritabanı (Hosting panelinden alacaksınız)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_pass

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Mail (Opsiyonel)
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourhost.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=yourpassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 3️⃣ Veritabanı

### Local'den Export:
```bash
# XAMPP phpMyAdmin'den export alın
# veya komut satırı:
mysqldump -u root -p atakodb2b > backup.sql
```

### Hosting'e Import:
1. cPanel > phpMyAdmin
2. Veritabanı oluşturun
3. SQL dosyasını import edin

## 4️⃣ Composer Paketleri

SSH veya hosting terminal'den:

```bash
cd /path/to/atakodb2b
composer install --no-dev --optimize-autoloader
```

**SSH yoksa:** `vendor` klasörünü FTP ile yükleyin (ancak yavaş olabilir)

## 5️⃣ Storage Link Oluşturma

```bash
php artisan storage:link
```

**SSH yoksa:** Manuel symlink oluşturun veya dosyaları `public/storage`'a kopyalayın

## 6️⃣ Dosya İzinleri (Çok Önemli!) 🔒

```bash
# Storage ve bootstrap/cache yazılabilir olmalı
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Sahibi web sunucusu kullanıcısı yapın
chown -R www-data:www-data storage bootstrap/cache
# veya
chown -R nobody:nobody storage bootstrap/cache
```

## 7️⃣ Cache Temizleme & Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**Değişiklik yapınca cache temizleme:**
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## 8️⃣ .htaccess Düzenleme

### public/.htaccess (Zaten Laravel ile gelir)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Root .htaccess (Eğer public klasörü public_html dışındaysa)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## 9️⃣ index.php Düzenleme

Eğer dosya yapısını değiştirdiyseniz, `public/index.php` dosyasında path'leri güncelleyin:

```php
require __DIR__.'/../atakodb2b/vendor/autoload.php';
$app = require_once __DIR__.'/../atakodb2b/bootstrap/app.php';
```

## 🔟 Güvenlik Kontrolleri

### ✅ Yapılması Gerekenler:

1. **APP_DEBUG=false** - Production'da MUTLAKA!
2. **APP_ENV=production**
3. **phpinfo.php sil** - Güvenlik riski
4. **.env dosyası erişilebilir olmamalı** - public dışında tutun
5. **HTTPS kullanın** - SSL sertifikası aktif edin
6. **Güçlü veritabanı şifresi**

### 🚫 .env Dosyasını Koruma:

`.htaccess` ekleyin (root'ta):
```apache
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

## 📝 Hızlı Komut Listesi

```bash
# Kurulum
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Temizlik (Geliştirme)
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# İzinler
chmod -R 775 storage bootstrap/cache
```

## 🔍 Sorun Giderme

### 500 Internal Server Error
- **storage/** ve **bootstrap/cache/** izinlerini kontrol edin (775)
- **.env** dosyası doğru mu?
- **APP_KEY** var mı? (`php artisan key:generate`)
- **Apache mod_rewrite** aktif mi?

### Veritabanı Bağlantı Hatası
- **.env** DB bilgileri doğru mu?
- Veritabanı kullanıcısının uzaktan erişim izni var mı?
- DB_HOST genellikle **localhost** olmalı

### CSS/JS Yüklenmiyor
- **APP_URL** doğru mu?
- **storage:link** yaptınız mı?
- **public/** klasöründe **css, js, images** var mı?

### 404 Hatası
- **.htaccess** doğru yerde mi?
- **mod_rewrite** aktif mi?
- **AllowOverride All** Apache config'de var mı?

## 📞 Hosting Sağlayıcıdan Almanız Gerekenler

1. **Veritabanı Bilgileri:**
   - DB Host (genellikle localhost)
   - DB Name
   - DB User
   - DB Password

2. **Mail Bilgileri** (Opsiyonel):
   - SMTP Host
   - SMTP Port
   - SMTP Username
   - SMTP Password

3. **SSH Erişimi** (Varsa):
   - Host
   - Port
   - Username
   - Password/Key

## ✅ Test Checklist

- [ ] Ana sayfa açılıyor mu?
- [ ] Login çalışıyor mu?
- [ ] Veritabanı bağlantısı var mı?
- [ ] Resimler yükleniyor mu?
- [ ] CSS/JS dosyaları yükleniyor mu?
- [ ] Admin paneli çalışıyor mu?
- [ ] Ürün ekleme/düzenleme çalışıyor mu?
- [ ] Sepet işlemleri çalışıyor mu?
- [ ] Sipariş oluşturma çalışıyor mu?

## 🎉 Tamamlandı!

Site artık canlıda! 🚀

**Önemli:** 
- İlk kurulumdan sonra `phpinfo.php` dosyasını silin
- Düzenli yedek alın (veritabanı + dosyalar)
- Log dosyalarını kontrol edin: `storage/logs/`
















