using System.Text;
using System.Text.Json;
using AtakoErpService.Models;

namespace AtakoErpService.Services;

public class BakiyeSyncService
{
    private readonly IDatabaseService _db;
    private readonly HttpClient _httpClient;
    private readonly IConfiguration _config;
    private readonly ILogger<BakiyeSyncService> _logger;
    private readonly SyncStatusService _statusService;

    public BakiyeSyncService(
        IDatabaseService db,
        HttpClient httpClient,
        IConfiguration config,
        ILogger<BakiyeSyncService> logger,
        SyncStatusService statusService)
    {
        _db = db;
        _httpClient = httpClient;
        _config = config;
        _logger = logger;
        _statusService = statusService;
    }

    /// <summary>
    /// ATV_B2BBAKIYE view'inden bekleyen bakiyeleri getirir
    /// </summary>
    public async Task<IEnumerable<BakiyeDto>> GetPendingBakiyeAsync()
    {
        _logger.LogInformation("Bekleyen bakiyeler getiriliyor...");
        
        var topLimit = _config.GetValue<int>("SyncSettings:TopLimit", 0);
        var topClause = topLimit > 0 ? $"TOP {topLimit}" : "";
        
        var sql = $@"
            SELECT {topClause}
                STOK_KODU,
                BAKIYE,
                ISLEM
            FROM ATV_B2BBAKIYE
            WHERE ISLEM IN ('I', 'U')";

        var result = await _db.QueryAsync<BakiyeDto>(sql);
        var resultList = result.ToList();
        
        _logger.LogInformation("Bekleyen bakiye sayısı: {Count}", resultList.Count);
        return resultList;
    }

    /// <summary>
    /// Bakiyeleri Laravel'e senkronize eder
    /// Sadece mevcut ürünlerin bakiyesini günceller, yeni ürün oluşturmaz
    /// </summary>
    public async Task<BakiyeSyncResult> SyncToLaravelAsync()
    {
        var result = new BakiyeSyncResult();
        
        try
        {
            var pendingBakiyeler = (await GetPendingBakiyeAsync()).ToList();
            
            _logger.LogInformation("Toplam {Count} bakiye senkronize edilecek", pendingBakiyeler.Count);
            
            foreach (var bakiye in pendingBakiyeler)
            {
                try
                {
                    if (string.IsNullOrEmpty(bakiye.STOK_KODU))
                    {
                        result.SkippedCount++;
                        continue;
                    }
                    
                    // Laravel'e gönder
                    var (success, errorMessage) = await SendBakiyeToLaravelAsync(bakiye.STOK_KODU, bakiye.BAKIYE ?? 0);
                    
                    if (success)
                    {
                        // Log tablosuna yaz
                        await LogBakiyeOperationAsync(bakiye);
                        
                        result.UpdatedCount++;
                        _logger.LogDebug("Bakiye güncellendi: {StokKodu} = {Bakiye}", bakiye.STOK_KODU, bakiye.BAKIYE);
                    }
                    else if (errorMessage?.Contains("Ürün bulunamadı") == true || 
                             errorMessage?.Contains("404") == true)
                    {
                        result.SkippedCount++;
                        _logger.LogWarning("Ürün bulunamadı: {StokKodu}", bakiye.STOK_KODU);
                    }
                    else
                    {
                        result.ErrorCount++;
                        var errorMsg = $"{bakiye.STOK_KODU}: {errorMessage}";
                        result.Errors.Add(errorMsg);
                        _logger.LogWarning("Gönderilemedi: {StokKodu} - {Hata}", bakiye.STOK_KODU, errorMessage);
                        _statusService.AddError("BakiyeSync", errorMsg);
                    }
                }
                catch (Exception ex)
                {
                    result.ErrorCount++;
                    var errorMsg = $"{bakiye.STOK_KODU}: {ex.Message}";
                    result.Errors.Add(errorMsg);
                    _logger.LogError(ex, "Bakiye işlemi hatası: {StokKodu}", bakiye.STOK_KODU);
                    _statusService.AddError("BakiyeSync", errorMsg);
                }
            }
            
            result.Success = result.ErrorCount == 0;
            _logger.LogInformation(
                "Bakiye senkronizasyonu tamamlandı. Güncellenen: {Updated}, Atlanan: {Skipped}, Hata: {Error}",
                result.UpdatedCount, result.SkippedCount, result.ErrorCount);
        }
        catch (Exception ex)
        {
            result.Success = false;
            result.Errors.Add($"Genel hata: {ex.Message}");
            _logger.LogError(ex, "Bakiye senkronizasyon genel hatası");
        }
        
        return result;
    }

    /// <summary>
    /// AKTBL_B2BBAKIYELOG tablosuna log yazar
    /// </summary>
    public async Task LogBakiyeOperationAsync(BakiyeDto bakiye)
    {
        try
        {
            // Önce varsa sil
            var deleteSql = "DELETE FROM AKTBL_B2BBAKIYELOG WHERE STOK_KODU = @STOK_KODU";
            await _db.ExecuteAsync(deleteSql, new { bakiye.STOK_KODU });
            
            // Sonra ekle
            var insertSql = @"
                INSERT INTO AKTBL_B2BBAKIYELOG (STOK_KODU, BAKIYE, TARIH)
                VALUES (@STOK_KODU, @BAKIYE, GETDATE())";

            await _db.ExecuteAsync(insertSql, new
            {
                bakiye.STOK_KODU,
                bakiye.BAKIYE
            });
            
            _logger.LogDebug("Bakiye log kaydı yazıldı: {StokKodu}", bakiye.STOK_KODU);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Bakiye log yazma hatası: {StokKodu}", bakiye.STOK_KODU);
            throw;
        }
    }

    /// <summary>
    /// Laravel'e bakiye gönderir
    /// </summary>
    private async Task<(bool Success, string? ErrorMessage)> SendBakiyeToLaravelAsync(string stokKodu, decimal bakiye)
    {
        var laravelUrl = _config["LaravelApi:BaseUrl"] ?? "http://localhost:8000";
        var apiKey = _config["LaravelApi:ApiKey"] ?? "";
        
        var endpoint = $"{laravelUrl}/api/erp/products/{stokKodu}/bakiye";
        
        var payload = new { Bakiye = bakiye };
        
        var request = new HttpRequestMessage(HttpMethod.Put, endpoint)
        {
            Content = new StringContent(
                JsonSerializer.Serialize(payload),
                Encoding.UTF8,
                "application/json")
        };
        
        request.Headers.Add("X-API-Key", apiKey);
        
        try
        {
            var response = await _httpClient.SendAsync(request);
            
            if (!response.IsSuccessStatusCode)
            {
                var content = await response.Content.ReadAsStringAsync();
                _logger.LogWarning(
                    "Laravel API hatası: {StatusCode} - {Content}", 
                    response.StatusCode, 
                    content);
                return (false, $"HTTP {(int)response.StatusCode}: {content}");
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
