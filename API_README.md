# atakodb2b ERP API Entegrasyonu

Bu proje, ERP sistemlerinden kullanıcı ve ürün verilerini senkronize etmek için RESTful API servisleri sağlar.

## 🚀 Özellikler

### Kullanıcı Yönetimi
- ✅ Kullanıcı listeleme (filtreleme, arama, sayfalama)
- ✅ Yeni kullanıcı oluşturma
- ✅ Kullanıcı güncelleme
- ✅ Kullanıcı silme
- ✅ Müşteri koduna göre kullanıcı bulma
- ✅ Toplu kullanıcı senkronizasyonu (Sync)

### Ürün Yönetimi
- ✅ Ürün listeleme (filtreleme, arama, sayfalama)
- ✅ Yeni ürün oluşturma
- ✅ Ürün güncelleme
- ✅ Ürün silme
- ✅ Ürün koduna göre ürün bulma
- ✅ Barkoda göre ürün bulma
- ✅ Muadil ürünleri listeleme
- ✅ Stok güncelleme
- ✅ Fiyat güncelleme
- ✅ Toplu ürün senkronizasyonu (Sync)

### Güvenlik
- 🔒 Laravel Sanctum ile token-based authentication
- 🔒 Rate limiting (60 istek/dakika)
- 🔒 Validation ve error handling
- 🔒 HTTPS desteği

## 📋 Gereksinimler

- PHP >= 8.1
- Laravel >= 10.x
- MySQL/MariaDB
- Composer

## 🔧 Kurulum

### 1. Sanctum Migration'ı Çalıştır

```bash
php artisan migrate
```

### 2. API Token Oluşturma

İlk admin kullanıcısı için token oluşturmak üzere tinker kullanabilirsiniz:

```bash
php artisan tinker
```

```php
$user = User::where('email', 'admin@example.com')->first();
$token = $user->createToken('ERP System')->plainTextToken;
echo $token;
```

## 📖 Kullanım

### Hızlı Başlangıç

#### 1. Login ve Token Alma

```bash
curl -X POST https://yourdomain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123",
    "device_name": "ERP System"
  }'
```

**Response:**
```json
{
    "message": "Giriş başarılı",
    "token": "1|abcdefghijklmnopqrstuvwxyz123456789",
    "user": { ... }
}
```

#### 2. API İstekleri

Token'ı aldıktan sonra, tüm isteklerde `Authorization` header'ı ile kullanın:

```bash
curl -X GET https://yourdomain.com/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Toplu Senkronizasyon Örnekleri

#### Kullanıcı Senkronizasyonu

```bash
curl -X POST https://yourdomain.com/api/users/sync \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "users": [
      {
        "musteri_kodu": "M001",
        "name": "Eczane A",
        "email": "eczanea@example.com",
        "user_type": "musteri",
        "telefon": "0555 123 4567",
        "il": "İstanbul",
        "is_active": true
      },
      {
        "musteri_kodu": "M002",
        "name": "Eczane B",
        "email": "eczaneb@example.com",
        "user_type": "musteri",
        "is_active": true
      }
    ]
  }'
```

#### Ürün Senkronizasyonu

```bash
curl -X POST https://yourdomain.com/api/products/sync \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "products": [
      {
        "urun_kodu": "U001",
        "urun_adi": "Aspirin 100mg",
        "satis_fiyati": 100.00,
        "barkod": "8690123456789",
        "bakiye": 150.00,
        "marka": "BAYER",
        "kdv_orani": 18.00,
        "is_active": true
      },
      {
        "urun_kodu": "U002",
        "urun_adi": "Paracetamol 500mg",
        "satis_fiyati": 50.00,
        "bakiye": 200.00,
        "is_active": true
      }
    ]
  }'
