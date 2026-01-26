---
description: Deploy files to production via FTP (doganarib2b.com)
---
# FTP Deployment Workflow

## FTP Bilgileri
- **Host:** ftp.caygetir.net
- **User:** doganarib2b@doganarib2b.com
- **Password:** Konya1923*

## Tek Dosya Yükleme
// turbo
```powershell
curl.exe --upload-file "DOSYA_YOLU" --user "doganarib2b@doganarib2b.com:Konya1923*" "ftp://ftp.caygetir.net/HEDEF_YOL"
```

## Örnek: Controller Yükleme
```powershell
curl.exe --upload-file "app/Http/Controllers/CONTROLLER.php" --user "doganarib2b@doganarib2b.com:Konya1923*" "ftp://ftp.caygetir.net/app/Http/Controllers/CONTROLLER.php"
```

## Örnek: View Yükleme
```powershell
curl.exe --upload-file "resources/views/DOSYA.blade.php" --user "doganarib2b@doganarib2b.com:Konya1923*" "ftp://ftp.caygetir.net/resources/views/DOSYA.blade.php"
```
