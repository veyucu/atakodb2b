using System.Text;
using AtakoErpService.Models;

namespace AtakoErpService.Services;

/// <summary>
/// Fatura Detay Service
/// Netsis SQL veritabanından fatura kalem ve toplam bilgilerini çeker
/// </summary>
public class FaturaDetayService
{
    private readonly IDatabaseService _db;
    private readonly ILogger<FaturaDetayService> _logger;

    // Windows-1252 -> UTF-8 Türkçe karakter dönüşümü
    private static readonly Dictionary<char, char> TurkishCharMap = new()
    {
        { 'Ð', 'Ğ' }, { 'ð', 'ğ' },
        { 'Þ', 'Ş' }, { 'þ', 'ş' },
        { 'Ý', 'İ' }, { 'ý', 'ı' },
    };

    public FaturaDetayService(
        IDatabaseService db,
        ILogger<FaturaDetayService> logger)
    {
        _db = db;
        _logger = logger;
    }

    /// <summary>
    /// Türkçe karakterleri düzeltir
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
    /// Fatura detayını getirir (kalemler + toplamlar)
    /// </summary>
    public async Task<FaturaDetayResponse> GetFaturaDetayAsync(string belgeNo, string musteriKodu)
    {
        var response = new FaturaDetayResponse
        {
            BelgeNo = belgeNo,
            MusteriKodu = musteriKodu
        };

        try
        {
            if (string.IsNullOrEmpty(belgeNo) || string.IsNullOrEmpty(musteriKodu))
            {
                response.Success = false;
                response.Message = "Belge no ve müşteri kodu zorunludur";
                return response;
            }

            _logger.LogInformation("Fatura detay getiriliyor: {BelgeNo}, {MusteriKodu}", belgeNo, musteriKodu);

            // 1. Fatura toplamlarını getir
            response.Toplam = await GetFaturaToplam(belgeNo, musteriKodu);

            // 2. Fatura kalemlerini getir
            response.Kalemler = await GetFaturaKalemler(belgeNo, musteriKodu);

            // Türkçe karakterleri düzelt
            foreach (var kalem in response.Kalemler)
            {
                kalem.StokAdi = FixTurkishChars(kalem.StokAdi);
            }

            response.Success = true;
            response.Message = $"{response.Kalemler.Count} adet kalem bulundu";

            _logger.LogInformation("Fatura detay tamamlandı: {BelgeNo}, {KalemSayisi} kalem", 
                belgeNo, response.Kalemler.Count);

            return response;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Fatura detay hatası: {BelgeNo}", belgeNo);
            response.Success = false;
            response.Message = "Fatura detay alınırken hata: " + ex.Message;
            return response;
        }
    }

    /// <summary>
    /// Fatura toplamlarını getirir (TBLFATUIRS)
    /// </summary>
    private async Task<FaturaToplam?> GetFaturaToplam(string belgeNo, string musteriKodu)
    {
        var sql = @"
            SELECT
                FATIRS_NO AS FatIrsNo,
                CARI_KODU AS CariKodu,
                GENELTOPLAM - KDV AS AraToplam,
                KDV AS Kdv,
                GENELTOPLAM AS GenelToplam
            FROM TBLFATUIRS WITH (NOLOCK)
            WHERE FTIRSIP IN (1,2) 
              AND FATIRS_NO = @BelgeNo 
              AND CARI_KODU = @MusteriKodu";

        return await _db.QueryFirstOrDefaultAsync<FaturaToplam>(sql, new
        {
            BelgeNo = belgeNo,
            MusteriKodu = musteriKodu
        });
    }

    /// <summary>
    /// Fatura kalemlerini getirir (TBLSTHAR + TBLSTSABIT)
    /// </summary>
    private async Task<List<FaturaKalem>> GetFaturaKalemler(string belgeNo, string musteriKodu)
    {
        var sql = @"
            SELECT
                A.INCKEYNO AS IncKeyNo,
                A.STOK_KODU AS StokKodu,
                B.STOK_ADI AS StokAdi,
                A.STHAR_GCMIK AS Miktar,
                ROUND(A.STHAR_NF * (1 + A.STHAR_KDV / 100), 2) AS Fiyat,
                ROUND(A.STHAR_GCMIK * (A.STHAR_NF * (1 + A.STHAR_KDV / 100)), 2) AS Tutar,
                A.STHAR_KDV AS KdvOrani
            FROM TBLSTHAR A WITH (NOLOCK)
            INNER JOIN TBLSTSABIT B WITH (NOLOCK) ON A.STOK_KODU = B.STOK_KODU
            WHERE A.STHAR_HTUR IN ('I','J','K','L') 
              AND A.FISNO = @BelgeNo 
              AND A.STHAR_ACIKLAMA = @MusteriKodu";

        var result = await _db.QueryAsync<FaturaKalem>(sql, new
        {
            BelgeNo = belgeNo,
            MusteriKodu = musteriKodu
        });

        return result.ToList();
    }
}
