# atakodb2b - Kurulum Rehberi

## Sistem Gereksinimleri

- PHP 8.1 veya Ã¼zeri
- MySQL 5.7+ veya MariaDB 10.3+
- Composer
- XAMPP veya benzeri web sunucu ortamÄ±

## AdÄ±m AdÄ±m Kurulum

### 1. Projeyi Ä°ndirin

Proje zaten `C:\xampp\htdocs\atakodb2b` dizininde bulunmaktadÄ±r.

### 2. Composer BaÄŸÄ±mlÄ±lÄ±klarÄ±nÄ± YÃ¼kleyin

Komut satÄ±rÄ±nÄ± aÃ§Ä±n ve proje dizinine gidin:

```bash
cd C:\xampp\htdocs\atakodb2b
composer install
```

### 3. VeritabanÄ±nÄ± OluÅŸturun

XAMPP Control Panel'den MySQL'i baÅŸlatÄ±n, ardÄ±ndan phpMyAdmin'e gidin (`http://localhost/phpmyadmin`) ve yeni bir veritabanÄ± oluÅŸturun:

- VeritabanÄ± AdÄ±: `atakodb2b`
- Karakter Seti: `utf8mb4_unicode_ci`

### 4. Environment DosyasÄ±nÄ± YapÄ±landÄ±rÄ±n

`.env` dosyasÄ± zaten oluÅŸturulmuÅŸtur. Kontrol edin ve gerekirse dÃ¼zenleyin:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=atakodb2b
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Uygulama AnahtarÄ± OluÅŸturun

```bash
php artisan key:generate
```

### 6. VeritabanÄ± TablolarÄ±nÄ± OluÅŸturun

```bash
php artisan migrate
```

### 7. Ã–rnek Verileri YÃ¼kleyin

```bash
php artisan db:seed
```

Bu komut ÅŸu verileri oluÅŸturacaktÄ±r:
- 3 kullanÄ±cÄ± (admin, plasiyer, mÃ¼ÅŸteri)
- 1 Ã¶rnek mÃ¼ÅŸteri firmasÄ±
- 6 Ã¶rnek Ã¼rÃ¼n
- 2 slider resmi

### 8. Storage Linkini OluÅŸturun

```bash
php artisan storage:link
```

Bu komut `public/storage` klasÃ¶rÃ¼nÃ¼ `storage/app/public` klasÃ¶rÃ¼ne baÄŸlar.

### 9. Storage KlasÃ¶rlerini OluÅŸturun

AÅŸaÄŸÄ±daki klasÃ¶rlerin var olduÄŸundan emin olun:

```
storage/app/public/
storage/app/public/sliders/
storage/app/public/products/
```

EÄŸer yoksa, manuel olarak oluÅŸturun.

### 10. Sunucuyu BaÅŸlatÄ±n

#### SeÃ§enek A: Laravel Development Server

```bash
php artisan serve
```

ArdÄ±ndan tarayÄ±cÄ±nÄ±zda `http://localhost:8000` adresine gidin.

#### SeÃ§enek B: XAMPP ile

XAMPP Control Panel'den Apache'yi baÅŸlatÄ±n ve tarayÄ±cÄ±nÄ±zda ÅŸu adrese gidin:
```
http://localhost/atakodb2b/public
```

**Not**: Virtual host yapÄ±landÄ±rmasÄ± iÃ§in Apache `httpd-vhosts.conf` dosyasÄ±na ekleyin:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/atakodb2b/public"
    ServerName atakodb2b.local
    <Directory "C:/xampp/htdocs/atakodb2b/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Ve `C:\Windows\System32\drivers\etc\hosts` dosyasÄ±na ekleyin:
```
127.0.0.1 atakodb2b.local
```

## Test KullanÄ±cÄ±larÄ±

Sistem aÅŸaÄŸÄ±daki test kullanÄ±cÄ±larÄ± ile gelir:

### Admin KullanÄ±cÄ±
- **E-posta**: admin@atakodb2b.com
- **Åifre**: admin123
- **Yetkiler**: TÃ¼m sistem yÃ¶netimi

### Plasiyer KullanÄ±cÄ±
- **E-posta**: plasiyer@atakodb2b.com
- **Åifre**: plasiyer123
- **Yetkiler**: MÃ¼ÅŸteri takibi ve sipariÅŸ yÃ¶netimi

### MÃ¼ÅŸteri KullanÄ±cÄ±
- **E-posta**: musteri@atakodb2b.com
- **Åifre**: musteri123
- **Yetkiler**: ÃœrÃ¼n gÃ¶rÃ¼ntÃ¼leme ve sipariÅŸ verme

