# FTP ile .env dosyasini indir

$ftpHost = "ftp.caygetir.net"
$ftpUser = "atakodb2b@atakod.tr"
$ftpPass = "Konyaspor56"
$remoteFile = "/.env"
$localFile = "C:\xampp\htdocs\atakodb2b\server_env_backup.txt"

Write-Host "Sunucudaki .env dosyasi indiriliyor..." -ForegroundColor Cyan

try {
    $ftpUri = "ftp://$ftpHost$remoteFile"
    $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::DownloadFile
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.UseBinary = $true
    $ftpRequest.UsePassive = $true
    
    $response = $ftpRequest.GetResponse()
    $stream = $response.GetResponseStream()
    $reader = New-Object System.IO.StreamReader($stream)
    
    $content = $reader.ReadToEnd()
    
    Write-Host "=== SUNUCUDAKI .ENV DOSYASI ===" -ForegroundColor Green
    Write-Host $content
    Write-Host "===============================" -ForegroundColor Green
    
    # Dosyayi kaydet
    $content | Out-File -FilePath $localFile -Encoding UTF8
    Write-Host "Dosya kaydedildi: $localFile" -ForegroundColor Yellow
    
    $reader.Close()
    $stream.Close()
    $response.Close()
} catch {
    Write-Host "HATA: $($_.Exception.Message)" -ForegroundColor Red
}
