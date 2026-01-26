using System.Text;
using System.Text.Json;
using AtakoErpService.Models;

namespace AtakoErpService.Services;

public class StokSyncService
{
    private readonly IDatabaseService _db;
    private readonly HttpClient _httpClient;
    private readonly IConfiguration _config;
    private readonly ILogger<StokSyncService> _logger;
    private readonly SyncStatusService _statusService;

    // Windows-1252 -> UTF-8 Türkçe karakter dönüşümü
    private static readonly Dictionary<char, char> TurkishCharMap = new()
    {
        { 'Ð', 'Ğ' }, { 'ð', 'ğ' },
        { 'Þ', 'Ş' }, { 'þ', 'ş' },
        { 'Ý', 'İ' }, { 'ý', 'ı' },
    };

    // UTF-8 -> Windows-1252 (SQL yazarken)
    private static readonly Dictionary<char, char> ReverseTurkishCharMap = new()
    {
        { 'Ğ', 'Ð' }, { 'ğ', 'ð' },
        { 'Ş', 'Þ' }, { 'ş', 'þ' },
        { 'İ', 'Ý' }, { 'ı', 'ý' },
    };

    public StokSyncService(
        IDatabaseService db,
        HttpClient httpClient,
        IConfiguration config,
        ILogger<StokSyncService> logger,
        SyncStatusService statusService)
    {
        _db = db;
        _httpClient = httpClient;
        _config = config;
        _logger = logger;
        _statusService = statusService;
    }

    /// <summary>
    /// Türkçe karakterleri düzeltir (SQL'den okurken)
    /// </summary>
    private static string? FixTurkishChars(string? text)
    {
        if (string.IsNullOrEmpty(text)) return text;
        
        var result = new StringBuilder(text.Length);
        foreach (var c in text)
        {
            result.Append(TurkishCharMap.TryGetValue(c, out var replacement) ? replacement : c);
        }
        return result.ToString();
    }

    /// <summary>
    /// Türkçe karakterleri geri çevirir (SQL'e yazarken)
    /// </summary>
    private static string? ReverseTurkishChars(string? text)
    {
        if (string.IsNullOrEmpty(text)) return text;
        
        var result = new StringBuilder(text.Length);
        foreach (var c in text)
        {
            result.Append(ReverseTurkishCharMap.TryGetValue(c, out var replacement) ? replacement : c);
        }
        return result.ToString();
    }

    /// <summary>
    /// Stok verisinde Türkçe karakterleri düzeltir
    /// </summary>
    private static StokDto FixStokTurkishChars(StokDto stok)
    {
        return new StokDto
        {
            STOK_KODU = stok.STOK_KODU,
            STOK_ADI = FixTurkishChars(stok.STOK_ADI),
            BARKOD = stok.BARKOD,
            GRUP = FixTurkishChars(stok.GRUP),
            MARKA = FixTurkishChars(stok.MARKA),
            KDV_ORANI = stok.KDV_ORANI,
            MUADIL_KODU = stok.MUADIL_KODU,
            ETKEN_MADDE = FixTurkishChars(stok.ETKEN_MADDE),
            PSF = stok.PSF,
            ECZ_KARI = stok.ECZ_KARI,
            KURUM_ISK = stok.KURUM_ISK,
            TIC_ISK = stok.TIC_ISK,
            DEPOCU_FIYATI = stok.DEPOCU_FIYATI,
            MF1 = stok.MF1,
            NET1 = stok.NET1,
            MF2 = stok.MF2,
            NET2 = stok.NET2,
            ISLEM = stok.ISLEM
        };
    }

