# AtakoDB2B Uygulamasi

Laravel tabanli, genel sektorlere hitap eden profesyonel B2B e-ticaret platformu.

## Ozellikler

### Kullanici Tipleri
- **Admin**: Sistem yonetimi, slider yonetimi, urun ve musteri yonetimi
- **Plasiyer**: Musteri takibi ve siparis yonetimi
- **Musteri**: Urun goruntuleme ve siparis verme

### Ana Ozellikler

#### Urun Yonetimi
- Urun Kodu, Urun Adi, Barkod
- Satis Fiyati, KDV Orani
- Marka, Grup
- 5 Adet Ozel Kod Alani (Kod1-Kod5)
- Urun Resmi Destegi

#### Musteri Yonetimi
- Musteri Kodu, Musteri Adi
- Tam Adres (Adres, Ilce, Il)
- GLN Numarasi
- Iletisim Bilgileri (Telefon, E-posta)
- Vergi Bilgileri (Vergi Dairesi, VKN)
- Grup Kodu ve 5 Ozel Kod Alani
- Plasiyer Kodu Atamasi

#### Urun Goruntuleme
- **Katalog Gorunumu**: Resimli urun kartlari
- **Liste Gorunumu**: Hizli siparis icin tablo formati
- Her iki gorunumde de:
  - Miktar belirleme (spin button veya direkt giris)
  - Dogrudan sepete ekleme
  - Resim onizleme (liste gorunumunde hover ile)

#### Sepet Sistemi
- Gercek zamanli sepet sayaci
- Urun ekleme/cikarma
- Miktar guncelleme
- KDV dahil/haric fiyat gosterimi

#### Admin Panel
- Slider yonetimi (resim, baslik, aciklama, siralama)
- Site ismi ve logosu ayarlari
- Istatistikler dashboard
- Kullanici, urun ve musteri ozet bilgileri

## Kurulum

### Gereksinimler
- PHP 8.1 veya uzeri
- Composer
- MySQL/MariaDB
- Node.js ve NPM (opsiyonel, frontend asset'leri icin)

### Kurulum Adimlari

1. **Bagimliliklari Yukleyin**
```bash
composer install
```

2. **Veritabani Yapilandirmasi**
`.env` dosyasini duzenleyin:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=atakodb2b
DB_USERNAME=root
DB_PASSWORD=
```

3. **Uygulama Anahtari Olusturun**
```bash
php artisan key:generate
```

4. **Veritabani Migration ve Seed**
```bash
php artisan migrate
php artisan db:seed
```

5. **Storage Link Olusturun**
```bash
php artisan storage:link
```

6. **Uygulamayi Calistirin**
```bash
php artisan serve
```

Uygulama `http://localhost:8000` adresinde calisacaktir.

## Varsayilan Kullanicilar

Sistem varsayilan olarak 3 test kullanicisi ile gelir:

### Admin
- E-posta: `admin@atakodb2b.com`
- Sifre: `admin123`

### Plasiyer
- E-posta: `plasiyer@atakodb2b.com`
- Sifre: `plasiyer123`

### Musteri
- E-posta: `musteri@atakodb2b.com`
- Sifre: `musteri123`

## Veritabani Yapisi

### Products (Urunler)
- urun_kodu, urun_adi, barkod
- satis_fiyati, kdv_orani
- marka, grup
- kod1, kod2, kod3, kod4, kod5
- urun_resmi

### Customers (Musteriler)
- musteri_kodu, musteri_adi
- adres, ilce, il
- gln_numarasi
- telefon, mail_adresi
- vergi_dairesi, vergi_kimlik_numarasi
- grup_kodu, kod1-kod5
- plasiyer_kodu

### Users (Kullanicilar)
- name, email, password
- user_type (admin, plasiyer, musteri)
- customer_id (musteri kullanicilari icin)
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
- **Veritabani**: MySQL
- **Ikonlar**: Font Awesome 6.4

## Guvenlik

- CSRF korumasi tum formlarda aktif
- Middleware tabanli yetkilendirme
- Sifreler hash'lenerek saklanir
- Kullanici tiplerine gore erisim kontrolu

## Lisans

Bu proje MIT lisansi ile lisanslanmistir.

## Destek

Herhangi bir sorun veya oneri icin lutfen iletisime gecin.

---

**AtakoDB2B** - Kaliteli B2B Cozumler
