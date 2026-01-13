using System.Net.Http.Json;
using System.Text.Json;
using AtakoDB2B.WindowsService.Models;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;

namespace AtakoDB2B.WindowsService.Services;

public class AtakoDB2BApiService : IAtakoDB2BApiService
{
    private readonly HttpClient _httpClient;
    private readonly ApiConfig _config;
    private readonly ILogger<AtakoDB2BApiService> _logger;
    private string? _authToken;
    private DateTime _tokenExpiry = DateTime.MinValue;

    public AtakoDB2BApiService(
        HttpClient httpClient,
        IOptions<ApiConfig> config,
        ILogger<AtakoDB2BApiService> logger)
    {
        _httpClient = httpClient;
        _config = config.Value;
        _logger = logger;

        _httpClient.BaseAddress = new Uri(_config.BaseUrl);
        _httpClient.Timeout = TimeSpan.FromSeconds(_config.Timeout);
        _httpClient.DefaultRequestHeaders.Add("Accept", "application/json");
    }

    public async Task<bool> LoginAsync()
    {
        try
        {
            // Token hala geçerliyse yeniden login yapma
            if (!string.IsNullOrEmpty(_authToken) && DateTime.Now < _tokenExpiry)
            {
                return true;
            }

            var loginData = new
            {
                email = _config.Email,
                password = _config.Password,
                device_name = _config.DeviceName
            };

            var response = await _httpClient.PostAsJsonAsync("/auth/login", loginData);
            
            if (!response.IsSuccessStatusCode)
            {
                var error = await response.Content.ReadAsStringAsync();
                _logger.LogError("API Login başarısız: {StatusCode} - {Error}", response.StatusCode, error);
                return false;
            }

            var result = await response.Content.ReadFromJsonAsync<LoginResponse>();
            if (result?.Token != null)
            {
                _authToken = result.Token;
                _tokenExpiry = DateTime.Now.AddHours(23); // Token 24 saat geçerli, 23 saat sonra yenile
                
                // Token'ı header'a ekle
                _httpClient.DefaultRequestHeaders.Remove("Authorization");
                _httpClient.DefaultRequestHeaders.Add("Authorization", $"Bearer {_authToken}");
                
                _logger.LogInformation("API Login başarılı");
                return true;
            }

            return false;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "API Login sırasında hata oluştu");
            return false;
        }
    }

    public async Task<SyncResult> SyncUsersAsync(List<ApiUserDto> users)
    {
        try
        {
            await EnsureAuthenticatedAsync();

            var requestData = new { users };
            var response = await _httpClient.PostAsJsonAsync("/users/sync", requestData);
            
            if (!response.IsSuccessStatusCode)
            {
                var error = await response.Content.ReadAsStringAsync();
                _logger.LogError("Kullanıcı sync başarısız: {StatusCode} - {Error}", response.StatusCode, error);
                return new SyncResult
                {
                    Success = false,
                    Message = $"API Error: {response.StatusCode}",
                    Errors = new List<string> { error }
                };
            }

            var result = await response.Content.ReadFromJsonAsync<ApiSyncResponse>();
            
            _logger.LogInformation(
                "Kullanıcı sync başarılı: {Created} oluşturuldu, {Updated} güncellendi",
                result?.Created ?? 0,
                result?.Updated ?? 0
            );

            return new SyncResult
            {
                Success = true,
                Created = result?.Created ?? 0,
                Updated = result?.Updated ?? 0,
                Message = result?.Message ?? "Başarılı",
                Errors = result?.Errors ?? new List<string>()
            };
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Kullanıcı sync sırasında hata oluştu");
            return new SyncResult
            {
                Success = false,
                Message = ex.Message,
                Errors = new List<string> { ex.ToString() }
            };
        }
    }

    public async Task<SyncResult> SyncProductsAsync(List<ApiProductDto> products)
    {
        try
        {
            await EnsureAuthenticatedAsync();

            var requestData = new { products };
            var response = await _httpClient.PostAsJsonAsync("/products/sync", requestData);
            
            if (!response.IsSuccessStatusCode)
            {
                var error = await response.Content.ReadAsStringAsync();
                _logger.LogError("Ürün sync başarısız: {StatusCode} - {Error}", response.StatusCode, error);
                return new SyncResult
                {
                    Success = false,
                    Message = $"API Error: {response.StatusCode}",
                    Errors = new List<string> { error }
                };
            }

            var result = await response.Content.ReadFromJsonAsync<ApiSyncResponse>();
            
            _logger.LogInformation(
                "Ürün sync başarılı: {Created} oluşturuldu, {Updated} güncellendi",
                result?.Created ?? 0,
                result?.Updated ?? 0
            );

            return new SyncResult
            {
                Success = true,
                Created = result?.Created ?? 0,
                Updated = result?.Updated ?? 0,
                Message = result?.Message ?? "Başarılı",
                Errors = result?.Errors ?? new List<string>()
            };
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Ürün sync sırasında hata oluştu");
            return new SyncResult
            {
                Success = false,
                Message = ex.Message,
                Errors = new List<string> { ex.ToString() }
            };
        }
    }

    public async Task<bool> UpdateProductStockAsync(string productCode, decimal stock)
    {
        try
        {
            await EnsureAuthenticatedAsync();

            // Önce ürün koduna göre ürün ID'sini bul
            var response = await _httpClient.GetAsync($"/products/find-by-code?urun_kodu={productCode}");
            
            if (!response.IsSuccessStatusCode)
            {
                _logger.LogWarning("Ürün bulunamadı: {ProductCode}", productCode);
                return false;
            }

            var productResponse = await response.Content.ReadFromJsonAsync<ApiProductResponse>();
            if (productResponse?.Data?.Id == null)
            {
                return false;
            }

            // Stok güncelle
            var stockData = new { bakiye = stock };
            var updateResponse = await _httpClient.PatchAsJsonAsync(
                $"/products/{productResponse.Data.Id}/stock",
                stockData
            );

            if (updateResponse.IsSuccessStatusCode)
            {
                _logger.LogDebug("Stok güncellendi: {ProductCode} -> {Stock}", productCode, stock);
                return true;
            }

            return false;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Stok güncellerken hata: {ProductCode}", productCode);
            return false;
        }
    }

    public async Task<bool> TestConnectionAsync()
    {
        try
        {
            var canLogin = await LoginAsync();
            if (!canLogin)
            {
                return false;
            }

            var response = await _httpClient.GetAsync("/auth/me");
            return response.IsSuccessStatusCode;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "API bağlantı testi başarısız");
            return false;
        }
    }

    private async Task EnsureAuthenticatedAsync()
    {
        if (string.IsNullOrEmpty(_authToken) || DateTime.Now >= _tokenExpiry)
        {
            var success = await LoginAsync();
            if (!success)
            {
                throw new Exception("API authentication failed");
            }
        }
    }

    #region Response Models

    private class LoginResponse
    {
        public string? Token { get; set; }
        public string? Message { get; set; }
    }

    private class ApiSyncResponse
    {
        public string Message { get; set; } = string.Empty;
        public int Created { get; set; }
        public int Updated { get; set; }
        public List<string> Errors { get; set; } = new();
    }

    private class ApiProductResponse
    {
        public ProductData? Data { get; set; }
    }

    private class ProductData
    {
        public int Id { get; set; }
    }

    #endregion
}







