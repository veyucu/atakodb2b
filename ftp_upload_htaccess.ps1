# .htaccess dosyasini FTP'ye yukle

$ftpHost = "ftp.caygetir.net"
$ftpUser = "atakodb2b@atakod.tr"
$ftpPass = "Konyaspor56"
$localFile = "C:\xampp\htdocs\atakodb2b\public\.htaccess"
$remoteFile = "/public/.htaccess"

Write-Host ".htaccess dosyasi sunucuya yukleniyor..." -ForegroundColor Cyan

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
    Write-Host ".htaccess dosyasi basariyla yuklendi!" -ForegroundColor Green
    $response.Close()
} catch {
    Write-Host "HATA: $($_.Exception.Message)" -ForegroundColor Red
}
