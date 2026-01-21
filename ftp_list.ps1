# FTP List Script - Mevcut dosyalari listele

$ftpHost = "ftp.caygetir.net"
$ftpUser = "atakodb2b@atakod.tr"
$ftpPass = "Konyaspor56"
$remotePath = "/"

Write-Host "FTP'deki mevcut dosyalar listeleniyor..." -ForegroundColor Cyan
Write-Host "Host: $ftpHost"
Write-Host "Path: $remotePath"
Write-Host ""

try {
    $ftpUri = "ftp://$ftpHost$remotePath"
    $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.UseBinary = $true
    $ftpRequest.UsePassive = $true
    
    $response = $ftpRequest.GetResponse()
    $stream = $response.GetResponseStream()
    $reader = New-Object System.IO.StreamReader($stream)
    
    $content = $reader.ReadToEnd()
    
    Write-Host "=== MEVCUT DOSYALAR ===" -ForegroundColor Green
    Write-Host $content
    Write-Host "=======================" -ForegroundColor Green
    
    $reader.Close()
    $stream.Close()
    $response.Close()
} catch {
    Write-Host "HATA: $($_.Exception.Message)" -ForegroundColor Red
}