    /// <summary>
    /// ATV_B2BSTOK view'inden bekleyen stokları getirir
    /// </summary>
    public async Task<IEnumerable<StokDto>> GetPendingStokAsync()
    {
        _logger.LogInformation("Bekleyen stoklar getiriliyor...");
        
        var topLimit = _config.GetValue<int>("SyncSettings:TopLimit", 0);
        var topClause = topLimit > 0 ? $"TOP {topLimit}" : "";
        
        var sql = $@"
            SELECT {topClause}
                STOK_KODU,
                STOK_ADI,
                BARKOD,
                GRUP,
                MARKA,
                KDV_ORANI,
                MUADIL_KODU,
                ETKEN_MADDE,
                PSF,
                ECZ_KARI,
                KURUM_ISK,
                TIC_ISK,
                DEPOCU_FIYATI,
                MF1,
                NET1,
                MF2,
                NET2,
                ISLEM
            FROM ATV_B2BSTOK
            WHERE ISLEM IN ('I', 'U')";

        var result = await _db.QueryAsync<StokDto>(sql);
        
        // Türkçe karakterleri düzelt
        var fixedResult = result.Select(FixStokTurkishChars).ToList();
        
        _logger.LogInformation("Bekleyen stok sayısı: {Count}", fixedResult.Count);
        return fixedResult;
    }

    /// <summary>
    /// Stokları Laravel'e senkronize eder
    /// </summary>
    public async Task<StokSyncResult> SyncToLaravelAsync()
    {
        var result = new StokSyncResult();
        
        try
        {
            var pendingStoks = (await GetPendingStokAsync()).ToList();
            
            _logger.LogInformation("Toplam {Count} stok senkronize edilecek", pendingStoks.Count);
            
            foreach (var stok in pendingStoks)
            {
                try
                {
                    var webProduct = MapToWebProduct(stok);
                    bool isUpdate = stok.ISLEM?.ToUpper() == "U";
                    
                    // Laravel'e gönder
                    var (success, errorMessage) = await SendToLaravelAsync(webProduct, isUpdate);
                    
                    if (success)
                    {
                        // Log tablosuna yaz
                        await LogStokOperationAsync(stok, isUpdate);
                        
                        if (isUpdate)
                            result.UpdatedCount++;
                        else
                            result.InsertedCount++;
                        
                        _logger.LogInformation(
                            "{Operation} başarılı: {StokKodu} - {StokAdi}", 
                            isUpdate ? "Güncelleme" : "Ekleme",
                            stok.STOK_KODU, 
                            stok.STOK_ADI);
                    }
                    else
                    {
                        result.ErrorCount++;
                        var errorMsg = $"{stok.STOK_KODU}: {errorMessage}";
                        result.Errors.Add(errorMsg);
                        _logger.LogWarning("Gönderilemedi: {StokKodu} - {Hata}", stok.STOK_KODU, errorMessage);
                        _statusService.AddError("StokSync", errorMsg);
                    }
                }
                catch (Exception ex)
                {
                    result.ErrorCount++;
                    var errorMsg = $"{stok.STOK_KODU}: {ex.Message}";
                    result.Errors.Add(errorMsg);
                    _logger.LogError(ex, "Stok işlemi hatası: {StokKodu}", stok.STOK_KODU);
                    _statusService.AddError("StokSync", errorMsg);
                }
            }
            
            result.Success = result.ErrorCount == 0;
            _logger.LogInformation(
                "Stok senkronizasyonu tamamlandı. Eklenen: {Inserted}, Güncellenen: {Updated}, Hata: {Error}",
                result.InsertedCount, result.UpdatedCount, result.ErrorCount);
        }
        catch (Exception ex)
        {
            result.Success = false;
            result.Errors.Add($"Genel hata: {ex.Message}");
            _logger.LogError(ex, "Stok senkronizasyon genel hatası");
        }
        
        return result;
    }

