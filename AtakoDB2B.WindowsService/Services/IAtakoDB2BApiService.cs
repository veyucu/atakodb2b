using AtakoDB2B.WindowsService.Models;

namespace AtakoDB2B.WindowsService.Services;

public interface IAtakoDB2BApiService
{
    /// <summary>
    /// API'ye login olup token alır
    /// </summary>
    Task<bool> LoginAsync();

    /// <summary>
    /// Toplu kullanıcı senkronizasyonu
    /// </summary>
    Task<SyncResult> SyncUsersAsync(List<ApiUserDto> users);

    /// <summary>
    /// Toplu ürün senkronizasyonu
    /// </summary>
    Task<SyncResult> SyncProductsAsync(List<ApiProductDto> products);

    /// <summary>
    /// Tek ürünün stok bilgisini günceller
    /// </summary>
    Task<bool> UpdateProductStockAsync(string productCode, decimal stock);

    /// <summary>
    /// Bağlantıyı test eder
    /// </summary>
    Task<bool> TestConnectionAsync();
}

public class SyncResult
{
    public bool Success { get; set; }
    public int Created { get; set; }
    public int Updated { get; set; }
    public List<string> Errors { get; set; } = new();
    public string Message { get; set; } = string.Empty;
}







