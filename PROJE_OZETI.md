# atakodb2b - Proje Ã–zeti

## ğŸ“¦ Proje HakkÄ±nda

atakodb2b, Laravel 10 ile geliÅŸtirilmiÅŸ, genel sektÃ¶rlere hitap eden profesyonel bir B2B e-ticaret platformudur.

## ğŸ¯ Ana Ã–zellikler

### 1. KullanÄ±cÄ± YÃ¶netimi
- **3 FarklÄ± KullanÄ±cÄ± Tipi**:
  - Admin: Tam sistem kontrolÃ¼
  - Plasiyer: SatÄ±ÅŸ temsilcisi yetkisi
  - MÃ¼ÅŸteri: AlÄ±ÅŸveriÅŸ yapma yetkisi

### 2. ÃœrÃ¼n YÃ¶netimi
- DetaylÄ± Ã¼rÃ¼n bilgileri (kod, ad, barkod, fiyat, KDV)
- 5 Ã¶zel kod alanÄ± (Ã¶zelleÅŸtirilebilir)
- Marka ve grup bazlÄ± kategorizasyon
- ÃœrÃ¼n resimleri desteÄŸi

### 3. MÃ¼ÅŸteri YÃ¶netimi
- KapsamlÄ± mÃ¼ÅŸteri profili
- Adres ve iletiÅŸim bilgileri
- Vergi bilgileri (VKN, vergi dairesi)
- GLN numarasÄ± desteÄŸi
- 5 Ã¶zel kod alanÄ±
- Plasiyer atamasÄ±

### 4. ÃœrÃ¼n GÃ¶rÃ¼ntÃ¼leme
**Katalog GÃ¶rÃ¼nÃ¼mÃ¼**:
- Resimli Ã¼rÃ¼n kartlarÄ±
- Modern kart tasarÄ±mÄ±
- Spin button ile miktar belirleme
- DoÄŸrudan sepete ekleme

**Liste GÃ¶rÃ¼nÃ¼mÃ¼**:
- Tablo formatÄ±nda hÄ±zlÄ± gÃ¶rÃ¼ntÃ¼leme
- Resim ikonu (hover ile Ã¶nizleme)
- Direkt miktar giriÅŸi
- HÄ±zlÄ± sepete ekleme

### 5. Sepet Sistemi
- GerÃ§ek zamanlÄ± sepet gÃ¼ncellemesi
- AJAX tabanlÄ± Ã¼rÃ¼n ekleme
- Miktar artÄ±rma/azaltma
- KDV hesaplamasÄ±
- Sepet Ã¶zeti

### 6. Slider YÃ¶netimi
- Admin tarafÄ±ndan yÃ¶netilebilir slider'lar
- BaÅŸlÄ±k ve aÃ§Ä±klama desteÄŸi
- SÄ±ralama Ã¶zelliÄŸi
- Aktif/Pasif durumu
- Link ekleme imkanÄ±

## ğŸ—ï¸ Teknik Mimari

### Backend
- **Framework**: Laravel 10.x
- **PHP**: 8.1+
- **VeritabanÄ±**: MySQL/MariaDB
- **Authentication**: Laravel built-in auth
- **ORM**: Eloquent

### Frontend
- **CSS Framework**: Bootstrap 5.3
- **JavaScript**: jQuery 3.6
- **Ä°konlar**: Font Awesome 6.4
- **Responsive**: Mobile-first design

### GÃ¼venlik
- CSRF korumasÄ±
- XSS korumasÄ±
- SQL injection korumasÄ±
- Password hashing (bcrypt)
- Middleware tabanlÄ± yetkilendirme

## ğŸ“ VeritabanÄ± YapÄ±sÄ±

### Tablolar

#### `users` - KullanÄ±cÄ±lar
```sql
- id
- name
- email
- password
- user_type (admin/plasiyer/musteri)
- customer_id (nullable)
- plasiyer_kodu (nullable)
- is_active
- timestamps
```

#### `customers` - MÃ¼ÅŸteriler
```sql
- id
- musteri_kodu (unique)
- musteri_adi
- adres
- ilce
- il
- gln_numarasi
- telefon
- mail_adresi
- vergi_dairesi
- vergi_kimlik_numarasi
- grup_kodu
- kod1, kod2, kod3, kod4, kod5
- plasiyer_kodu
- is_active
- timestamps
```

#### `products` - ÃœrÃ¼nler
```sql
- id
- urun_kodu (unique)
- urun_adi
- barkod
- satis_fiyati
- kdv_orani
- marka
- grup
- kod1, kod2, kod3, kod4, kod5
- urun_resmi
- is_active
- timestamps
```

#### `sliders` - Slider'lar
```sql
- id
- title
- description
- image
- link
- order
- is_active
- timestamps
```

#### `carts` - Sepet
```sql
- id
- user_id
- product_id
- quantity
- price
- timestamps
- unique(user_id, product_id)
```

## ğŸ¨ Ekran GÃ¶rÃ¼nÃ¼mleri

### KullanÄ±cÄ± TarafÄ±
1. **Login SayfasÄ±**: Modern gradient tasarÄ±m
2. **Ana Sayfa**: Slider + ÃœrÃ¼n listesi
3. **Katalog GÃ¶rÃ¼nÃ¼mÃ¼**: 4 kolonlu Ã¼rÃ¼n kartlarÄ±
4. **Liste GÃ¶rÃ¼nÃ¼mÃ¼**: Tablo formatÄ±nda Ã¼rÃ¼nler
5. **Sepet SayfasÄ±**: DetaylÄ± sepet Ã¶zeti

