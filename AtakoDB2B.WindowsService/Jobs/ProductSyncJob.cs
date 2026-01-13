using AtakoDB2B.WindowsService.Services;
using Microsoft.Extensions.Logging;
using Quartz;

namespace AtakoDB2B.WindowsService.Jobs;

[DisallowConcurrentExecution]
public class ProductSyncJob : IJob
{
    private readonly ISyncService _syncService;
    private readonly ILogger<ProductSyncJob> _logger;

    public ProductSyncJob(ISyncService syncService, ILogger<ProductSyncJob> logger)
    {
        _syncService = syncService;
        _logger = logger;
    }

    public async Task Execute(IJobExecutionContext context)
    {
        _logger.LogInformation("========== Ürün Senkronizasyonu Job Başlatıldı ==========");
        var startTime = DateTime.Now;

        try
        {
            var success = await _syncService.SyncProductsAsync();
            
            var duration = DateTime.Now - startTime;
            if (success)
            {
                _logger.LogInformation(
                    "========== Ürün Senkronizasyonu BAŞARILI - Süre: {Duration} ==========",
                    duration
                );
            }
            else
            {
                _logger.LogWarning(
                    "========== Ürün Senkronizasyonu HATALI - Süre: {Duration} ==========",
                    duration
                );
            }
        }
        catch (Exception ex)
        {
            var duration = DateTime.Now - startTime;
            _logger.LogError(
                ex,
                "========== Ürün Senkronizasyonu HATA - Süre: {Duration} ==========",
                duration
            );
            throw;
        }
    }
}







