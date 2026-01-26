using System.Text;
using AtakoErpService.Models;

namespace AtakoErpService.Services;

/// <summary>
/// Cari Ekstre (Customer Account Statement) Service
/// Netsis SQL veritabanından doğrudan cari hareket verisi çeker
/// </summary>
public class CariEkstreService
{
    private readonly IDatabaseService _db;
    private readonly ILogger<CariEkstreService> _logger;

    // Windows-1252 -> UTF-8 Türkçe karakter dönüşümü
    private static readonly Dictionary<char, char> TurkishCharMap = new()
    {
        { 'Ð', 'Ğ' }, { 'ð', 'ğ' },
        { 'Þ', 'Ş' }, { 'þ', 'ş' },
        { 'Ý', 'İ' }, { 'ý', 'ı' },
    };

    public CariEkstreService(
        IDatabaseService db,
        ILogger<CariEkstreService> logger)
    {
        _db = db;
        _logger = logger;
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
    /// Cari hesap ekstresini getirir
    /// UNION ile devir bakiyesi + dönem hareketleri tek sorguda
    /// </summary>
    public async Task<CariEkstreResponse> GetEkstreAsync(string musteriKodu, string baslangicTarihi, string bitisTarihi)
    {
        var response = new CariEkstreResponse
        {
            MusteriKodu = musteriKodu,
            BaslangicTarihi = baslangicTarihi,
            BitisTarihi = bitisTarihi
        };

        try
        {
            if (string.IsNullOrEmpty(musteriKodu))
            {
                response.Success = false;
                response.Message = "Müşteri kodu boş olamaz";
                return response;
            }

            _logger.LogInformation("Cari ekstre getiriliyor: {MusteriKodu}, {BaslangicTarihi} - {BitisTarihi}", 
                musteriKodu, baslangicTarihi, bitisTarihi);

            // UNION ile devir + dönem hareketleri tek sorguda
            var hareketler = await GetEkstreWithDevirAsync(musteriKodu, baslangicTarihi, bitisTarihi);
            
            // Bakiye hesaplaması
            decimal calisanBakiye = 0;
            decimal devirBakiye = 0;
            
            foreach (var hareket in hareketler)
            {
                calisanBakiye += hareket.Borc - hareket.Alacak;
                hareket.Bakiye = calisanBakiye;
                
                // Devir satırının bakiyesini kaydet
                if (hareket.HareketTuru == "A")
                {
                    devirBakiye = hareket.Borc - hareket.Alacak;
                }
                
                // Türkçe karakterleri düzelt
                hareket.Aciklama = FixTurkishChars(hareket.Aciklama);
                hareket.HareketAdi = FixTurkishChars(hareket.HareketAdi);
            }

            response.Hareketler = hareketler;
            response.DevirBakiye = devirBakiye;
            response.ToplamBorc = hareketler.Sum(h => h.Borc);
            response.ToplamAlacak = hareketler.Sum(h => h.Alacak);
            response.GenelBakiye = calisanBakiye;
            response.Success = true;
            response.Message = $"Devir: {devirBakiye:N2} TL, {hareketler.Count} adet hareket";

            _logger.LogInformation("Cari ekstre tamamlandı: {MusteriKodu}, Devir: {Devir:N2}, {Count} hareket, Son Bakiye: {Bakiye:N2}", 
                musteriKodu, devirBakiye, hareketler.Count, calisanBakiye);

            return response;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Cari ekstre hatası: {MusteriKodu}", musteriKodu);
            response.Success = false;
            response.Message = "Cari ekstre alınırken hata oluştu: " + ex.Message;
            return response;
        }
    }

    /// <summary>
    /// UNION ile devir bakiyesi + dönem hareketleri tek sorguda
    /// </summary>
    private async Task<List<CariHareketDto>> GetEkstreWithDevirAsync(string musteriKodu, string baslangicTarihi, string bitisTarihi)
    {
        var sql = @"
            -- Devir bakiyesi (başlangıç tarihinden önceki toplam) - sadece sıfır değilse
            SELECT 
                @BaslangicTarihi AS Tarih,
                @BaslangicTarihi AS VadeTarihi,
                '' AS BelgeNo,
                'A' AS HareketTuru,
                'Devir' AS HareketAdi,
                'Devir Bakiyesi' AS Aciklama,
                CASE WHEN ISNULL(SUM(BORC - ALACAK), 0) > 0 THEN ISNULL(SUM(BORC - ALACAK), 0) ELSE 0 END AS Borc,
                CASE WHEN ISNULL(SUM(BORC - ALACAK), 0) < 0 THEN ABS(ISNULL(SUM(BORC - ALACAK), 0)) ELSE 0 END AS Alacak,
                0 AS Bakiye,
                '' AS EntRefKey
            FROM TBLCAHAR
            WHERE CARI_KOD = @MusteriKodu
              AND TARIH < @BaslangicTarihi
            HAVING ISNULL(SUM(BORC - ALACAK), 0) <> 0

            UNION ALL

            -- Dönem hareketleri
            SELECT 
                CONVERT(VARCHAR(10), TARIH, 120) AS Tarih,
                CONVERT(VARCHAR(10), VADE_TARIHI, 120) AS VadeTarihi,
                BELGE_NO AS BelgeNo,
                HAREKET_TURU AS HareketTuru,
                CASE HAREKET_TURU 
                    WHEN 'A' THEN 'Devir'
                    WHEN 'B' THEN 'Fatura'
                    WHEN 'C' THEN 'Iade Fatura'
                    WHEN 'D' THEN 'Kasa'
                    WHEN 'E' THEN 'Müsteri Senedi'
                    WHEN 'F' THEN 'Borç Senedi'
                    WHEN 'G' THEN 'Müsteri Çeki'
                    WHEN 'H' THEN 'Borç Çeki'
                    WHEN 'I' THEN 'Prot. Senet'
                    WHEN 'J' THEN 'Kars. Çek'
                    WHEN 'K' THEN 'Dekont'
                    WHEN 'L' THEN 'Muhtelif' 
                    ELSE HAREKET_TURU 
                END AS HareketAdi,
                ISNULL(ACIKLAMA, '') AS Aciklama,
                ISNULL(BORC, 0) AS Borc,
                ISNULL(ALACAK, 0) AS Alacak,
                0 AS Bakiye,
                ENT_REF_KEY AS EntRefKey
            FROM TBLCAHAR
            WHERE CARI_KOD = @MusteriKodu
              AND TARIH >= @BaslangicTarihi
              AND TARIH <= @BitisTarihi
            
            ORDER BY Tarih, BelgeNo";

        var result = await _db.QueryAsync<CariHareketDto>(sql, new
        {
            MusteriKodu = musteriKodu,
            BaslangicTarihi = baslangicTarihi,
            BitisTarihi = bitisTarihi
        });

        return result.ToList();
    }

    /// <summary>
    /// Carinin güncel bakiyesini getirir (tüm zamanlar)
    /// </summary>
    public async Task<decimal> GetGuncelBakiyeAsync(string musteriKodu)
    {
        try
        {
            var sql = @"
                SELECT ISNULL(SUM(BORC) - SUM(ALACAK), 0) AS Bakiye
                FROM TBLCAHAR
                WHERE CARI_KOD = @MusteriKodu";

            var result = await _db.QueryFirstOrDefaultAsync<decimal?>(sql, new { MusteriKodu = musteriKodu });
            return result ?? 0;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Güncel bakiye alınamadı: {MusteriKodu}", musteriKodu);
            return 0;
        }
    }
}
