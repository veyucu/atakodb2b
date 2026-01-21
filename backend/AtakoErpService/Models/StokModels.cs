namespace AtakoErpService.Models;

/// <summary>
/// ATV_B2BSTOK view'inden gelen stok verisi
/// </summary>
public class StokDto
{
    public string? STOK_KODU { get; set; }
    public string? STOK_ADI { get; set; }
    public string? BARKOD { get; set; }
    public string? GRUP { get; set; }
    public string? MARKA { get; set; }
    public decimal? KDV_ORANI { get; set; }
    public string? MUADIL_KODU { get; set; }
    public string? ETKEN_MADDE { get; set; }
    public decimal? PSF { get; set; }
    public decimal? ECZ_KARI { get; set; }
    public decimal? KURUM_ISK { get; set; }
    public decimal? TIC_ISK { get; set; }
    public decimal? DEPOCU_FIYATI { get; set; }
    public string? MF1 { get; set; }
    public decimal? NET1 { get; set; }
    public string? MF2 { get; set; }
    public decimal? NET2 { get; set; }
    public string? ISLEM { get; set; }
}

/// <summary>
/// Laravel'e gönderilecek ürün verisi
/// </summary>
public class WebProductDto
{
    public string UrunKodu { get; set; } = "";
    public string UrunAdi { get; set; } = "";
    public string? Barkod { get; set; }
    public string? Grup { get; set; }
    public string? Marka { get; set; }
    public decimal? KdvOrani { get; set; }
    public string? MuadilKodu { get; set; }
    public string? EtkenMadde { get; set; }
    public decimal? SatisFiyati { get; set; }
    public decimal? EczaciKari { get; set; }
    public decimal? KurumIskonto { get; set; }
    public decimal? TicariIskonto { get; set; }
    public decimal? DepocuFiyati { get; set; }
    public string? Mf1 { get; set; }
    public decimal? NetFiyat1 { get; set; }
    public string? Mf2 { get; set; }
    public decimal? NetFiyat2 { get; set; }
}

/// <summary>
/// Stok senkronizasyon sonucu
/// </summary>
public class StokSyncResult
{
    public bool Success { get; set; }
    public int InsertedCount { get; set; }
    public int UpdatedCount { get; set; }
    public int ErrorCount { get; set; }
    public List<string> Errors { get; set; } = new();
}
