using System.Text.Json.Serialization;

namespace AtakoErpService.Models
{
    // ========== NetOpenX REST API Models ==========

    /// <summary>
    /// NetOpenX Login Request
    /// </summary>
    public class JLogin
    {
        public int BranchCode { get; set; }
        public string NetsisUser { get; set; } = string.Empty;
        public string NetsisPassword { get; set; } = string.Empty;
        public string DbType { get; set; } = "vtMSSQL";
        public string DbName { get; set; } = string.Empty;
        public string DbPassword { get; set; } = string.Empty;
        public string DbUser { get; set; } = string.Empty;
    }

    /// <summary>
    /// NetOpenX Login Response (OAuth2 token response)
    /// </summary>
    public class LoginResponse
    {
        [JsonPropertyName("access_token")]
        public string? AccessToken { get; set; }
        
        [JsonPropertyName("refresh_token")]
        public string? RefreshToken { get; set; }
        
        [JsonPropertyName("token_type")]
        public string? TokenType { get; set; }
        
        [JsonPropertyName("expires_in")]
        public int? ExpiresIn { get; set; }
        
        [JsonPropertyName("error")]
        public string? Error { get; set; }
        
        [JsonPropertyName("error_description")]
        public string? ErrorDescription { get; set; }
    }

    /// <summary>
    /// ItemSlips - Satış Sipariş/Fatura
    /// </summary>
    public class ItemSlips
    {
        public int FaturaTip { get; set; } = 0; // Belge tipi
        public string? Seri { get; set; } // Numara seri ön eki (örn: "W")
        public bool SeriliHesapla { get; set; } = false;
        public bool KayitliNumaraOtomatikGuncellensin { get; set; } = true;
        public bool OtomatikIslemTipiGetir { get; set; } = false;
        public ItemSlipsHeader FatUst { get; set; } = new();
        public List<ItemSlipLines> Kalems { get; set; } = new();
    }

    /// <summary>
    /// ItemSlipsHeader - Sipariş Üst Bilgileri
    /// </summary>
    public class ItemSlipsHeader
    {
        public string CariKod { get; set; } = string.Empty;
        public string Tarih { get; set; } = string.Empty;
        public string? ENTEGRE_TRH { get; set; }
        public string? SIPARIS_TEST { get; set; }
        public string? FIYATTARIHI { get; set; }
        public string? FiiliTarih { get; set; }
        public int TIPI { get; set; } = 2; // 2 = ft_Acik (Açık Fatura)
        public bool KDV_DAHILMI { get; set; } = true;
        public int Tip { get; set; } = 7; // 7 = ftSSip (Satış Siparişi)
        public string? Aciklama { get; set; }
        public string? EKACK14 { get; set; }
        public string? EKACK15 { get; set; }
        public string? EKACK16 { get; set; }
        public string? FATIRS_NO { get; set; }
    }

    /// <summary>
    /// ItemSlipLines - Sipariş Kalemleri
    /// </summary>
    public class ItemSlipLines
    {
        public string StokKodu { get; set; } = string.Empty;
        public int DEPO_KODU { get; set; } = 0;
        public double STra_GCMIK { get; set; } // Miktar
        public double STra_NF { get; set; } // Net Fiyat
        public double STra_BF { get; set; } // Birim Fiyat
        public int Olcubr { get; set; } = 1;
    }

    /// <summary>
    /// ItemSlips Response
    /// </summary>
    public class ItemSlipsResponse
    {
        public bool IsSuccessful { get; set; }
        public string? ErrorDesc { get; set; }
        public ItemSlipsResponseData? Data { get; set; }
    }

    public class ItemSlipsResponseData
    {
        public ItemSlipsHeader? FatUst { get; set; }
    }

    /// <summary>
    /// ModuleProcessType Parameter
    /// </summary>
    public class ModuleProcessTypeParam
    {
        public string ModuleProcessType { get; set; } = "SS"; // SS = Satış Siparişi
    }

    /// <summary>
    /// ItemSlipsCodeParam - YeniNumara (NewNumber) için parametre
    /// </summary>
    public class ItemSlipsCodeParam
    {
        public string Code { get; set; } = "W"; // Ön ek (W = Web)
        public int DocumentType { get; set; } = 7; // 7 = ftSSip (Satış Siparişi)
        public string? DocumentNumber { get; set; }
        public string? CustomerCode { get; set; }
        public bool Use64BitService { get; set; } = true;
    }

    /// <summary>
    /// NewNumber API Response
    /// </summary>
    public class NewNumberResponse
    {
        public bool IsSuccessful { get; set; }
        public string? ErrorDesc { get; set; }
        public string? Data { get; set; } // Yeni numara
    }
}
