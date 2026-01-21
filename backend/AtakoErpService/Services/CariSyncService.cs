using System.Text;
using System.Text.Json;
using AtakoErpService.Models;

namespace AtakoErpService.Services;

public interface ICariSyncService
{
    Task<IEnumerable<CariDto>> GetPendingCariAsync();
    Task<SyncResult> SyncToLaravelAsync();
    Task LogCariOperationAsync(CariDto cari, bool isUpdate);
}

public class CariSyncService : ICariSyncService
{
    private readonly IDatabaseService _db;
    private readonly ILogger<CariSyncService> _logger;
    private readonly IConfiguration _config;
    private readonly HttpClient _httpClient;

    // Türkçe karakter dönüşüm tablosu (Windows-1252 -> UTF-8 Turkish)
    private static readonly Dictionary<char, char> TurkishCharMap = new()
    {
        { 'Ð', 'Ğ' }, { 'ð', 'ğ' },
        { 'Þ', 'Ş' }, { 'þ', 'ş' },
        { 'Ý', 'İ' }, { 'ý', 'ı' },
        { '\u0131', 'ı' } // dotless i
    };

    // Ters dönüşüm tablosu (UTF-8 Turkish -> Windows-1252 for SQL writes)
    private static readonly Dictionary<char, char> ReverseTurkishCharMap = new()
    {
        { 'Ğ', 'Ð' }, { 'ğ', 'ð' },
        { 'Ş', 'Þ' }, { 'ş', 'þ' },
        { 'İ', 'Ý' }, { 'ı', 'ý' }
    };

    public CariSyncService(
        IDatabaseService db, 
        ILogger<CariSyncService> logger, 
        IConfiguration config,
        IHttpClientFactory httpClientFactory)
    {
        _db = db;
        _logger = logger;
        _config = config;
        _httpClient = httpClientFactory.CreateClient("LaravelApi");
    }

    /// <summary>
    /// Türkçe karakterleri düzeltir (Windows-1252 -> UTF-8)
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
    /// Türkçe karakterleri SQL formatına çevirir (UTF-8 -> Windows-1252)
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
    /// CariDto'daki tüm string alanları düzeltir
    /// </summary>
    private static CariDto FixCariTurkishChars(CariDto cari)
    {
        return new CariDto
        {
            CARI_KODU = cari.CARI_KODU,
            CARI_ISIM = FixTurkishChars(cari.CARI_ISIM),
            ADRES = FixTurkishChars(cari.ADRES),
            ILCE = FixTurkishChars(cari.ILCE),
            IL = FixTurkishChars(cari.IL),
            PLASIYER_KODU = cari.PLASIYER_KODU,
            SIFRE = FixTurkishChars(cari.SIFRE),
            ISLEM = cari.ISLEM
        };
    }

    /// <summary>
    /// ATV_B2BCARI view'inden bekleyen carileri getirir
    /// Development ortaminda TopLimit ayarına göre sınırlar
    /// </summary>
    public async Task<IEnumerable<CariDto>> GetPendingCariAsync()
    {
        _logger.LogInformation("Bekleyen cariler getiriliyor...");
        
        // Development ortaminda sınırlı kayıt çek
        var topLimit = _config.GetValue<int>("SyncSettings:TopLimit", 0);
        var topClause = topLimit > 0 ? $"TOP {topLimit}" : "";
        
        var sql = $@"
            SELECT {topClause}
                CARI_KODU,
                CARI_ISIM,
                ADRES,
                ILCE,
                IL,
                PLASIYER_KODU,
                SIFRE,
                ISLEM
            FROM ATV_B2BCARI
            WHERE ISLEM IN ('I', 'U')";

        var result = await _db.QueryAsync<CariDto>(sql);
        
        // Türkçe karakterleri düzelt
        var fixedResult = result.Select(FixCariTurkishChars).ToList();
        
        _logger.LogInformation("Bekleyen cari sayısı: {Count}", fixedResult.Count);
        return fixedResult;
    }

