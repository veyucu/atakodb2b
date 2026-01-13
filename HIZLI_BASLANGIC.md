# HÄ±zlÄ± BaÅŸlangÄ±Ã§ Rehberi

## ğŸš€ 5 Dakikada BaÅŸlayÄ±n!

### Ã–n KoÅŸullar
- âœ… XAMPP kurulu
- âœ… Composer kurulu
- âœ… Proje dosyalarÄ± `C:\xampp\htdocs\atakodb2b` dizininde

### AdÄ±m 1: XAMPP'i BaÅŸlatÄ±n
XAMPP Control Panel'den **Apache** ve **MySQL** servislerini baÅŸlatÄ±n.

### AdÄ±m 2: Komut SatÄ±rÄ±nÄ± AÃ§Ä±n
```bash
cd C:\xampp\htdocs\atakodb2b
```

### AdÄ±m 3: Composer Kurulumu
```bash
composer install
```

### AdÄ±m 4: VeritabanÄ±nÄ± OluÅŸturun
1. TarayÄ±cÄ±da `http://localhost/phpmyadmin` aÃ§Ä±n
2. Yeni veritabanÄ± oluÅŸturun: `atakodb2b`
3. Karakter seti: `utf8mb4_unicode_ci`

### AdÄ±m 5: Uygulama AnahtarÄ±
```bash
php artisan key:generate
```

### AdÄ±m 6: VeritabanÄ± Kurulumu
```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### AdÄ±m 7: Sunucuyu BaÅŸlatÄ±n
```bash
php artisan serve
```

### AdÄ±m 8: TarayÄ±cÄ±da AÃ§Ä±n
`http://localhost:8000` adresine gidin.

## ğŸ” Test KullanÄ±cÄ±larÄ±

| Rol | E-posta | Åifre |
|-----|---------|-------|
| **Admin** | admin@atakodb2b.com | admin123 |
| **Plasiyer** | plasiyer@atakodb2b.com | plasiyer123 |
| **MÃ¼ÅŸteri** | musteri@atakodb2b.com | musteri123 |

## ğŸ“‹ Ä°lk YapÄ±lacaklar

1. **Admin ile giriÅŸ yapÄ±n**
2. **Slider resimlerini yÃ¼kleyin** (Admin Panel > Slider YÃ¶netimi)
3. **ÃœrÃ¼n resimlerini ekleyin**
4. **Sistemi test edin**

## ğŸ¯ Temel Ã–zellikler

### MÃ¼ÅŸteri GÃ¶rÃ¼nÃ¼mÃ¼
- âœ… Slider ile karÅŸÄ±lama
- âœ… Katalog/Liste gÃ¶rÃ¼nÃ¼mÃ¼
- âœ… ÃœrÃ¼n arama
- âœ… Sepete ekleme
- âœ… Sepet yÃ¶netimi

### Admin Paneli
- âœ… Dashboard istatistikleri
- âœ… Slider yÃ¶netimi
- âœ… KullanÄ±cÄ± kontrolÃ¼

### Plasiyer Paneli
- âœ… MÃ¼ÅŸteri takibi (geliÅŸtirme aÅŸamasÄ±nda)
- âœ… SipariÅŸ yÃ¶netimi (geliÅŸtirme aÅŸamasÄ±nda)

## âš ï¸ Sorun mu Var?

### Port 8000 kullanÄ±mda?
```bash
php artisan serve --port=8080
```

### Composer bulunamadÄ±?
Composer'Ä± https://getcomposer.org adresinden indirin.

### Migration hatasÄ±?
```bash
php artisan migrate:fresh --seed
```
**UYARI**: TÃ¼m verileri siler!

### Resimler gÃ¶rÃ¼nmÃ¼yor?
```bash
php artisan storage:link
```

## ğŸ“š Daha Fazla Bilgi

DetaylÄ± kurulum iÃ§in `KURULUM.md` dosyasÄ±na bakÄ±n.

## ğŸ‰ HazÄ±rsÄ±nÄ±z!

ArtÄ±k atakodb2b uygulamanÄ±z Ã§alÄ±ÅŸÄ±yor. Ä°yi Ã§alÄ±ÅŸmalar!




















