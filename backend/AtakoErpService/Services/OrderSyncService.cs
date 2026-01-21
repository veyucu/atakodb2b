using System.Runtime.InteropServices;
using System.Text.Json;
using AtakoErpService.Models;

namespace AtakoErpService.Services
{
    /// <summary>
    /// Web siparişlerini Netsis ERP'ye aktaran servis
    /// Netsis NetOpenX COM kütüphanesini kullanır
    /// </summary>
    public class OrderSyncService
    {
        private readonly ILogger<OrderSyncService> _logger;
        private readonly IConfiguration _configuration;
        private readonly HttpClient _httpClient;


        // Netsis COM nesneleri
        private dynamic? _kernel;
        private dynamic? _sirket;

        public OrderSyncService(
            ILogger<OrderSyncService> logger,
            IConfiguration configuration)
        {
            _logger = logger;
            _configuration = configuration;
            _httpClient = new HttpClient();


            var laravelBaseUrl = configuration["LaravelApi:BaseUrl"] ?? "http://localhost:8000";
            _httpClient.BaseAddress = new Uri(laravelBaseUrl);
            _httpClient.Timeout = TimeSpan.FromSeconds(30);
        }

        /// <summary>
        /// Pending siparişleri ERP'ye aktar
        /// </summary>
        public async Task<(int success, int failed)> SyncPendingOrdersAsync()
        {
            int successCount = 0;
            int failedCount = 0;

            try
            {
                // 1. Laravel'den pending siparişleri al
                var orders = await GetPendingOrdersAsync();
                if (orders.Count == 0)
                {
                    _logger.LogInformation("Senkronize edilecek sipariş bulunamadı");
                    return (0, 0);
                }

                _logger.LogInformation("{Count} adet sipariş senkronize edilecek", orders.Count);

                // 2. Netsis bağlantısını aç
                if (!InitializeNetsis())
                {
                    _logger.LogError("Netsis bağlantısı açılamadı");
                    return (0, orders.Count);
                }

                // 3. Her siparişi ERP'ye aktar
                foreach (var order in orders)
                {
                    try
                    {
                        var erpOrderNo = CreateOrderInNetsis(order);
                        if (!string.IsNullOrEmpty(erpOrderNo))
                        {
                            // Laravel'de synced olarak işaretle
                            await MarkOrderAsSyncedAsync(order.Id, erpOrderNo);
                            
                            successCount++;
                            _logger.LogInformation("Sipariş senkronize edildi: Web={WebNo}, ERP={ErpNo}", 
                                order.OrderNumber, erpOrderNo);
                        }
                        else
                        {
                            failedCount++;
                        }
                    }
                    catch (Exception ex)
                    {
                        failedCount++;
                        _logger.LogError(ex, "Sipariş senkronize edilemedi: {OrderNo}", order.OrderNumber);
                    }
                }
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Sipariş senkronizasyonu sırasında hata oluştu");
            }
            finally
            {
                // Netsis bağlantısını kapat
                CleanupNetsis();
            }

            return (successCount, failedCount);
        }

        /// <summary>
        /// Laravel API'den pending siparişleri al
        /// </summary>
        private async Task<List<OrderDto>> GetPendingOrdersAsync()
        {
            try
            {
                var response = await _httpClient.GetAsync("/api/erp/orders/pending");
                if (!response.IsSuccessStatusCode)
                {
                    _logger.LogError("Laravel API hatası: {Status}", response.StatusCode);
                    return new List<OrderDto>();
                }

                var json = await response.Content.ReadAsStringAsync();
                var result = JsonSerializer.Deserialize<PendingOrdersResponse>(json, new JsonSerializerOptions
                {
                    PropertyNameCaseInsensitive = true
                });

                return result?.Orders ?? new List<OrderDto>();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Pending siparişler alınırken hata oluştu");
                return new List<OrderDto>();
            }
        }

        /// <summary>
        /// Netsis COM bağlantısını başlat
        /// </summary>
        private bool InitializeNetsis()
        {
            try
            {
                var vtAdi = _configuration["Netsis:VtAdi"];
                var vtKulAdi = _configuration["Netsis:VtKulAdi"];
                var vtKulSifre = _configuration["Netsis:VtKulSifre"] ?? "";
                var netKul = _configuration["Netsis:NetKul"];
                var netSifre = _configuration["Netsis:NetSifre"];
                var subeKod = int.Parse(_configuration["Netsis:SubeKod"] ?? "0");

                // Kernel oluştur - GUID config'den oku
                var kernelGuidStr = _configuration["Netsis:KernelGuid"] ?? "65EB3876-89FF-459F-BF24-02E8DD7F2DB2";
                var kernelGuid = new Guid(kernelGuidStr);
                var kernelType = Type.GetTypeFromCLSID(kernelGuid);
                if (kernelType == null)
                {
                    _logger.LogError("Netsis Kernel COM nesnesi bulunamadı. NetOpenX kurulu mu?");
                    return false;
                }

                _kernel = Activator.CreateInstance(kernelType);
                
                // Şirket bağlantısı
                // TVTTipi.vtMSSQL = 0
                _sirket = _kernel.yeniSirket(0, vtAdi, vtKulAdi, vtKulSifre, netKul, netSifre, subeKod);
                
                _logger.LogInformation("Netsis bağlantısı başarılı: {VtAdi}", vtAdi);
                return true;
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Netsis bağlantısı açılamadı");
                return false;
            }
        }

