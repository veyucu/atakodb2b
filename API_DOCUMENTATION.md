# atakodb2b API Dokümantasyonu

## Genel Bilgiler

Bu API, atakodb2b sistemi için kullanıcı ve ürün yönetimi sağlar. Tüm API istekleri JSON formatında olmalıdır.

**Base URL:** `https://yourdomain.com/api`

**Authentication:** Bearer Token (Laravel Sanctum)

**Content-Type:** `application/json`

**Accept:** `application/json`

---

## Authentication (Kimlik Doğrulama)

### 1. Login (Giriş Yap)

Token almak için kullanılır.

**Endpoint:** `POST /api/auth/login`

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123",
    "device_name": "ERP System" // Opsiyonel
}
```

**Response (200 OK):**
```json
{
    "message": "Giriş başarılı",
    "token": "1|abcdefghijklmnopqrstuvwxyz123456789",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "user_type": "admin",
        "musteri_kodu": "M001"
    }
}
```

**Error Response (401 Unauthorized):**
```json
{
    "message": "Girilen bilgiler hatalı.",
    "errors": {
        "email": ["Girilen bilgiler hatalı."]
    }
}
```

---

### 2. Logout (Çıkış Yap)

Mevcut token'ı iptal eder.

**Endpoint:** `POST /api/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "message": "Çıkış başarılı"
}
```

---

### 3. Logout All (Tüm Oturumları Kapat)

Kullanıcının tüm token'larını iptal eder.

**Endpoint:** `POST /api/auth/logout-all`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "message": "Tüm oturumlar sonlandırıldı"
}
```

---

### 4. Me (Kullanıcı Bilgilerini Getir)

Giriş yapmış kullanıcının bilgilerini döndürür.

**Endpoint:** `GET /api/auth/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "user_type": "admin",
        "musteri_kodu": "M001",
        // ... diğer kullanıcı bilgileri
    }
}
```

---

## User Management (Kullanıcı Yönetimi)

### 1. Kullanıcı Listesi

**Endpoint:** `GET /api/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `user_type` (string): admin, plasiyer, musteri
- `is_active` (boolean): true, false
- `musteri_kodu` (string): Müşteri kodu
- `plasiyer_kodu` (string): Plasiyer kodu
- `search` (string): Arama terimi
- `sort_by` (string): Sıralama alanı (default: created_at)
- `sort_order` (string): asc, desc (default: desc)
- `per_page` (integer): Sayfa başına kayıt (default: 15)
- `page` (integer): Sayfa numarası

**Example Request:**
```
GET /api/users?user_type=musteri&is_active=true&per_page=20&page=1
```

**Response (200 OK):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "username": "johndoe",
            "user_type": "musteri",
            "musteri_kodu": "M001",
            "musteri_adi": "John Doe Eczanesi",
            "adres": "İstanbul, Türkiye",
            "ilce": "Kadıköy",
            "il": "İstanbul",
            "gln_numarasi": "1234567890123",
            "telefon": "0555 123 4567",
            "mail_adresi": "john@example.com",
            "vergi_dairesi": "Kadıköy VD",
            "vergi_kimlik_numarasi": "1234567890",
            "grup_kodu": "G01",
            "kod1": null,
            "kod2": null,
            "kod3": null,
            "kod4": null,
            "kod5": null,
            "plasiyer_kodu": "P001",
            "is_active": true,
            "last_login_at": "2024-12-04T10:30:00+00:00",
            "last_login_ip": "192.168.1.1",
            "created_at": "2024-01-01T00:00:00+00:00",
            "updated_at": "2024-12-04T10:30:00+00:00"
        }
    ],
    "links": {
        "first": "http://yourdomain.com/api/users?page=1",
        "last": "http://yourdomain.com/api/users?page=5",
        "prev": null,
        "next": "http://yourdomain.com/api/users?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "per_page": 15,
        "to": 15,
        "total": 75
    }
}
```

---

