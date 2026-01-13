namespace AtakoDB2B.WindowsService.Models;

/// <summary>
/// Netsis CARI_HESAPLAR tablosundan müşteri bilgileri
/// </summary>
public class NetsisCustomer
{
    public string cari_kod { get; set; } = string.Empty;
    public string cari_isim { get; set; } = string.Empty;
    public string cari_unvan1 { get; set; } = string.Empty;
    public string cari_adres1 { get; set; } = string.Empty;
    public string cari_ilce { get; set; } = string.Empty;
    public string cari_il { get; set; } = string.Empty;
    public string cari_tel1 { get; set; } = string.Empty;
    public string cari_email { get; set; } = string.Empty;
    public string cari_vergi_dairesi { get; set; } = string.Empty;
    public string cari_vergi_no { get; set; } = string.Empty;
    public string cari_gln_kodu { get; set; } = string.Empty;
    public string cari_grup_kodu { get; set; } = string.Empty;
    public string plasiyer_kodu { get; set; } = string.Empty;
    public int cari_hareket_tipi { get; set; }
    public DateTime? cari_kayit_tarihi { get; set; }
    public DateTime? cari_guncelleme_tarihi { get; set; }
}

/// <summary>
/// Netsis STOKLAR tablosundan ürün bilgileri
/// </summary>
public class NetsisProduct
{
    public string sto_kod { get; set; } = string.Empty;
    public string sto_isim { get; set; } = string.Empty;
    public string barkod { get; set; } = string.Empty;
    public string muadil_kodu { get; set; } = string.Empty;
    public decimal sto_perakende_vergi { get; set; }
    public decimal sto_kdv_dahil_perakende { get; set; }
    public decimal sto_kdv_haric_perakende { get; set; }
    public int kdv_kodu { get; set; }
    public decimal kurum_iskonto { get; set; }
    public decimal eczaci_kari { get; set; }
    public decimal ticari_iskonto { get; set; }
    public string mf { get; set; } = string.Empty;
    public decimal depocu_fiyati { get; set; }
    public decimal net_fiyat { get; set; }
    public decimal sto_miktar { get; set; }
    public string sto_marka_kodu { get; set; } = string.Empty;
    public string sto_grup_kodu { get; set; } = string.Empty;
    public string sto_birim1_ad { get; set; } = string.Empty;
    public int sto_pasif_mi { get; set; }
    public DateTime? sto_kayit_tarihi { get; set; }
    public DateTime? sto_guncelleme_tarihi { get; set; }
}

/// <summary>
/// Netsis STOK_HAREKETLERI tablosundan stok bilgileri
/// </summary>
public class NetsisStock
{
    public string sto_kod { get; set; } = string.Empty;
    public decimal miktar { get; set; }
    public string depo_kodu { get; set; } = string.Empty;
    public DateTime? hareket_tarihi { get; set; }
}

/// <summary>
/// API'ye gönderilecek müşteri modeli
/// </summary>
public class ApiUserDto
{
    public string musteri_kodu { get; set; } = string.Empty;
    public string name { get; set; } = string.Empty;
    public string email { get; set; } = string.Empty;
    public string? password { get; set; }
    public string user_type { get; set; } = "musteri";
    public string? musteri_adi { get; set; }
    public string? adres { get; set; }
    public string? ilce { get; set; }
    public string? il { get; set; }
    public string? gln_numarasi { get; set; }
    public string? telefon { get; set; }
    public string? mail_adresi { get; set; }
    public string? vergi_dairesi { get; set; }
    public string? vergi_kimlik_numarasi { get; set; }
    public string? grup_kodu { get; set; }
    public string? plasiyer_kodu { get; set; }
    public bool is_active { get; set; } = true;
}

/// <summary>
/// API'ye gönderilecek ürün modeli
/// </summary>
public class ApiProductDto
{
    public string urun_kodu { get; set; } = string.Empty;
    public string urun_adi { get; set; } = string.Empty;
    public string? barkod { get; set; }
    public string? muadil_kodu { get; set; }
    public decimal satis_fiyati { get; set; }
    public decimal? kdv_orani { get; set; }
    public decimal? kurum_iskonto { get; set; }
    public decimal? eczaci_kari { get; set; }
    public decimal? ticari_iskonto { get; set; }
    public string? mf { get; set; }
    public decimal? depocu_fiyati { get; set; }
    public decimal? net_fiyat_manuel { get; set; }
    public decimal? bakiye { get; set; }
    public string? marka { get; set; }
    public string? grup { get; set; }
    public bool is_active { get; set; } = true;
}





