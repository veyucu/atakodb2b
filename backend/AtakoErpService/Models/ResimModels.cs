namespace AtakoErpService.Models;

/// <summary>
/// ATV_B2BRESIM view'inden gelen resim verisi
/// </summary>
public class ResimDto
{
    public string? STOK_KODU { get; set; }
    public byte[]? RESIM { get; set; }
    public string? ISLEM { get; set; }
}

/// <summary>
/// Resim senkronizasyon sonucu
/// </summary>
public class ResimSyncResult
{
    public bool Success { get; set; }
    public int UpdatedCount { get; set; }
    public int ErrorCount { get; set; }
    public int SkippedCount { get; set; } // Ürün bulunamadığında
    public List<string> Errors { get; set; } = new();
}