    /// <summary>
    /// AKTBL_B2BSTOKLOG tablosuna log yazar (DEPOCU_FIYATI hariç)
    /// </summary>
    public async Task LogStokOperationAsync(StokDto stok, bool isUpdate)
    {
        try
        {
            // Önce varsa sil
            var deleteSql = "DELETE FROM AKTBL_B2BSTOKLOG WHERE STOK_KODU = @STOK_KODU";
            await _db.ExecuteAsync(deleteSql, new { stok.STOK_KODU });
            
            // Sonra ekle
            await InsertLogAsync(stok);
            
            _logger.LogDebug("Stok log kaydı yazıldı: {StokKodu}", stok.STOK_KODU);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Stok log yazma hatası: {StokKodu}", stok.STOK_KODU);
            throw;
        }
    }

    private async Task InsertLogAsync(StokDto stok)
    {
        var sql = @"
            INSERT INTO AKTBL_B2BSTOKLOG 
            (STOK_KODU, STOK_ADI, BARKOD, GRUP, MARKA, KDV_ORANI, MUADIL_KODU, ETKEN_MADDE,
             PSF, ECZ_KARI, KURUM_ISK, TIC_ISK, MF1, NET1, MF2, NET2 )
            VALUES 
            (@STOK_KODU, @STOK_ADI, @BARKOD, @GRUP, @MARKA, @KDV_ORANI, @MUADIL_KODU, @ETKEN_MADDE,
             @PSF, @ECZ_KARI, @KURUM_ISK, @TIC_ISK, @MF1, @NET1, @MF2, @NET2)";

        await _db.ExecuteAsync(sql, new
        {
            stok.STOK_KODU,
            STOK_ADI = ReverseTurkishChars(stok.STOK_ADI),
            stok.BARKOD,
            GRUP = ReverseTurkishChars(stok.GRUP),
            MARKA = ReverseTurkishChars(stok.MARKA),
            stok.KDV_ORANI,
            stok.MUADIL_KODU,
            ETKEN_MADDE = ReverseTurkishChars(stok.ETKEN_MADDE),
            stok.PSF,
            stok.ECZ_KARI,
            stok.KURUM_ISK,
            stok.TIC_ISK,
            stok.MF1,
            stok.NET1,
            stok.MF2,
            stok.NET2
        });
    }

    /// <summary>
    /// StokDto'yu WebProductDto'ya dönüştürür
    /// </summary>
    private static WebProductDto MapToWebProduct(StokDto stok)
    {
        return new WebProductDto
        {
            UrunKodu = stok.STOK_KODU ?? "",
            UrunAdi = stok.STOK_ADI ?? "",
            Barkod = stok.BARKOD,
            Grup = stok.GRUP,
            Marka = stok.MARKA,
            KdvOrani = stok.KDV_ORANI,
            MuadilKodu = stok.MUADIL_KODU,
            EtkenMadde = stok.ETKEN_MADDE,
            SatisFiyati = stok.PSF,
            EczaciKari = stok.ECZ_KARI,
            KurumIskonto = stok.KURUM_ISK,
            TicariIskonto = stok.TIC_ISK,
            DepocuFiyati = stok.DEPOCU_FIYATI,
            Mf1 = stok.MF1,
            NetFiyat1 = stok.NET1,
            Mf2 = stok.MF2,
            NetFiyat2 = stok.NET2
        };
    }

    /// <summary>
    /// Laravel'e ürün gönderir
    /// </summary>
    private async Task<(bool Success, string? ErrorMessage)> SendToLaravelAsync(WebProductDto product, bool isUpdate)
    {
        var laravelUrl = _config["LaravelApi:BaseUrl"] ?? "http://localhost:8000";
        var apiKey = _config["LaravelApi:ApiKey"] ?? "";
        
        var endpoint = isUpdate 
            ? $"{laravelUrl}/api/erp/products/{product.UrunKodu}" 
            : $"{laravelUrl}/api/erp/products";
        
        var method = isUpdate ? HttpMethod.Put : HttpMethod.Post;
        
        var request = new HttpRequestMessage(method, endpoint)
        {
            Content = new StringContent(
                JsonSerializer.Serialize(product),
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
