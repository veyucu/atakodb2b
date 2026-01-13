# atakodb2b UygulamasÄ±

Laravel tabanlÄ±, genel sektÃ¶rlere hitap eden profesyonel B2B e-ticaret platformu.

## Ã–zellikler

### KullanÄ±cÄ± Tipleri
- **Admin**: Sistem yÃ¶netimi, slider yÃ¶netimi, Ã¼rÃ¼n ve mÃ¼ÅŸteri yÃ¶netimi
- **Plasiyer**: MÃ¼ÅŸteri takibi ve sipariÅŸ yÃ¶netimi
- **MÃ¼ÅŸteri**: ÃœrÃ¼n gÃ¶rÃ¼ntÃ¼leme ve sipariÅŸ verme

### Ana Ã–zellikler

#### ÃœrÃ¼n YÃ¶netimi
- ÃœrÃ¼n Kodu, ÃœrÃ¼n AdÄ±, Barkod
- SatÄ±ÅŸ FiyatÄ±, KDV OranÄ±
- Marka, Grup
- 5 Adet Ã–zel Kod AlanÄ± (Kod1-Kod5)
- ÃœrÃ¼n Resmi DesteÄŸi

#### MÃ¼ÅŸteri YÃ¶netimi
- MÃ¼ÅŸteri Kodu, MÃ¼ÅŸteri AdÄ±
- Tam Adres (Adres, Ä°lÃ§e, Ä°l)
- GLN NumarasÄ±
- Ä°letiÅŸim Bilgileri (Telefon, E-posta)
- Vergi Bilgileri (Vergi Dairesi, VKN)
- Grup Kodu ve 5 Ã–zel Kod AlanÄ±
- Plasiyer Kodu AtamasÄ±

#### ÃœrÃ¼n GÃ¶rÃ¼ntÃ¼leme
- **Katalog GÃ¶rÃ¼nÃ¼mÃ¼**: Resimli Ã¼rÃ¼n kartlarÄ±
- **Liste GÃ¶rÃ¼nÃ¼mÃ¼**: HÄ±zlÄ± sipariÅŸ iÃ§in tablo formatÄ±
- Her iki gÃ¶rÃ¼nÃ¼mde de:
  - Miktar belirleme (spin button veya direkt giriÅŸ)
  - DoÄŸrudan sepete ekleme
  - Resim Ã¶nizleme (liste gÃ¶rÃ¼nÃ¼mÃ¼nde hover ile)

#### Sepet Sistemi
- GerÃ§ek zamanlÄ± sepet sayacÄ±
- ÃœrÃ¼n ekleme/Ã§Ä±karma
- Miktar gÃ¼ncelleme
- KDV dahil/hariÃ§ fiyat gÃ¶sterimi

#### Admin Panel
- Slider yÃ¶netimi (resim, baÅŸlÄ±k, aÃ§Ä±klama, sÄ±ralama)
- Site ismi ve logosu ayarlarÄ±
- Ä°statistikler dashboard
- KullanÄ±cÄ±, Ã¼rÃ¼n ve mÃ¼ÅŸteri Ã¶zet bilgileri

## Kurulum

### Gereksinimler
- PHP 8.1 veya Ã¼zeri
- Composer
- MySQL/MariaDB
- Node.js ve NPM (opsiyonel, frontend asset'leri iÃ§in)

### Kurulum AdÄ±mlarÄ±

1. **BaÄŸÄ±mlÄ±lÄ±klarÄ± YÃ¼kleyin**
```bash
composer install
```

2. **VeritabanÄ± YapÄ±landÄ±rmasÄ±**
`.env` dosyasÄ±nÄ± dÃ¼zenleyin:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=atakodb2b
DB_USERNAME=root
DB_PASSWORD=
```

3. **Uygulama AnahtarÄ± OluÅŸturun**
```bash
php artisan key:generate
```

4. **VeritabanÄ± Migration ve Seed**
```bash
php artisan migrate
php artisan db:seed
```

5. **Storage Link OluÅŸturun**
```bash
php artisan storage:link
```

6. **UygulamayÄ± Ã‡alÄ±ÅŸtÄ±rÄ±n**
```bash
php artisan serve
```

Uygulama `http://localhost:8000` adresinde Ã§alÄ±ÅŸacaktÄ±r.

## VarsayÄ±lan KullanÄ±cÄ±lar

Sistem varsayÄ±lan olarak 3 test kullanÄ±cÄ±sÄ± ile gelir:

### Admin
- E-posta: `admin@atakodb2b.com`
- Åifre: `admin123`

### Plasiyer
- E-posta: `plasiyer@atakodb2b.com`
- Åifre: `plasiyer123`

### MÃ¼ÅŸteri
- E-posta: `musteri@atakodb2b.com`
- Åifre: `musteri123`

## VeritabanÄ± YapÄ±sÄ±

### Products (ÃœrÃ¼nler)
- urun_kodu, urun_adi, barkod
- satis_fiyati, kdv_orani
- marka, grup
- kod1, kod2, kod3, kod4, kod5
- urun_resmi

### Customers (MÃ¼ÅŸteriler)
- musteri_kodu, musteri_adi
- adres, ilce, il
- gln_numarasi
- telefon, mail_adresi
- vergi_dairesi, vergi_kimlik_numarasi
- grup_kodu, kod1-kod5
- plasiyer_kodu

### Users (KullanÄ±cÄ±lar)
- name, email, password
- user_type (admin, plasiyer, musteri)
- customer_id (mÃ¼ÅŸteri kullanÄ±cÄ±larÄ± iÃ§in)
- plasiyer_kodu

### Sliders
- title, description
- image, link
- order, is_active

### Carts (Sepet)
- user_id, product_id
- quantity, price

## Teknolojiler

- **Backend**: Laravel 10.x
- **Frontend**: Bootstrap 5.3, jQuery
- **VeritabanÄ±**: MySQL
- **Ä°konlar**: Font Awesome 6.4

## GÃ¼venlik

- CSRF korumasÄ± tÃ¼m formlarda aktif
- Middleware tabanlÄ± yetkilendirme
- Åifreler hash'lenerek saklanÄ±r
- KullanÄ±cÄ± tiplerine gÃ¶re eriÅŸim kontrolÃ¼

## Lisans

Bu proje MIT lisansÄ± ile lisanslanmÄ±ÅŸtÄ±r.

## Destek

Herhangi bir sorun veya Ã¶neri iÃ§in lÃ¼tfen iletiÅŸime geÃ§in.

---

**atakodb2b** - Kaliteli B2B Ã‡Ã¶zÃ¼mler




