### 2. Kullanıcı Oluştur

**Endpoint:** `POST /api/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "user_type": "musteri",
    "username": "johndoe",
    "musteri_kodu": "M001",
    "musteri_adi": "John Doe Eczanesi",
    "adres": "İstanbul, Türkiye",
    "ilce": "Kadıköy",
    "il": "İstanbul",
    "gln_numarasi": "1234567890123",
    "telefon": "0555 123 4567",
    "mail_adresi": "john@example.com",
    "vergi_dairesi": "Kadıköy VD",
    "vergi_kimlik_numarasi": "1234567890",
    "grup_kodu": "G01",
    "plasiyer_kodu": "P001",
    "is_active": true
}
```

**Required Fields:**
- `name` (string)
- `email` (string, unique)
- `password` (string, min: 6)
- `user_type` (string: admin, plasiyer, musteri)

**Response (201 Created):**
```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        // ... diğer kullanıcı bilgileri
    }
}
```

---

### 3. Kullanıcı Detayı

**Endpoint:** `GET /api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        // ... tüm kullanıcı bilgileri
    }
}
```

---

### 4. Kullanıcı Güncelle

**Endpoint:** `PUT /api/users/{id}` veya `PATCH /api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "telefon": "0555 999 8888",
    "is_active": false
}
```

**Note:** Sadece güncellemek istediğiniz alanları göndermeniz yeterlidir.

**Response (200 OK):**
```json
{
    "data": {
        "id": 1,
        "name": "John Doe Updated",
        // ... güncellenmiş kullanıcı bilgileri
    }
}
```

---

### 5. Kullanıcı Sil

**Endpoint:** `DELETE /api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "message": "Kullanıcı başarıyla silindi"
}
```

---

### 6. Kullanıcı Koduna Göre Bul

**Endpoint:** `GET /api/users/find-by-code?musteri_kodu={code}`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `musteri_kodu` (string, required): Müşteri kodu

**Response (200 OK):**
```json
{
    "data": {
        "id": 1,
        "musteri_kodu": "M001",
        // ... kullanıcı bilgileri
    }
}
```

**Error Response (404 Not Found):**
```json
{
    "message": "Kullanıcı bulunamadı"
}
```

---

### 7. Toplu Kullanıcı Senkronizasyonu

ERP'den toplu kullanıcı oluşturma veya güncelleme için kullanılır.

