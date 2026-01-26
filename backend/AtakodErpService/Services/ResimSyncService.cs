using System.Text;
using System.Text.Json;
using AtakoErpService.Models;

namespace AtakoErpService.Services;

public class ResimSyncService
{
    private readonly IDatabaseService _db;
    private readonly HttpClient _httpClient;
    private readonly IConfiguration _config;
    private readonly ILogger<ResimSyncService> _logger;

    public ResimSyncService(
        IDatabaseService db,
        HttpClient httpClient,
        IConfiguration config,
        ILogger<ResimSyncService> logger)
    {
        _db = db;
        _httpClient = httpClient;
        _config = config;
        _logger = logger;
    }

    /// <summary>
    /// ATV_B2BRESIM view'inden bekleyen resimleri getirir
    /// </summary>
    public async Task<IEnumerable<ResimDto>> GetPendingResimAsync()
    {
        _logger.LogInformation("Bekleyen resimler getiriliyor...");
        
        var topLimit = _config.GetValue<int>("SyncSettings:TopLimit", 0);
        var topClause = topLimit > 0 ? $"TOP {topLimit}" : "";
        
        var sql = $@"
            SELECT {topClause}
                STOK_KODU,
                RESIM,
                ISLEM
            FROM ATV_B2BRESIM
            WHERE ISLEM IN ('I', 'U')";

        var result = await _db.QueryAsync<ResimDto>(sql);
        var resultList = result.ToList();
        
        _logger.LogInformation("Bekleyen resim sayısı: {Count}", resultList.Count);
        return resultList;
    }

    /// <summary>
    /// Resimleri Laravel'e senkronize eder
    /// Sadece mevcut ürünlerin resmini günceller, yeni ürün oluşturmaz
    /// </summary>
    public async Task<ResimSyncResult> SyncToLaravelAsync()
    {
        var result = new ResimSyncResult();
        
        try
        {
            var pendingResimler = (await GetPendingResimAsync()).ToList();
            
            _logger.LogInformation("Toplam {Count} resim senkronize edilecek", pendingResimler.Count);
            
            foreach (var resim in pendingResimler)
            {
                try
                {
                    if (string.IsNullOrEmpty(resim.STOK_KODU))
                    {
                        result.SkippedCount++;
                        continue;
                    }
                    
                    if (resim.RESIM == null || resim.RESIM.Length == 0)
                    {
                        result.SkippedCount++;
                        _logger.LogWarning("Resim verisi boş: {StokKodu}", resim.STOK_KODU);
                        continue;
                    }
                    
                    // Laravel'e gönder (multipart/form-data olarak)
                    var (success, errorMessage) = await SendImageToLaravelAsync(resim.STOK_KODU, resim.RESIM);
                    
                    if (success)
                    {
                        // Log tablosuna yaz
                        await LogResimOperationAsync(resim);
                        
                        result.UpdatedCount++;
                        _logger.LogInformation("Resim güncellendi: {StokKodu}", resim.STOK_KODU);
                    }
                    else if (errorMessage?.Contains("Ürün bulunamadı") == true || 
                             errorMessage?.Contains("404") == true)
                    {
                        result.SkippedCount++;
                        _logger.LogWarning("Ürün bulunamadı: {StokKodu}", resim.STOK_KODU);
                    }
                    else
                    {
                        result.ErrorCount++;
                        result.Errors.Add($"{resim.STOK_KODU}: {errorMessage}");
                        _logger.LogWarning("Gönderilemedi: {StokKodu} - {Hata}", resim.STOK_KODU, errorMessage);
                    }
                }
                catch (Exception ex)
                {
                    result.ErrorCount++;
                    result.Errors.Add($"{resim.STOK_KODU}: {ex.Message}");
                    _logger.LogError(ex, "Resim işlemi hatası: {StokKodu}", resim.STOK_KODU);
                }
            }
            
            result.Success = result.ErrorCount == 0;
            _logger.LogInformation(
                "Resim senkronizasyonu tamamlandı. Güncellenen: {Updated}, Atlanan: {Skipped}, Hata: {Error}",
                result.UpdatedCount, result.SkippedCount, result.ErrorCount);
        }
        catch (Exception ex)
        {
            result.Success = false;
            result.Errors.Add($"Genel hata: {ex.Message}");
            _logger.LogError(ex, "Resim senkronizasyon genel hatası");
        }
        
        return result;
    }

    /// <summary>
    /// AKTBL_RESIMLOG tablosuna log yazar
    /// </summary>
    public async Task LogResimOperationAsync(ResimDto resim)
    {
        try
        {
            // Önce varsa sil
            var deleteSql = "DELETE FROM AKTBL_B2BRESIMLOG WHERE STOK_KODU = @STOK_KODU";
            await _db.ExecuteAsync(deleteSql, new { resim.STOK_KODU });
            
            // Sonra ekle
            var insertSql = @"
                INSERT INTO AKTBL_B2BRESIMLOG (STOK_KODU, RESIM, TARIH)
                VALUES (@STOK_KODU, @RESIM, GETDATE())";

            await _db.ExecuteAsync(insertSql, new
            {
                resim.STOK_KODU,
                resim.RESIM
            });
            
            _logger.LogDebug("Resim log kaydı yazıldı: {StokKodu}", resim.STOK_KODU);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Resim log yazma hatası: {StokKodu}", resim.STOK_KODU);
            throw;
        }
    }

    /// <summary>
    /// Laravel'e resim gönderir (multipart/form-data)
    /// </summary>
    private async Task<(bool Success, string? ErrorMessage)> SendImageToLaravelAsync(string stokKodu, byte[] imageData)
    {
        var laravelUrl = _config["LaravelApi:BaseUrl"] ?? "http://localhost:8000";
        var apiKey = _config["LaravelApi:ApiKey"] ?? "";
        
        var endpoint = $"{laravelUrl}/api/erp/products/{stokKodu}/image";
        
        try
        {
            using var content = new MultipartFormDataContent();
            
            // Resim dosyası olarak ekle
            var imageContent = new ByteArrayContent(imageData);
            imageContent.Headers.ContentType = new System.Net.Http.Headers.MediaTypeHeaderValue("image/jpeg");
            content.Add(imageContent, "image", $"{stokKodu}.jpg");
            
            using var request = new HttpRequestMessage(HttpMethod.Post, endpoint);
            request.Content = content;
            request.Headers.Add("X-API-Key", apiKey);
            
            var response = await _httpClient.SendAsync(request);
            
            if (!response.IsSuccessStatusCode)
            {
                var responseContent = await response.Content.ReadAsStringAsync();
                _logger.LogWarning(
                    "Laravel API hatası: {StatusCode} - {Content}", 
                    response.StatusCode, 
                    responseContent);
                return (false, $"HTTP {(int)response.StatusCode}: {responseContent}");
            }
            
            return (true, null);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Laravel API iletişim hatası: {Endpoint}", endpoint);
            return (false, ex.Message);
        }
    }
}
