# Database backup dosyasini FTP'ye yukle

$ftpHost = "ftp.caygetir.net"
$ftpUser = "atakodb2b@atakod.tr"
$ftpPass = "Konyaspor56"
$localFile = "C:\xampp\htdocs\atakodb2b\atakodb2b_backup.sql"
$remoteFile = "/atakodb2b_backup.sql"

Write-Host "Database backup dosyasi sunucuya yukleniyor..." -ForegroundColor Cyan

try {
    $ftpUri = "ftp://$ftpHost$remoteFile"
    $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.UseBinary = $true
    $ftpRequest.UsePassive = $true
    
    $content = [System.IO.File]::ReadAllBytes($localFile)
    $ftpRequest.ContentLength = $content.Length
    
    $requestStream = $ftpRequest.GetRequestStream()
    $requestStream.Write($content, 0, $content.Length)
    $requestStream.Close()
    
    $response = $ftpRequest.GetResponse()
    Write-Host "Database backup basariyla yuklendi!" -ForegroundColor Green
    Write-Host "Dosya: $remoteFile" -ForegroundColor Yellow
    $response.Close()
} catch {
    Write-Host "HATA: $($_.Exception.Message)" -ForegroundColor Red
}
