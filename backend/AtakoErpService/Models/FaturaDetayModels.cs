namespace AtakoErpService.Models;

/// <summary>
/// Fatura Detay Response
/// </summary>
public class FaturaDetayResponse
{
    public bool Success { get; set; }
    public string? Message { get; set; }
    public string? BelgeNo { get; set; }
    public string? MusteriKodu { get; set; }
    public FaturaToplam? Toplam { get; set; }
    public List<FaturaKalem> Kalemler { get; set; } = new();
}

/// <summary>
/// Fatura Toplamları
/// </summary>
public class FaturaToplam
{
    public string? FatIrsNo { get; set; }
    public string? CariKodu { get; set; }
    public decimal AraToplam { get; set; }
    public decimal Kdv { get; set; }
    public decimal GenelToplam { get; set; }
}

/// <summary>
/// Fatura Kalemi
/// </summary>
public class FaturaKalem
{
    public long IncKeyNo { get; set; }
    public string? StokKodu { get; set; }
    public string? StokAdi { get; set; }
    public decimal Miktar { get; set; }
    public decimal Fiyat { get; set; }
    public decimal Tutar { get; set; }
    public decimal KdvOrani { get; set; }
}
