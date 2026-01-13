# atakodb2b Windows Service Kaldırma Script'i
# Yönetici olarak çalıştırın

param(
    [string]$ServiceName = "atakodb2bSyncService"
)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "atakodb2b Windows Service Kaldırma" -ForegroundColor Cyan
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

# Servisi kontrol et
$service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

if (-not $service) {
    Write-Host "Servis bulunamadı: $ServiceName" -ForegroundColor Yellow
    Write-Host "Zaten kaldırılmış olabilir." -ForegroundColor Gray
    exit 0
}

# Servisi durdur
if ($service.Status -eq 'Running') {
    Write-Host "Servis durduruluyor..." -ForegroundColor Yellow
    Stop-Service -Name $ServiceName -Force
    Write-Host "Servis durduruldu." -ForegroundColor Green
    Start-Sleep -Seconds 2
}

# Servisi sil
Write-Host "Servis siliniyor..." -ForegroundColor Yellow
sc.exe delete $ServiceName

if ($LASTEXITCODE -eq 0) {
    Write-Host "Servis başarıyla kaldırıldı." -ForegroundColor Green
} else {
    Write-Host "HATA: Servis silinemedi!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Kaldırma Tamamlandı!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Not: Uygulama dosyaları ve loglar silinmedi." -ForegroundColor Gray
Write-Host "Gerekirse manuel olarak silebilirsiniz." -ForegroundColor Gray







