# FTP Upload Script
# Laravel projesini hosting'e yukler

$ftpHost = "ftp.caygetir.net"
$ftpUser = "atakodb2b@atakod.tr"
$ftpPass = "Konyaspor56"
$remotePath = "/"
$localPath = "C:\xampp\htdocs\atakodb2b"

# Yuklenecek dosya ve klasorler (vendor, node_modules ve .git haric)
$excludeDirs = @(".git", "node_modules", "vendor", "storage\framework\cache\data", "storage\framework\sessions", "storage\framework\views", "storage\logs")

# FTP baglantisi olustur
$ftpUri = "ftp://$ftpHost"

# Recursive upload fonksiyonu
function Upload-FtpDirectory {
    param(
        [string]$LocalDir,
        [string]$RemoteDir
    )
    
    Write-Host "Yukleniyor: $LocalDir -> $RemoteDir"
    
    # Oncelikle klasoru olustur
    try {
        $ftpRequest = [System.Net.FtpWebRequest]::Create("$ftpUri$RemoteDir")
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.UseBinary = $true
        $ftpRequest.UsePassive = $true
        $response = $ftpRequest.GetResponse()
        $response.Close()
        Write-Host "Klasor olusturuldu: $RemoteDir"
    } catch {
        # Klasor zaten var olabilir, devam et
    }
    
    # Dosyalari yukle
    Get-ChildItem -Path $LocalDir -File | ForEach-Object {
        $fileName = $_.Name
        $localFile = $_.FullName
        $remoteFile = "$RemoteDir/$fileName"
        
        Write-Host "  Dosya: $fileName"
        
        try {
            $ftpRequest = [System.Net.FtpWebRequest]::Create("$ftpUri$remoteFile")
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
            $response.Close()
        } catch {
            Write-Host "  HATA: $($_.Exception.Message)" -ForegroundColor Red
        }
    }
    
    # Alt klasorleri yukle
    Get-ChildItem -Path $LocalDir -Directory | ForEach-Object {
        $dirName = $_.Name
        $localSubDir = $_.FullName
        $remoteSubDir = "$RemoteDir/$dirName"
        
        # Exclude listesinde var mi kontrol et
        $relativePath = $localSubDir.Replace($localPath, "").TrimStart("\")
        $shouldExclude = $false
        foreach ($exclude in $excludeDirs) {
            if ($relativePath -like "$exclude*" -or $relativePath -eq $exclude) {
                $shouldExclude = $true
                break
            }
        }
        
        if (-not $shouldExclude) {
            Upload-FtpDirectory -LocalDir $localSubDir -RemoteDir $remoteSubDir
        } else {
            Write-Host "  Atlaniyor: $dirName" -ForegroundColor Yellow
        }
    }
}

Write-Host "FTP Yukleme Basladi..." -ForegroundColor Green
Write-Host "Hedef: $ftpHost$remotePath"
Write-Host ""

# Yuklemeyi baslat
Upload-FtpDirectory -LocalDir $localPath -RemoteDir $remotePath

Write-Host ""
Write-Host "Yukleme Tamamlandi!" -ForegroundColor Green