## Slider Resimleri HakkÄ±nda

Seed iÅŸlemi varsayÄ±lan slider giriÅŸleri oluÅŸturur ancak resimler dahil deÄŸildir. Slider'larÄ±n dÃ¼zgÃ¼n gÃ¶rÃ¼nmesi iÃ§in:

1. Admin hesabÄ± ile giriÅŸ yapÄ±n
2. Admin Panel > Slider YÃ¶netimi'ne gidin
3. Mevcut slider'larÄ± dÃ¼zenleyin ve gerÃ§ek resimler yÃ¼kleyin

Veya `storage/app/public/sliders/` klasÃ¶rÃ¼ne manuel olarak resim ekleyin ve veritabanÄ±ndaki `image` alanlarÄ±nÄ± gÃ¼ncelleyin.

## ÃœrÃ¼n Resimleri Ekleme

ÃœrÃ¼n resimlerini eklemek iÃ§in:

1. Resimleri `storage/app/public/products/` klasÃ¶rÃ¼ne yÃ¼kleyin
2. VeritabanÄ±nda ilgili Ã¼rÃ¼nÃ¼n `urun_resmi` alanÄ±nÄ± gÃ¼ncelleyin (Ã¶rnek: `products/urun1.jpg`)

Veya admin panelden (geliÅŸtirme aÅŸamasÄ±nda) Ã¼rÃ¼n yÃ¶netimi ile resim yÃ¼kleyebilirsiniz.

## Sorun Giderme

### Permission HatalarÄ±

Windows'ta genellikle sorun olmaz, ancak Linux/Mac'te:

```bash
chmod -R 775 storage bootstrap/cache
```

### Composer YÃ¼klenemiyor

Composer yÃ¼klÃ¼ deÄŸilse:
1. https://getcomposer.org/download/ adresinden indirin
2. Kurulum dosyasÄ±nÄ± Ã§alÄ±ÅŸtÄ±rÄ±n
3. Sisteminizi yeniden baÅŸlatÄ±n

### Migration HatalarÄ±

EÄŸer migration hatasÄ± alÄ±rsanÄ±z:

```bash
php artisan migrate:fresh --seed
```

**UYARI**: Bu komut tÃ¼m verileri siler ve yeniden oluÅŸturur!

### Resimler GÃ¶rÃ¼nmÃ¼yor

1. Storage link oluÅŸturuldu mu kontrol edin:
   ```bash
   php artisan storage:link
   ```

2. `public/storage` klasÃ¶rÃ¼nÃ¼n `storage/app/public` klasÃ¶rÃ¼ne link olduÄŸundan emin olun

## GeliÅŸtirme NotlarÄ±

### Yeni Ã–zellikler Eklemek

- **Controller**: `app/Http/Controllers/` dizinine ekleyin
- **Model**: `app/Models/` dizinine ekleyin
- **View**: `resources/views/` dizinine ekleyin
- **Route**: `routes/web.php` dosyasÄ±na ekleyin
- **Migration**: `php artisan make:migration` komutu ile oluÅŸturun

### Cache Temizleme

GeliÅŸtirme sÄ±rasÄ±nda sorun yaÅŸarsanÄ±z:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## GÃ¼venlik NotlarÄ±

**Ãœretim ortamÄ±na geÃ§meden Ã¶nce**:

1. `.env` dosyasÄ±nda `APP_ENV=production` yapÄ±n
2. `APP_DEBUG=false` ayarlayÄ±n
3. GÃ¼Ã§lÃ¼ `APP_KEY` oluÅŸturun
4. VeritabanÄ± kullanÄ±cÄ±sÄ± iÃ§in gÃ¼Ã§lÃ¼ ÅŸifre kullanÄ±n
5. Test kullanÄ±cÄ± ÅŸifrelerini deÄŸiÅŸtirin
6. HTTPS kullanÄ±n

## Destek

Herhangi bir sorun yaÅŸarsanÄ±z:
- Laravel dokÃ¼mantasyonu: https://laravel.com/docs
- README.md dosyasÄ±nÄ± kontrol edin

## Sonraki AdÄ±mlar

1. Admin hesabÄ± ile giriÅŸ yapÄ±n
2. Slider resimlerini yÃ¼kleyin
3. ÃœrÃ¼n resimlerini ekleyin
4. GerÃ§ek Ã¼rÃ¼n ve mÃ¼ÅŸteri verilerini girin
5. Sistemi test edin

BaÅŸarÄ±lar! ğŸ‰




