```

**Response:**
```json
{
    "message": "Senkronizasyon tamamlandı",
    "created": 50,
    "updated": 100,
    "errors": []
}
```

## 📚 Dokümantasyon

Detaylı API dokümantasyonu için: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

## 🔌 Postman Collection

Postman ile test etmek için hazır collection dosyası: [atakodb2b_API.postman_collection.json](atakodb2b_API.postman_collection.json)

### Postman Collection Kullanımı

1. Postman'i açın
2. `Import` butonuna tıklayın
3. `atakodb2b_API.postman_collection.json` dosyasını seçin
4. Collection içindeki `Variables` sekmesinden:
   - `base_url`: API'nizin base URL'ini girin (örn: `https://yourdomain.com/api`)
   - `api_token`: Login yaptıktan sonra otomatik olarak doldurulacak

## 🎯 API Endpoints Özeti

### Authentication
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout
- `POST /api/auth/logout-all` - Logout All
- `GET /api/auth/me` - Get User Info

### Users
- `GET /api/users` - List Users
- `POST /api/users` - Create User
- `GET /api/users/{id}` - Get User
- `PUT /api/users/{id}` - Update User
- `DELETE /api/users/{id}` - Delete User
- `GET /api/users/find-by-code` - Find by Code
- `POST /api/users/sync` - Sync Users

### Products
- `GET /api/products` - List Products
- `POST /api/products` - Create Product
- `GET /api/products/{id}` - Get Product
- `PUT /api/products/{id}` - Update Product
- `DELETE /api/products/{id}` - Delete Product
- `GET /api/products/find-by-code` - Find by Code
- `GET /api/products/find-by-barcode` - Find by Barcode
- `GET /api/products/{id}/equivalents` - Get Equivalents
- `PATCH /api/products/{id}/stock` - Update Stock
- `PATCH /api/products/{id}/price` - Update Price
- `POST /api/products/sync` - Sync Products

## 🔐 Güvenlik Notları

1. **Token Güvenliği**: API token'larını güvenli bir şekilde saklayın
2. **HTTPS**: Üretim ortamında mutlaka HTTPS kullanın
3. **Rate Limiting**: API istekleri dakikada 60 ile sınırlıdır
4. **Validation**: Tüm girdiler validation'dan geçer
5. **Password Hashing**: Şifreler otomatik olarak hash'lenir

## ⚙️ Yapılandırma

### .env Ayarları

```env
# API Rate Limiting
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
SESSION_DOMAIN=.yourdomain.com
```

### CORS Ayarları

`config/cors.php` dosyasında gerekli ayarlamaları yapın:

```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

## 🐛 Hata Ayıklama

### Yaygın Hatalar

#### 401 Unauthorized
- Token'ın doğru gönderildiğinden emin olun
- Token'ın geçerli olduğunu kontrol edin
- `Authorization: Bearer TOKEN` formatını kullanın

#### 422 Validation Error
- Gönderilen verilerin doğru formatta olduğundan emin olun
- Required alanların eksik olmadığını kontrol edin

#### 429 Too Many Requests
- Rate limit aşıldı, 60 saniye bekleyin

### Log Kontrolü

```bash
tail -f storage/logs/laravel.log
```

## 📊 Performans İpuçları

1. **Toplu İşlemler**: Tek tek istek yerine `sync` endpoint'lerini kullanın
2. **Sayfalama**: Büyük listelerde `per_page` parametresini kullanın
3. **Filtreleme**: Gereksiz veri transferini önlemek için filtreleme kullanın
4. **Caching**: Sık kullanılan verileri cache'leyin

## 🧪 Test

### API Testleri

```bash
php artisan test --filter Api
```

### Manuel Test

Postman collection'ı kullanarak manuel test yapabilirsiniz.

## 📞 Destek

Sorularınız veya sorunlarınız için:
- Email: info@atakodb2b.com
- GitHub Issues: [github.com/yourrepo/issues](https://github.com/yourrepo/issues)

## 📝 Lisans

Bu proje özel lisans altındadır.

## 🔄 Versiyon Geçmişi

### v1.0.0 (2024-12-04)
- ✅ İlk API sürümü
- ✅ Kullanıcı yönetimi endpoints
- ✅ Ürün yönetimi endpoints
- ✅ Toplu senkronizasyon desteği
- ✅ Laravel Sanctum authentication
- ✅ API dokümantasyonu
- ✅ Postman collection

---

**Not:** Bu API sürekli geliştirilmektedir. Güncellemeler için dokümantasyonu takip edin.