        /// <summary>
        /// Siparişi Netsis'te oluştur
        /// </summary>
        private string? CreateOrderInNetsis(OrderDto order)
        {
            dynamic? fatura = null;
            dynamic? ust = null;
            dynamic? kalem = null;

            try
            {
                if (_kernel == null || _sirket == null)
                {
                    _logger.LogError("Netsis bağlantısı yok");
                    return null;
                }

                if (string.IsNullOrEmpty(order.CariKodu))
                {
                    _logger.LogWarning("Sipariş {OrderNo} için cari kodu bulunamadı", order.OrderNumber);
                    return null;
                }

                // TFaturaTip.ftSSip = 6 (Satış Siparişi)
                fatura = _kernel.yeniFatura(_sirket, 6);
                ust = fatura.Ust();

                // Sipariş numarası - W prefixli
                ust.FATIRS_NO = fatura.YeniNumara("W");
                ust.CariKod = order.CariKodu;
                
                // Tarihler
                var tarih = DateTime.Parse(order.Tarih);
                ust.Tarih = tarih;
                ust.ENTEGRE_TRH = tarih;
                ust.SIPARIS_TEST = tarih;
                ust.FIYATTARIHI = tarih;
                ust.FiiliTarih = tarih;
                
                // TFaturaTipi.ft_Acik = 0
                ust.TIPI = 0;
                ust.KDV_DAHILMI = true;

                // Açıklama = Gönderim Şekli
                ust.Aciklama = order.GonderimSekli ?? "";
                
                // EKACK14 = Web sipariş numarası
                ust.EKACK14 = $"Web No: {order.OrderNumber}";
                
                // EKACK15/16 = Sipariş notları (100 karakter sınırı)
                if (!string.IsNullOrEmpty(order.Notes))
                {
                    if (order.Notes.Length <= 100)
                    {
                        ust.EKACK15 = order.Notes;
                    }
                    else
                    {
                        ust.EKACK15 = order.Notes.Substring(0, 100);
                        ust.EKACK16 = order.Notes.Length > 200 
                            ? order.Notes.Substring(100, 100) 
                            : order.Notes.Substring(100);
                    }
                }

                // Kalemler
                foreach (var item in order.Items)
                {
                    if (string.IsNullOrEmpty(item.UrunKodu))
                    {
                        _logger.LogWarning("Ürün kodu bulunamadı: ProductId={ProductId}", item.ProductId);
                        continue;
                    }

                    kalem = fatura.kalemYeni(item.UrunKodu);
                    kalem.DEPO_KODU = 0;
                    kalem.STra_GCMIK = (double)item.Quantity;
                    kalem.Olcubr = 1;
                    kalem.STra_NF = (double)item.NetFiyat; // KDV dahil net fiyat
                    kalem.STra_BF = (double)item.NetFiyat; // Birim fiyat

                    // COM nesnesini serbest bırak
                    if (kalem != null)
                    {
                        Marshal.ReleaseComObject(kalem);
                        kalem = null;
                    }
                }

                // Kaydet
                fatura.kayitYeni();
                
                string? erpOrderNo = ust.FATIRS_NO?.ToString();
                _logger.LogInformation("Netsis siparişi oluşturuldu: {ErpNo}", erpOrderNo ?? "N/A");
                
                return erpOrderNo;
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Netsis siparişi oluşturulurken hata: {OrderNo}", order.OrderNumber);
                return null;
            }
            finally
            {
                // COM nesnelerini temizle
                if (kalem != null) Marshal.ReleaseComObject(kalem);
                if (ust != null) Marshal.ReleaseComObject(ust);
                if (fatura != null) Marshal.ReleaseComObject(fatura);
            }
        }

        /// <summary>
        /// Laravel'de siparişi synced olarak işaretle
        /// </summary>
        private async Task MarkOrderAsSyncedAsync(int orderId, string erpOrderNo)
        {
            try
            {
                var content = new StringContent(
                    JsonSerializer.Serialize(new { erp_order_number = erpOrderNo }),
                    System.Text.Encoding.UTF8,
                    "application/json");

                var response = await _httpClient.PutAsync($"/api/erp/orders/{orderId}/synced", content);
                if (!response.IsSuccessStatusCode)
                {
                    _logger.LogWarning("Sipariş synced olarak işaretlenemedi: {OrderId}", orderId);
                }
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "MarkOrderAsSynced hatası: {OrderId}", orderId);
            }
        }

        /// <summary>
        /// Netsis COM nesnelerini temizle
        /// </summary>
        private void CleanupNetsis()
        {
            try
            {
                if (_sirket != null)
                {
                    Marshal.ReleaseComObject(_sirket);
                    _sirket = null;
                }
                if (_kernel != null)
                {
                    Marshal.ReleaseComObject(_kernel);
                    _kernel = null;
                }
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Netsis cleanup hatası");
            }
        }
    }
}
