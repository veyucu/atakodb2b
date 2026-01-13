namespace AtakoDB2B.WindowsService.Services;

public interface ISyncService
{
    Task<bool> SyncUsersAsync();
    Task<bool> SyncProductsAsync();
    Task<bool> SyncStockAsync();
}







