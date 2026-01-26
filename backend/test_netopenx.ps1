# Netsis NetOpenX - Sirket Baglanti Testi
# Credentials from appsettings.json

$vtAdi = "MUHASEBE2026"
$vtKulAdi = "TEMELSET"
$vtKulSifre = ""
$netKul = "NETSIS"
$netSifre = "NET5"
$subeKod = 0

try {
    Write-Host "Kernel olusturuluyor..."
    $type = [System.Type]::GetTypeFromCLSID([Guid]'65EB3876-89FF-459F-BF24-02E8DD7F2DB2')
    $kernel = [Activator]::CreateInstance($type)
    Write-Host "Kernel olusturuldu!" -ForegroundColor Green
    
    Write-Host ""
    Write-Host "Sirkete baglaniliyor..."
    Write-Host "  VT: $vtAdi"
    Write-Host "  VT Kullanici: $vtKulAdi"
    Write-Host "  Netsis Kullanici: $netKul"
    Write-Host "  Sube Kodu: $subeKod"
    
    # TVTTipi.vtMSSQL = 0
    $sirket = $kernel.yeniSirket(0, $vtAdi, $vtKulAdi, $vtKulSifre, $netKul, $netSifre, $subeKod)
    
    if ($null -ne $sirket) {
        Write-Host ""
        Write-Host "SIRKET BAGLANTISI BASARILI!" -ForegroundColor Green
        Write-Host "Sirket objesi: $($sirket.GetType().Name)"
    } else {
        Write-Host "HATA: Sirket objesi null!" -ForegroundColor Red
    }
    
} catch {
    Write-Host ""
    Write-Host "HATA: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Stack: $($_.Exception.StackTrace)" -ForegroundColor Yellow
}