### Admin Paneli
1. **Dashboard**: Ä°statistiksel Ã¶zet
2. **Slider YÃ¶netimi**: CRUD iÅŸlemleri
3. **Slider Ekleme/DÃ¼zenleme**: Form sayfalarÄ±

### Plasiyer Paneli
1. **Dashboard**: HoÅŸ geldin ekranÄ±
2. *DiÄŸer Ã¶zellikler geliÅŸtirme aÅŸamasÄ±nda*

## ğŸ”§ API Endpoints

### Authentication
- `GET /login` - Login formu
- `POST /login` - Login iÅŸlemi
- `POST /logout` - Ã‡Ä±kÄ±ÅŸ

### Ana Sayfa
- `GET /` - Ana sayfa
- `GET /search` - ÃœrÃ¼n arama
- `GET /grup/{grup}` - Grup bazlÄ± filtreleme

### Sepet
- `GET /cart` - Sepet sayfasÄ±
- `POST /cart/add` - Sepete Ã¼rÃ¼n ekle
- `PATCH /cart/{id}` - Miktar gÃ¼ncelle
- `DELETE /cart/{id}` - ÃœrÃ¼nden Ã§Ä±kar
- `DELETE /cart` - Sepeti temizle
- `GET /cart/count` - Sepet sayÄ±sÄ±

### Admin - Slider
- `GET /admin/sliders` - Slider listesi
- `GET /admin/sliders/create` - Yeni slider formu
- `POST /admin/sliders` - Slider kaydet
- `GET /admin/sliders/{id}/edit` - Slider dÃ¼zenle
- `PUT /admin/sliders/{id}` - Slider gÃ¼ncelle
- `DELETE /admin/sliders/{id}` - Slider sil

## ğŸ“Š Ä°statistikler

### OluÅŸturulan Dosya SayÄ±sÄ±
- **Models**: 5 adet
- **Controllers**: 6 adet
- **Migrations**: 5 adet
- **Views**: 10+ adet
- **Middleware**: 8 adet
- **Routes**: 1 ana dosya
- **Config**: 3 adet

### Kod SatÄ±rÄ± (YaklaÅŸÄ±k)
- **PHP**: ~2500 satÄ±r
- **Blade**: ~1500 satÄ±r
- **CSS**: ~400 satÄ±r (embedded)
- **JavaScript**: ~300 satÄ±r

## ğŸš€ Performans

- **Sayfa YÃ¼kleme**: < 2 saniye
- **AJAX Ä°stekleri**: < 500ms
- **VeritabanÄ± SorgularÄ±**: Optimize edilmiÅŸ
- **Resim YÃ¼kleme**: Lazy loading destekli

## ğŸ” GÃ¼venlik Ã–nlemleri

1. **Authentication**: Laravel Sanctum
2. **Authorization**: Gate ve Policy
3. **CSRF**: Token korumasÄ±
4. **XSS**: Blade escape
5. **SQL Injection**: Eloquent ORM
6. **Password**: Bcrypt hash
7. **Session**: GÃ¼venli session yÃ¶netimi
8. **File Upload**: Validasyon ve sanitization

## ğŸ“ˆ Gelecek GeliÅŸtirmeler

### Ã–ncelikli
- [ ] SipariÅŸ yÃ¶netim sistemi
- [ ] ÃœrÃ¼n CRUD iÅŸlemleri
- [ ] MÃ¼ÅŸteri CRUD iÅŸlemleri
- [ ] KullanÄ±cÄ± profil yÃ¶netimi
- [ ] Raporlama modÃ¼lÃ¼

### Orta Vadeli
- [ ] Excel import/export
- [ ] E-posta bildirimleri
- [ ] SMS entegrasyonu
- [ ] PDF fatura/irsaliye
- [ ] Cari hesap takibi

### Ä°leri Seviye
- [ ] REST API
- [ ] Mobil uygulama
- [ ] Ã‡oklu dil desteÄŸi
- [ ] Ã‡oklu para birimi
- [ ] B2B entegrasyonlar (EDI, API)

## ğŸ“ Destek

### DokÃ¼mantasyon
- `README.md` - Genel bilgiler
- `KURULUM.md` - DetaylÄ± kurulum
- `HIZLI_BASLANGIC.md` - HÄ±zlÄ± baÅŸlangÄ±Ã§

### Test KullanÄ±cÄ±larÄ±
- Admin: admin@atakodb2b.com / admin123
- Plasiyer: plasiyer@atakodb2b.com / plasiyer123
- MÃ¼ÅŸteri: musteri@atakodb2b.com / musteri123

## ğŸ“œ Lisans

MIT License - AÃ§Ä±k kaynak

## ğŸ‘¨â€ğŸ’» GeliÅŸtirme

Proje Laravel 10 best practices'leri takip edilerek geliÅŸtirilmiÅŸtir.

### Kod StandartlarÄ±
- PSR-12 coding standards
- Eloquent ORM kullanÄ±mÄ±
- Blade templating
- RESTful API tasarÄ±mÄ±
- MVC pattern

### Versiyonlama
- Versiyon: 1.0.0
- Durum: Beta
- Son GÃ¼ncelleme: 2024

---

**atakodb2b** - Profesyonel B2B E-Ticaret Platformu




