**Endpoint:** `POST /api/users/sync`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "users": [
        {
            "musteri_kodu": "M001",
            "name": "John Doe",
            "email": "john@example.com",
            "password": "password123",
            "user_type": "musteri",
            "telefon": "0555 123 4567",
            "is_active": true
        },
        {
            "musteri_kodu": "M002",
            "name": "Jane Smith",
            "email": "jane@example.com",
            "user_type": "musteri",
            "is_active": true
        }
    ]
}
```

**Response (200 OK):**
```json
{
    "message": "Senkronizasyon tamamlandı",
    "created": 5,
    "updated": 10,
    "errors": []
}
```

**Note:** 
- `musteri_kodu` varsa günceller, yoksa yeni oluşturur
- Şifre belirtilmezse varsayılan şifre: `12345678`

---

## Product Management (Ürün Yönetimi)

### 1. Ürün Listesi

**Endpoint:** `GET /api/products`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `is_active` (boolean): true, false
- `ozel_liste` (boolean): true, false
- `marka` (string): Marka adı
- `grup` (string): Grup kodu
- `muadil_kodu` (string): Muadil kodu
- `min_price` (numeric): Minimum fiyat
- `max_price` (numeric): Maximum fiyat
- `in_stock` (boolean): Stokta olanlar (true) veya olmayanlar (false)
- `search` (string): Arama terimi
- `sort_by` (string): Sıralama alanı (default: created_at)
- `sort_order` (string): asc, desc (default: desc)
- `per_page` (integer): Sayfa başına kayıt (default: 15)
- `page` (integer): Sayfa numarası

**Example Request:**
```
GET /api/products?is_active=true&in_stock=true&marka=BAYER&per_page=20
```

**Response (200 OK):**
```json
{
    "data": [
        {
            "id": 1,
            "urun_kodu": "U001",
            "urun_adi": "Aspirin 100mg",
            "barkod": "8690123456789",
            "muadil_kodu": "MU001",
            "satis_fiyati": 100.00,
            "kdv_orani": 18.00,
            "kurum_iskonto": 10.00,
            "eczaci_kari": 5.00,
            "ticari_iskonto": 3.00,
            "mf": "MF",
            "depocu_fiyati": 80.00,
            "net_fiyat_manuel": 85.00,
            "net_fiyat": 82.00,
            "total_discount": 18.00,
            "bakiye": 150.00,
            "marka": "BAYER",
            "grup": "ANALJEZIK",
            "kod1": null,
            "kod2": null,
            "kod3": null,
            "kod4": null,
            "kod5": null,
            "urun_resmi": "products/aspirin.jpg",
            "image_url": "https://yourdomain.com/storage/products/aspirin.jpg",
            "is_active": true,
            "ozel_liste": false,
            "created_at": "2024-01-01T00:00:00+00:00",
            "updated_at": "2024-12-04T10:30:00+00:00"
        }
    ],
    "links": { ... },
    "meta": { ... }
}
```

---

### 2. Ürün Oluştur

**Endpoint:** `POST /api/products`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "urun_kodu": "U001",
    "urun_adi": "Aspirin 100mg",
    "barkod": "8690123456789",
    "muadil_kodu": "MU001",
    "satis_fiyati": 100.00,
    "kdv_orani": 18.00,
    "kurum_iskonto": 10.00,
    "eczaci_kari": 5.00,
    "ticari_iskonto": 3.00,
    "mf": "MF",
    "depocu_fiyati": 80.00,
    "net_fiyat_manuel": 85.00,
    "bakiye": 150.00,
    "marka": "BAYER",
    "grup": "ANALJEZIK",
    "urun_resmi": "products/aspirin.jpg",
    "is_active": true,
    "ozel_liste": false
}
```

**Required Fields:**
- `urun_kodu` (string, unique)
- `urun_adi` (string)
- `satis_fiyati` (numeric, min: 0)

**Response (201 Created):**
```json
{
    "data": {
        "id": 1,
        "urun_kodu": "U001",
        // ... tüm ürün bilgileri
    }
}
```

---

### 3. Ürün Detayı

**Endpoint:** `GET /api/products/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "data": {
        "id": 1,
        "urun_kodu": "U001",
        // ... tüm ürün bilgileri
    }
}
```

---

### 4. Ürün Güncelle

**Endpoint:** `PUT /api/products/{id}` veya `PATCH /api/products/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "satis_fiyati": 110.00,
    "bakiye": 200.00,
    "is_active": true
}
```

**Response (200 OK):**
```json
{
    "data": {
        "id": 1,
        // ... güncellenmiş ürün bilgileri
    }
}
```

---

### 5. Ürün Sil

**Endpoint:** `DELETE /api/products/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "message": "Ürün başarıyla silindi"
}
```

---

### 6. Ürün Koduna Göre Bul

**Endpoint:** `GET /api/products/find-by-code?urun_kodu={code}`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `urun_kodu` (string, required): Ürün kodu

**Response (200 OK):**
```json
{
    "data": {
        "id": 1,
        "urun_kodu": "U001",
        // ... ürün bilgileri
    }
}
```

---

### 7. Barkoda Göre Bul

**Endpoint:** `GET /api/products/find-by-barcode?barkod={barcode}`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `barkod` (string, required): Barkod

**Response (200 OK):**
```json
{
    "data": {
        "id": 1,
        "barkod": "8690123456789",
        // ... ürün bilgileri
    }
}
```

---

### 8. Muadil Ürünleri Getir

