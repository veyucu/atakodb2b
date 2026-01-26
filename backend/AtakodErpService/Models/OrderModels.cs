using System.Text.Json.Serialization;

namespace AtakoErpService.Models
{
    /// <summary>
    /// Web siparişi DTO - Laravel'den gelen veri
    /// </summary>
    public class OrderDto
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }
        
        [JsonPropertyName("order_number")]
        public string OrderNumber { get; set; } = string.Empty;
        
        [JsonPropertyName("cari_kodu")]
        public string? CariKodu { get; set; }
        
        [JsonPropertyName("tarih")]
        public string Tarih { get; set; } = string.Empty;
        
        [JsonPropertyName("gonderim_sekli")]
        public string? GonderimSekli { get; set; }
        
        [JsonPropertyName("notes")]
        public string? Notes { get; set; }
        
        [JsonPropertyName("subtotal")]
        [JsonNumberHandling(JsonNumberHandling.AllowReadingFromString)]
        public decimal Subtotal { get; set; }
        
        [JsonPropertyName("vat")]
        [JsonNumberHandling(JsonNumberHandling.AllowReadingFromString)]
        public decimal Vat { get; set; }
        
        [JsonPropertyName("total")]
        [JsonNumberHandling(JsonNumberHandling.AllowReadingFromString)]
        public decimal Total { get; set; }
        
        [JsonPropertyName("items")]
        public List<OrderItemDto> Items { get; set; } = new();
    }

    /// <summary>
    /// Sipariş kalemi DTO
    /// </summary>
    public class OrderItemDto
    {
        [JsonPropertyName("product_id")]
        [JsonNumberHandling(JsonNumberHandling.AllowReadingFromString)]
        public int ProductId { get; set; }
        
        [JsonPropertyName("urun_kodu")]
        public string? UrunKodu { get; set; }
        
        [JsonPropertyName("urun_adi")]
        public string? UrunAdi { get; set; }
        
        [JsonPropertyName("quantity")]
        [JsonNumberHandling(JsonNumberHandling.AllowReadingFromString)]
        public int Quantity { get; set; }
        
        [JsonPropertyName("price")]
        [JsonNumberHandling(JsonNumberHandling.AllowReadingFromString)]
        public decimal Price { get; set; }
        
        [JsonPropertyName("net_fiyat")]
        [JsonNumberHandling(JsonNumberHandling.AllowReadingFromString)]
        public decimal NetFiyat { get; set; }
        
        [JsonPropertyName("vat_rate")]
        [JsonNumberHandling(JsonNumberHandling.AllowReadingFromString)]
        public decimal VatRate { get; set; }
        
        [JsonPropertyName("total")]
        [JsonNumberHandling(JsonNumberHandling.AllowReadingFromString)]
        public decimal Total { get; set; }
        
        [JsonPropertyName("mal_fazlasi")]
        public string? MalFazlasi { get; set; }
    }

    /// <summary>
    /// Laravel API'den gelen pending orders yanıtı
    /// </summary>
    public class PendingOrdersResponse
    {
        [JsonPropertyName("success")]
        public bool Success { get; set; }
        
        [JsonPropertyName("count")]
        public int Count { get; set; }
        
        [JsonPropertyName("orders")]
        public List<OrderDto> Orders { get; set; } = new();
    }

    /// <summary>
    /// Sipariş senkronizasyon log kaydı
    /// </summary>
    public class OrderSyncLog
    {
        public string WebSiparisNo { get; set; } = string.Empty;
        public string? ErpSiparisNo { get; set; }
        public string Durum { get; set; } = string.Empty; // "BASARILI", "HATA"
        public string? Mesaj { get; set; }
    }

    /// <summary>
    /// Sipariş senkronizasyon sonucu
    /// </summary>
    public class OrderSyncResult
    {
        public int SuccessCount { get; set; }
        public int FailedCount { get; set; }

        public static OrderSyncResult FromTuple((int success, int failed) result)
        {
            return new OrderSyncResult
            {
                SuccessCount = result.success,
                FailedCount = result.failed
            };
        }
    }
}
