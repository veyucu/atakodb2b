using System.Net.Http.Headers;
using System.Text;
using System.Text.Json;
using AtakoErpService.Models;

namespace AtakoErpService.Services
{
    /// <summary>
    /// Web siparişlerini Netsis ERP'ye aktaran servis
    /// Netsis NetOpenX REST API kullanır
    /// </summary>
    public class OrderSyncService
    {
        private readonly ILogger<OrderSyncService> _logger;
        private readonly IConfiguration _configuration;
        private readonly HttpClient _laravelClient;
        private readonly HttpClient _netsisClient;
        private readonly SyncStatusService _statusService;
        
        private string? _netsisToken;
        private DateTime _tokenExpiry = DateTime.MinValue; // Token cache expiry
        private readonly JsonSerializerOptions _jsonOptions;
        private readonly JsonSerializerOptions _netsisJsonOptions;

        public OrderSyncService(
            ILogger<OrderSyncService> logger,
            IConfiguration configuration,
            IHttpClientFactory httpClientFactory,
            SyncStatusService statusService)
        {
            _logger = logger;
            _configuration = configuration;
            _statusService = statusService;
            
            // Laravel API client - IHttpClientFactory kullan (SSL/TLS düzgün çalışır)
            _laravelClient = httpClientFactory.CreateClient("LaravelApi");
            
            // Netsis REST API client
            _netsisClient = new HttpClient();
            var netsisRestUrl = configuration["Netsis:RestApiUrl"] ?? "http://localhost:7070";
            _netsisClient.BaseAddress = new Uri(netsisRestUrl);
            _netsisClient.Timeout = TimeSpan.FromSeconds(180); // Netsis işlemleri uzun sürebilir
            
            // Laravel API için camelCase
            _jsonOptions = new JsonSerializerOptions
            {
                PropertyNameCaseInsensitive = true,
                PropertyNamingPolicy = JsonNamingPolicy.CamelCase
            };
            
            // Netsis API için PascalCase (NetOpenX bunu bekliyor)
            _netsisJsonOptions = new JsonSerializerOptions
            {
                PropertyNameCaseInsensitive = true
                // PropertyNamingPolicy yok = PascalCase
            };
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

                // 2. Netsis'e login ol
                if (!await LoginToNetsisAsync())
                {
                    _logger.LogError("Netsis REST API'ye giriş yapılamadı");
                    return (0, orders.Count);
                }

                // 3. Her siparişi ERP'ye aktar
                foreach (var order in orders)
                {
                    try
                    {
                        var erpOrderNo = await CreateOrderInNetsisAsync(order);
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

            return (successCount, failedCount);
        }

        /// <summary>
        /// Laravel API'den pending siparişleri al
        /// </summary>
        private async Task<List<OrderDto>> GetPendingOrdersAsync()
        {
            try
            {
                var laravelBaseUrl = _configuration["LaravelApi:BaseUrl"] ?? "http://localhost:8000";
                var apiKey = _configuration["LaravelApi:ApiKey"] ?? "";
                var endpoint = $"{laravelBaseUrl}/api/erp/orders/pending";
                
                _logger.LogInformation("Laravel API çağrılıyor: {Endpoint}, ApiKey uzunluğu: {KeyLength}", endpoint, apiKey?.Length ?? 0);
                
                var request = new HttpRequestMessage(HttpMethod.Get, endpoint);
                request.Headers.Add("X-API-Key", apiKey);
                
                var response = await _laravelClient.SendAsync(request);
                _logger.LogInformation("Laravel API yanıt kodu: {Status}", response.StatusCode);
                
                if (!response.IsSuccessStatusCode)
                {
                    var errorContent = await response.Content.ReadAsStringAsync();
                    var errorMsg = $"Laravel API hatası: HTTP {(int)response.StatusCode} - {errorContent}";
                    _logger.LogError(errorMsg);
                    _statusService.AddError("OrderSync", errorMsg);
                    return new List<OrderDto>();
                }

                var json = await response.Content.ReadAsStringAsync();
                _logger.LogInformation("Laravel API yanıtı: {Length} karakter, İlk 500 karakter: {Preview}", 
                    json.Length, 
                    json.Length > 500 ? json.Substring(0, 500) : json);
                
                var result = JsonSerializer.Deserialize<PendingOrdersResponse>(json, new JsonSerializerOptions
                {
                    PropertyNameCaseInsensitive = true,
                    NumberHandling = System.Text.Json.Serialization.JsonNumberHandling.AllowReadingFromString
                });

                var count = result?.Orders?.Count ?? 0;
                _logger.LogInformation("Parse edilen sipariş sayısı: {Count}", count);
                
                return result?.Orders ?? new List<OrderDto>();
            }
            catch (Exception ex)
            {
                var errorMsg = $"Pending siparişler alınırken hata: {ex.Message}";
                _logger.LogError(ex, "Pending siparişler alınırken hata oluştu");
                _statusService.AddError("OrderSync", errorMsg);
                return new List<OrderDto>();
            }
        }

        /// <summary>
        /// Netsis REST API'ye login ol
        /// </summary>
        private async Task<bool> LoginToNetsisAsync()
        {
            // Token cache kontrolü - hala geçerliyse yeniden login yapma
            if (!string.IsNullOrEmpty(_netsisToken) && DateTime.Now < _tokenExpiry)
            {
                _logger.LogDebug("Cache'de geçerli token mevcut, yeniden login atlanıyor");
                return true;
            }

            try
            {
                var branchCode = _configuration["Netsis:BranchCode"] ?? "0";
                var netsisUser = _configuration["Netsis:NetsisUser"] ?? "";
                var netsisPassword = _configuration["Netsis:NetsisPassword"] ?? "";
                var dbType = _configuration["Netsis:DbType"] ?? "vtMSSQL";
                var dbName = _configuration["Netsis:DbName"] ?? "";
                var dbPassword = _configuration["Netsis:DbPassword"] ?? "";
                var dbUser = _configuration["Netsis:DbUser"] ?? "";

                _logger.LogInformation("Netsis REST API'ye giriş yapılıyor: {DbName}", dbName);

                // OAuth2 token endpoint için form-urlencoded format
                // Delphi örneğindeki parametre isimleri kullanılıyor
                var formData = new Dictionary<string, string>
                {
                    { "grant_type", "password" },
                    { "branchcode", branchCode },
                    { "username", netsisUser },
                    { "password", netsisPassword },
                    { "dbtype", "0" }, // 0 = MSSQL
                    { "dbname", dbName },
                    { "dbuser", dbUser },
                    { "dbpassword", dbPassword }
                };

                var content = new FormUrlEncodedContent(formData);
                var response = await _netsisClient.PostAsync("/api/v2/token", content);
                var json = await response.Content.ReadAsStringAsync();
                
                _logger.LogInformation("Netsis Login Response: {Json}", json);

                if (!response.IsSuccessStatusCode)
                {
                    _logger.LogError("Netsis login hatası: {Status} - {Response}", response.StatusCode, json);
                    return false;
                }

                var loginResponse = JsonSerializer.Deserialize<LoginResponse>(json, _jsonOptions);
                if (!string.IsNullOrEmpty(loginResponse?.AccessToken))
                {
                    _netsisToken = loginResponse.AccessToken;
                    // Token 20 dk geçerli, 18 dk sonra expire et (güvenlik payı)
                    _tokenExpiry = DateTime.Now.AddMinutes(18);
                    _netsisClient.DefaultRequestHeaders.Authorization = 
                        new AuthenticationHeaderValue("Bearer", _netsisToken);
                    
                    _logger.LogInformation("Netsis REST API giriş başarılı (token 18 dk geçerli)");
                    return true;
                }

                _logger.LogError("Netsis login başarısız: {Error}", loginResponse?.Error ?? loginResponse?.ErrorDescription);
                return false;
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Netsis login hatası");
                return false;
            }
        }

        /// <summary>
        /// Netsis'ten yeni sipariş numarası al (YeniNumara("W") karşılığı)
        /// POST /api/v2/ItemSlips/NewNumber
        /// </summary>
        private async Task<string?> GetNewNumberAsync(string prefix = "W", int documentType = 7)
        {
            try
            {
                var param = new ItemSlipsCodeParam
                {
                    Code = prefix,          // "W" = Web siparişleri
                    DocumentType = documentType, // 7 = ftSSip (Satış Siparişi)
                    Use64BitService = true
                };

                var json = JsonSerializer.Serialize(param, _jsonOptions);
                var content = new StringContent(json, Encoding.UTF8, "application/json");
                
                var response = await _netsisClient.PostAsync("/api/v2/ItemSlips/NewNumber", content);
                var responseJson = await response.Content.ReadAsStringAsync();
                
                _logger.LogDebug("NewNumber Response: {Json}", responseJson);
                
                var result = JsonSerializer.Deserialize<NewNumberResponse>(responseJson, _jsonOptions);
                
                if (result?.IsSuccessful == true && !string.IsNullOrEmpty(result.Data))
                {
                    _logger.LogInformation("Yeni numara alındı: {Number}", result.Data);
                    return result.Data;
                }
                
                _logger.LogWarning("Yeni numara alınamadı: {Error}", result?.ErrorDesc);
                return null;
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Yeni numara alınırken hata");
                return null;
            }
        }

        /// <summary>
        /// Siparişi Netsis'te oluştur (REST API)
        /// </summary>
        private async Task<string?> CreateOrderInNetsisAsync(OrderDto order)
        {
            try
            {
                if (string.IsNullOrEmpty(order.CariKodu))
                {
                    _logger.LogWarning("Sipariş {OrderNo} için cari kodu bulunamadı", order.OrderNumber);
                    return null;
                }

                // Sipariş objesi oluştur
                var itemSlips = new ItemSlips
                {                    
                    FatUst = new ItemSlipsHeader
                    {
                        Tip = 7, // ftSSip (Satış Siparişi)
                        //FATIRS_NO = newNumber, // Netsis'ten alınan yeni numara
                        CariKod = order.CariKodu,
                        Tarih = order.Tarih,
                        ENTEGRE_TRH = order.Tarih,
                        SIPARIS_TEST = order.Tarih,
                        FIYATTARIHI = order.Tarih,
                        FiiliTarih = order.Tarih,
                        TIPI = 2, // ft_Acik
                        KDV_DAHILMI = true,
                        Aciklama = order.GonderimSekli ?? "",
                        EKACK14 = order.OrderNumber,
                    },

                    SeriliHesapla = false,
                    Seri = "W",
                    KayitliNumaraOtomatikGuncellensin = true, // Numara kendimiz alıyoruz
                    //KayitliNumaraOtomatikGuncellensin = false, // Numara kendimiz alıyoruz
                    OtomatikIslemTipiGetir = false,                    

                    Kalems = new List<ItemSlipLines>()
                };

                // EKACK15/16 = Sipariş notları
                if (!string.IsNullOrEmpty(order.Notes))
                {
                    if (order.Notes.Length <= 100)
                    {
                        itemSlips.FatUst.EKACK15 = order.Notes;
                    }
                    else
                    {
                        itemSlips.FatUst.EKACK15 = order.Notes.Substring(0, 100);
                        itemSlips.FatUst.EKACK16 = order.Notes.Length > 200 
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

                    itemSlips.Kalems.Add(new ItemSlipLines
                    {
                        StokKodu = item.UrunKodu,
                        DEPO_KODU = 0,
                        STra_GCMIK = (double)item.Quantity,
                        Olcubr = 1,
                        STra_NF = (double)item.NetFiyat,
                        STra_BF = (double)item.NetFiyat,
                        Ekalan1 = item.MalFazlasi  // Mal fazlası bilgisi
                    });
                }

                // API'ye gönder
                var content = new StringContent(
                    JsonSerializer.Serialize(itemSlips, _jsonOptions),
                    Encoding.UTF8,
                    "application/json");

                _logger.LogDebug("Netsis ItemSlips gönderiliyor: {Json}", 
                    JsonSerializer.Serialize(itemSlips, _jsonOptions));

                var response = await _netsisClient.PostAsync("/api/v2/ItemSlips", content);
                var json = await response.Content.ReadAsStringAsync();

                _logger.LogDebug("Netsis ItemSlips Response: {Json}", json);

                var result = JsonSerializer.Deserialize<ItemSlipsResponse>(json, _jsonOptions);
                
                if (result?.IsSuccessful == true)
                {
                    var erpOrderNo = result.Data?.FatUst?.FATIRS_NO ?? "N/A";
                    _logger.LogInformation("Netsis siparişi oluşturuldu: {ErpNo}", erpOrderNo);
                    return erpOrderNo;
                }

                _logger.LogError("Netsis sipariş oluşturma hatası: {Error}", result?.ErrorDesc);
                return null;
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Netsis siparişi oluşturulurken hata: {OrderNo}", order.OrderNumber);
                return null;
            }
        }

        /// <summary>
        /// Laravel'de siparişi synced olarak işaretle
        /// </summary>
        private async Task MarkOrderAsSyncedAsync(int orderId, string erpOrderNo)
        {
            try
            {
                var laravelBaseUrl = _configuration["LaravelApi:BaseUrl"] ?? "http://localhost:8000";
                var apiKey = _configuration["LaravelApi:ApiKey"] ?? "";
                var endpoint = $"{laravelBaseUrl}/api/erp/orders/{orderId}/synced";
                
                var request = new HttpRequestMessage(HttpMethod.Put, endpoint)
                {
                    Content = new StringContent(
                        JsonSerializer.Serialize(new { erp_order_number = erpOrderNo }),
                        Encoding.UTF8,
                        "application/json")
                };
                request.Headers.Add("X-API-Key", apiKey);

                var response = await _laravelClient.SendAsync(request);
                if (!response.IsSuccessStatusCode)
                {
                    var errorContent = await response.Content.ReadAsStringAsync();
                    _logger.LogWarning("Sipariş synced olarak işaretlenemedi: {OrderId} - {Error}", orderId, errorContent);
                }
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "MarkOrderAsSynced hatası: {OrderId}", orderId);
            }
        }
    }
}