**Endpoint:** `GET /api/products/{id}/equivalents`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "data": [
        {
            "id": 2,
            "urun_kodu": "U002",
            "urun_adi": "Generic Aspirin 100mg",
            "muadil_kodu": "MU001",
            // ... ürün bilgileri
        }
    ]
}
```

---

### 9. Stok Güncelle

**Endpoint:** `PATCH /api/products/{id}/stock`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "bakiye": 250.00
}
```

**Response (200 OK):**
```json
{
    "data": {
        "id": 1,
        "bakiye": 250.00,
        // ... ürün bilgileri
    }
}
```

---

### 10. Fiyat Güncelle

**Endpoint:** `PATCH /api/products/{id}/price`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "satis_fiyati": 110.00,
    "kdv_orani": 18.00,
    "kurum_iskonto": 12.00,
    "eczaci_kari": 5.00,
    "ticari_iskonto": 3.00
}
```

**Response (200 OK):**
```json
{
    "data": {
        "id": 1,
        "satis_fiyati": 110.00,
        "net_fiyat": 90.20,
        // ... ürün bilgileri
    }
}
```

---

### 11. Toplu Ürün Senkronizasyonu

ERP'den toplu ürün oluşturma veya güncelleme için kullanılır.

**Endpoint:** `POST /api/products/sync`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "products": [
        {
            "urun_kodu": "U001",
            "urun_adi": "Aspirin 100mg",
            "satis_fiyati": 100.00,
            "barkod": "8690123456789",
            "bakiye": 150.00,
            "marka": "BAYER",
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
}
```

**Response (200 OK):**
```json
{
    "message": "Senkronizasyon tamamlandı",
    "created": 50,
    "updated": 100,
    "errors": []
}
```

**Note:** `urun_kodu` varsa günceller, yoksa yeni oluşturur.

---

## Error Responses (Hata Yanıtları)

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "message": "Hesabınız aktif değil."
}
```

### 404 Not Found
```json
{
    "message": "Kayıt bulunamadı"
}
```

### 422 Validation Error
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "Bu e-posta adresi zaten kullanılıyor"
        ],
        "satis_fiyati": [
            "Satış fiyatı 0'dan küçük olamaz"
        ]
    }
}
```

### 500 Internal Server Error
```json
{
    "message": "Server Error"
}
```

---

## Kullanım Örnekleri

### cURL ile Örnek İstekler

#### 1. Login
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

#### 2. Kullanıcı Listesi
```bash
curl -X GET "https://yourdomain.com/api/users?user_type=musteri&per_page=20" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

#### 3. Kullanıcı Oluştur
```bash
curl -X POST https://yourdomain.com/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "user_type": "musteri",
    "musteri_kodu": "M001"
  }'
```

#### 4. Ürün Senkronizasyonu
```bash
curl -X POST https://yourdomain.com/api/products/sync \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "products": [
      {
        "urun_kodu": "U001",
        "urun_adi": "Aspirin 100mg",
        "satis_fiyati": 100.00,
        "bakiye": 150.00
      }
    ]
  }'
```

---

## Rate Limiting

API istekleri rate limiting'e tabidir:
- **60 istek/dakika** authenticated kullanıcılar için

Rate limit aşıldığında:
```json
{
    "message": "Too Many Attempts."
}
```

**Response Headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
Retry-After: 60
```

---

## Notlar

1. Tüm tarih/saat değerleri ISO 8601 formatındadır (UTC)
2. Tüm fiyat değerleri decimal(10,2) formatındadır
3. Boolean değerler: `true`, `false`, `1`, `0` olarak gönderilebilir
4. Sayfalama otomatik olarak yapılır, `per_page` parametresi ile kontrol edilebilir
5. Token'lar güvenli bir şekilde saklanmalıdır
6. API isteklerinde HTTPS kullanılması önerilir

---

## Destek

Sorularınız için: info@atakodb2b.com







