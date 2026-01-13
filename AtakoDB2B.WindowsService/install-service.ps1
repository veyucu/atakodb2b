# atakodb2b Windows Service Kurulum Script'i
# Yönetici olarak çalıştırın

param(
    [string]$ServicePath = "$PSScriptRoot\bin\Release\net6.0\win-x64\publish\AtakoDB2B.WindowsService.exe",
    [string]$ServiceName = "atakodb2bSyncService",
    [string]$ServiceDisplayName = "atakodb2b Sync Service",
    [string]$ServiceDescription = "Netsis ERP'den atakodb2b'ye veri senkronizasyonu yapan Windows servisi"
)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "atakodb2b Windows Service Kurulum" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Yönetici kontrolü
$currentPrincipal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
$isAdmin = $currentPrincipal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "HATA: Bu script yönetici olarak çalıştırılmalıdır!" -ForegroundColor Red
    Write-Host "PowerShell'i 'Yönetici olarak çalıştır' seçeneği ile açın." -ForegroundColor Yellow
    exit 1
}

# Servis dosyasının varlığını kontrol et
if (-not (Test-Path $ServicePath)) {
    Write-Host "HATA: Servis dosyası bulunamadı: $ServicePath" -ForegroundColor Red
    Write-Host ""
    Write-Host "Lütfen önce projeyi publish edin:" -ForegroundColor Yellow
    Write-Host "  dotnet publish -c Release -r win-x64 --self-contained true" -ForegroundColor Cyan
    exit 1
}

# Mevcut servisi kontrol et ve durdur
$existingService = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
if ($existingService) {
    Write-Host "Mevcut servis bulundu. Durduruluyor..." -ForegroundColor Yellow
    
    if ($existingService.Status -eq 'Running') {
        Stop-Service -Name $ServiceName -Force
        Write-Host "Servis durduruldu." -ForegroundColor Green
    }
    
    Write-Host "Mevcut servis siliniyor..." -ForegroundColor Yellow
    sc.exe delete $ServiceName
    Start-Sleep -Seconds 2
    Write-Host "Mevcut servis silindi." -ForegroundColor Green
    Write-Host ""
}

# Yeni servisi yükle
Write-Host "Yeni servis yükleniyor..." -ForegroundColor Yellow
sc.exe create $ServiceName binPath= $ServicePath start= auto DisplayName= $ServiceDisplayName

if ($LASTEXITCODE -eq 0) {
    Write-Host "Servis başarıyla oluşturuldu." -ForegroundColor Green
    
    # Servis açıklaması ekle
    sc.exe description $ServiceName $ServiceDescription
    
    # Servisi başlat
    Write-Host ""
    Write-Host "Servis başlatılıyor..." -ForegroundColor Yellow
    Start-Service -Name $ServiceName
    
    Start-Sleep -Seconds 2
    
    $service = Get-Service -Name $ServiceName
    if ($service.Status -eq 'Running') {
        Write-Host "Servis başarıyla başlatıldı!" -ForegroundColor Green
    } else {
        Write-Host "UYARI: Servis başlatılamadı. Durum: $($service.Status)" -ForegroundColor Yellow
        Write-Host "Logları kontrol edin: $PSScriptRoot\logs\" -ForegroundColor Cyan
    }
} else {
    Write-Host "HATA: Servis oluşturulamadı!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Kurulum Tamamlandı!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Servis Yönetimi:" -ForegroundColor Yellow
Write-Host "  Servisi Durdur:   Stop-Service -Name $ServiceName" -ForegroundColor Cyan
Write-Host "  Servisi Başlat:   Start-Service -Name $ServiceName" -ForegroundColor Cyan
Write-Host "  Servis Durumu:    Get-Service -Name $ServiceName" -ForegroundColor Cyan
Write-Host "  Logları Görüntüle: Get-Content '$PSScriptRoot\logs\atakodb2b-service-*.txt' -Tail 50" -ForegroundColor Cyan
Write-Host ""
Write-Host "Services.msc ile servisi yönetebilirsiniz." -ForegroundColor Gray


