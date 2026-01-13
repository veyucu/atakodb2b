#!/bin/bash

# atakodb2b - Dosya İzinleri Düzeltme Scripti

echo "🔧 Dosya izinleri düzeltiliyor..."

# Ana dizin
cd /path/to/your/project

# Tüm klasörleri 755 yap
find . -type d -exec chmod 755 {} \;

# Tüm dosyaları 644 yap
find . -type f -exec chmod 644 {} \;

# Özel izinler
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# .env dosyası
chmod 644 .env

# Artisan çalıştırılabilir
chmod +x artisan

# Public klasörü
chmod -R 755 public

echo "✅ İzinler düzeltildi!"
echo ""
echo "Kontrol edin:"
echo "- Ana sayfa: https://yourdomain.com"
echo "- Storage: https://yourdomain.com/storage/"
















