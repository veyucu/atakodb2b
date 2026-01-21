namespace AtakoErpService.Models
{
    /// <summary>
    /// Web siparişi DTO - Laravel'den gelen veri
    /// </summary>
    public class OrderDto
    {
        public int Id { get; set; }
        public string OrderNumber { get; set; } = string.Empty;
        public string? CariKodu { get; set; }
        public string Tarih { get; set; } = string.Empty;
        public string? GonderimSekli { get; set; }
        public string? Notes { get; set; }
        public decimal Subtotal { get; set; }
        public decimal Vat { get; set; }
        public decimal Total { get; set; }
        public List<OrderItemDto> Items { get; set; } = new();
    }

    /// <summary>
    /// Sipariş kalemi DTO
    /// </summary>
    public class OrderItemDto
    {
        public int ProductId { get; set; }
        public string? UrunKodu { get; set; }
        public string? UrunAdi { get; set; }
        public int Quantity { get; set; }
        public decimal Price { get; set; }
        public decimal NetFiyat { get; set; }
        public decimal VatRate { get; set; }
        public decimal Total { get; set; }
        public string? MalFazlasi { get; set; }
    }

    /// <summary>
    /// Laravel API'den gelen pending orders yanıtı
    /// </summary>
    public class PendingOrdersResponse
    {
        public bool Success { get; set; }
        public int Count { get; set; }
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
}
