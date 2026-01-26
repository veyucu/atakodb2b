namespace AtakoErpService.Models;

/// <summary>
/// Cari Ekstre Response
/// </summary>
public class CariEkstreResponse
{
    public bool Success { get; set; }
    public string? Message { get; set; }
    public string? MusteriKodu { get; set; }
    public string? MusteriAdi { get; set; }
    public string? BaslangicTarihi { get; set; }
    public string? BitisTarihi { get; set; }
    public decimal ToplamBorc { get; set; }
    public decimal ToplamAlacak { get; set; }
    public decimal DevirBakiye { get; set; }
    public decimal GenelBakiye { get; set; }
    public List<CariHareketDto> Hareketler { get; set; } = new();
}

/// <summary>
/// Cari Hareket (Account Movement)
/// </summary>
public class CariHareketDto
{
    public string? Tarih { get; set; }
    public string? VadeTarihi { get; set; }
    public string? BelgeNo { get; set; }
    public string? HareketTuru { get; set; }
    public string? HareketAdi { get; set; }
    public string? Aciklama { get; set; }
    public decimal Borc { get; set; }
    public decimal Alacak { get; set; }
    public decimal Bakiye { get; set; }
    public string? EntRefKey { get; set; }
}