    /// <summary>
    /// Carileri Laravel'e tek tek senkronize eder
    /// </summary>
    public async Task<SyncResult> SyncToLaravelAsync()
    {
        var result = new SyncResult();
        
        try
        {
            var pendingCaris = (await GetPendingCariAsync()).ToList();
            
            _logger.LogInformation("Toplam {Count} cari senkronize edilecek", pendingCaris.Count);
            
            foreach (var cari in pendingCaris)
            {
                try
                {
                    var webUser = MapToWebUser(cari);
                    bool isUpdate = cari.ISLEM?.ToUpper() == "U";
                    
                    // Laravel'e gönder
                    var (success, errorMessage) = await SendToLaravelAsync(webUser, isUpdate);
                    
                    if (success)
                    {
                        // Log tablosuna yaz
                        await LogCariOperationAsync(cari, isUpdate);
                        
                        if (isUpdate)
                            result.UpdatedCount++;
                        else
                            result.InsertedCount++;
                        
                        _logger.LogInformation(
                            "{Operation} başarılı: {CariKodu} - {CariIsim}", 
                            isUpdate ? "Güncelleme" : "Ekleme",
                            cari.CARI_KODU, 
                            cari.CARI_ISIM);
                    }
                    else
                    {
                        result.ErrorCount++;
                        result.Errors.Add($"{cari.CARI_KODU}: {errorMessage}");
                        _logger.LogWarning("Gönderilemedi: {CariKodu} - {Hata}", cari.CARI_KODU, errorMessage);
                    }
                }
                catch (Exception ex)
                {
                    result.ErrorCount++;
                    result.Errors.Add($"{cari.CARI_KODU}: {ex.Message}");
                    _logger.LogError(ex, "Cari işlemi hatası: {CariKodu}", cari.CARI_KODU);
                }
            }
            
            result.Success = result.ErrorCount == 0;
            _logger.LogInformation(
                "Senkronizasyon tamamlandı. Eklenen: {Inserted}, Güncellenen: {Updated}, Hata: {Error}",
                result.InsertedCount, result.UpdatedCount, result.ErrorCount);
        }
        catch (Exception ex)
        {
            result.Success = false;
            result.Errors.Add($"Genel hata: {ex.Message}");
            _logger.LogError(ex, "Senkronizasyon genel hatası");
        }
        
        return result;
    }

    /// <summary>
    /// AKTBL_B2BCARILOG tablosuna log yazar (sil ve tekrar yaz)
    /// </summary>
    public async Task LogCariOperationAsync(CariDto cari, bool isUpdate)
    {
        try
        {
            // Önce varsa sil
            var deleteSql = "DELETE FROM AKTBL_B2BCARILOG WHERE CARI_KODU = @CARI_KODU";
            await _db.ExecuteAsync(deleteSql, new { cari.CARI_KODU });
            
            // Sonra tekrar ekle
            await InsertLogAsync(cari);
            
            _logger.LogDebug("Log kaydı yazıldı: {CariKodu}", cari.CARI_KODU);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Log yazma hatası: {CariKodu}", cari.CARI_KODU);
            throw;
        }
    }

    private async Task InsertLogAsync(CariDto cari)
    {
        var insertSql = @"
            INSERT INTO AKTBL_B2BCARILOG (CARI_KODU, CARI_ISIM, ADRES, ILCE, IL, PLASIYER_KODU, SIFRE)
            VALUES (@CARI_KODU, @CARI_ISIM, @ADRES, @ILCE, @IL, @PLASIYER_KODU, @SIFRE)";
        
        // Türkçe karakterleri SQL formatına çevir
        await _db.ExecuteAsync(insertSql, new
        {
            cari.CARI_KODU,
            CARI_ISIM = ReverseTurkishChars(cari.CARI_ISIM),
            ADRES = ReverseTurkishChars(cari.ADRES),
            ILCE = ReverseTurkishChars(cari.ILCE),
            IL = ReverseTurkishChars(cari.IL),
            cari.PLASIYER_KODU,
            SIFRE = ReverseTurkishChars(cari.SIFRE)
        });
    }

    private WebUserDto MapToWebUser(CariDto cari)
    {
        return new WebUserDto
        {
            KullaniciKodu = cari.CARI_KODU,
            AdSoyad = cari.CARI_ISIM ?? "",
            Adres = cari.ADRES,
            Ilce = cari.ILCE,
            Il = cari.IL,
            PlasiyerKodu = cari.PLASIYER_KODU,
            Sifre = cari.SIFRE,
            KullaniciTipi = "musteri",
            Aktif = true
        };
    }

    private async Task<(bool Success, string? ErrorMessage)> SendToLaravelAsync(WebUserDto user, bool isUpdate)
    {
        var laravelUrl = _config["LaravelApi:BaseUrl"] ?? "http://localhost:8000";
        var apiKey = _config["LaravelApi:ApiKey"] ?? "";
        
        var endpoint = isUpdate 
            ? $"{laravelUrl}/api/erp/users/{user.KullaniciKodu}" 
            : $"{laravelUrl}/api/erp/users";
        
        var method = isUpdate ? HttpMethod.Put : HttpMethod.Post;
        
        var request = new HttpRequestMessage(method, endpoint)
        {
            Content = new StringContent(
                JsonSerializer.Serialize(user),
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
