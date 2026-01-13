using AtakoDB2B.WindowsService.Models;

namespace AtakoDB2B.WindowsService.Services;

public interface INetsisDbService
{
    /// <summary>
    /// Netsis'ten tüm aktif müşterileri çeker
    /// </summary>
    Task<List<NetsisCustomer>> GetCustomersAsync(DateTime? lastSyncDate = null);

    /// <summary>
    /// Netsis'ten tüm aktif ürünleri çeker
    /// </summary>
    Task<List<NetsisProduct>> GetProductsAsync(DateTime? lastSyncDate = null);

    /// <summary>
    /// Netsis'ten güncel stok bilgilerini çeker
    /// </summary>
    Task<List<NetsisStock>> GetStockLevelsAsync(List<string> productCodes);

    /// <summary>
    /// Netsis'ten belirli bir tarihten sonra değişen kayıtları çeker
    /// </summary>
    Task<List<NetsisCustomer>> GetChangedCustomersAsync(DateTime sinceDate);

    /// <summary>
    /// Netsis'ten belirli bir tarihten sonra değişen ürünleri çeker
    /// </summary>
    Task<List<NetsisProduct>> GetChangedProductsAsync(DateTime sinceDate);

    /// <summary>
    /// Bağlantıyı test eder
    /// </summary>
    Task<bool> TestConnectionAsync();
}







