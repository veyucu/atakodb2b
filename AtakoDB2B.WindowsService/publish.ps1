# atakodb2b Windows Service Publish Script'i
# Projeyi derler ve yayınlar

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "atakodb2b Windows Service Publish" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Proje dizinini kontrol et
if (-not (Test-Path "AtakoDB2B.WindowsService.csproj")) {
    Write-Host "HATA: AtakoDB2B.WindowsService.csproj bulunamadı!" -ForegroundColor Red
    Write-Host "Bu script'i proje dizininde çalıştırın." -ForegroundColor Yellow
    exit 1
}

# Temizlik
Write-Host "Eski build dosyaları temizleniyor..." -ForegroundColor Yellow
if (Test-Path "bin") {
    Remove-Item -Path "bin" -Recurse -Force
}
if (Test-Path "obj") {
    Remove-Item -Path "obj" -Recurse -Force
}
Write-Host "Temizlik tamamlandı." -ForegroundColor Green
Write-Host ""

# Restore
Write-Host "NuGet paketleri yükleniyor..." -ForegroundColor Yellow
dotnet restore
if ($LASTEXITCODE -ne 0) {
    Write-Host "HATA: NuGet restore başarısız!" -ForegroundColor Red
    exit 1
}
Write-Host "NuGet paketleri yüklendi." -ForegroundColor Green
Write-Host ""

# Build
Write-Host "Proje derleniyor..." -ForegroundColor Yellow
dotnet build -c Release
if ($LASTEXITCODE -ne 0) {
    Write-Host "HATA: Build başarısız!" -ForegroundColor Red
    exit 1
}
Write-Host "Derleme tamamlandı." -ForegroundColor Green
Write-Host ""

# Publish
Write-Host "Proje yayınlanıyor (self-contained, win-x64)..." -ForegroundColor Yellow
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true
if ($LASTEXITCODE -ne 0) {
    Write-Host "HATA: Publish başarısız!" -ForegroundColor Red
    exit 1
}
Write-Host "Yayınlama tamamlandı." -ForegroundColor Green
Write-Host ""

$publishPath = "bin\Release\net6.0\win-x64\publish"
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Publish Başarılı!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Yayınlanan dosyalar: $publishPath" -ForegroundColor Cyan
Write-Host ""
Write-Host "Kurulum için:" -ForegroundColor Yellow
Write-Host "  1. appsettings.json dosyasını düzenleyin (Netsis ve API ayarları)" -ForegroundColor Cyan
Write-Host "  2. install-service.ps1 script'ini yönetici olarak çalıştırın" -ForegroundColor Cyan
Write-Host ""


