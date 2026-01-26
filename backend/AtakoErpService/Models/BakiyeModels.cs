namespace AtakoErpService.Models;

/// <summary>
/// ATV_B2BBAKIYE view'inden gelen bakiye verisi
/// </summary>
public class BakiyeDto
{
    public string? STOK_KODU { get; set; }
    public decimal? BAKIYE { get; set; }
    public string? ISLEM { get; set; }
}

/// <summary>
/// Bakiye senkronizasyon sonucu
/// </summary>
public class BakiyeSyncResult
{
    public bool Success { get; set; }
    public int UpdatedCount { get; set; }
    public int ErrorCount { get; set; }
    public int SkippedCount { get; set; } // Ürün bulunamadığında
    public List<string> Errors { get; set; } = new();
    public int SuccessCount => UpdatedCount;
}
