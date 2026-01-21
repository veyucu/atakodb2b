namespace AtakoErpService.Models;

/// <summary>
/// ATV_B2BCARI view'inden gelen cari verisi
/// </summary>
public class CariDto
{
    /// <summary>SQL: CARI_KODU - Web: Kullanıcı Kodu (Anahtar alan)</summary>
    public string CARI_KODU { get; set; } = "";
    
    /// <summary>SQL: CARI_ISIM - Web: Ad Soyad</summary>
    public string? CARI_ISIM { get; set; }
    
    /// <summary>SQL: ADRES - Web: Adres</summary>
    public string? ADRES { get; set; }
    
    /// <summary>SQL: ILCE - Web: İlçe</summary>
    public string? ILCE { get; set; }
    
    /// <summary>SQL: IL - Web: İl</summary>
    public string? IL { get; set; }
    
    /// <summary>SQL: PLASIYER_KODU - Web: Plasiyer Kodu</summary>
    public string? PLASIYER_KODU { get; set; }
    
    /// <summary>SQL: SIFRE - Web: Şifre</summary>
    public string? SIFRE { get; set; }
    
    /// <summary>SQL: ISLEM - I: Insert, U: Update</summary>
    public string? ISLEM { get; set; }
}

/// <summary>
/// Laravel'e gönderilecek kullanıcı verisi
/// </summary>
public class WebUserDto
{
    public string KullaniciKodu { get; set; } = "";
    public string AdSoyad { get; set; } = "";
    public string? Adres { get; set; }
    public string? Ilce { get; set; }
    public string? Il { get; set; }
    public string? PlasiyerKodu { get; set; }
    public string? Sifre { get; set; }
    public string KullaniciTipi { get; set; } = "musteri";
    public bool Aktif { get; set; } = true;
}

/// <summary>
/// AKTBL_B2BCARILOG tablosuna yazılacak log verisi
/// </summary>
public class CariLogDto
{
    public string CARI_KODU { get; set; } = "";
    public string? CARI_ISIM { get; set; }
    public string? ADRES { get; set; }
    public string? ILCE { get; set; }
    public string? IL { get; set; }
    public string? PLASIYER_KODU { get; set; }
    public string? SIFRE { get; set; }
    public DateTime TARIH { get; set; } = DateTime.Now;
}

/// <summary>
/// Senkronizasyon sonucu
/// </summary>
public class SyncResult
{
    public bool Success { get; set; }
    public int InsertedCount { get; set; }
    public int UpdatedCount { get; set; }
    public int ErrorCount { get; set; }
    public List<string> Errors { get; set; } = new();
    public DateTime SyncTime { get; set; } = DateTime.Now;
}
