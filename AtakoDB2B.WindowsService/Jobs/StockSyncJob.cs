using AtakoDB2B.WindowsService.Services;
using Microsoft.Extensions.Logging;
using Quartz;

namespace AtakoDB2B.WindowsService.Jobs;

[DisallowConcurrentExecution]
public class StockSyncJob : IJob
{
    private readonly ISyncService _syncService;
    private readonly ILogger<StockSyncJob> _logger;

    public StockSyncJob(ISyncService syncService, ILogger<StockSyncJob> logger)
    {
        _syncService = syncService;
        _logger = logger;
    }

    public async Task Execute(IJobExecutionContext context)
    {
        _logger.LogInformation("========== Stok Senkronizasyonu Job Başlatıldı ==========");
        var startTime = DateTime.Now;

        try
        {
            var success = await _syncService.SyncStockAsync();
            
            var duration = DateTime.Now - startTime;
            if (success)
            {
                _logger.LogInformation(
                    "========== Stok Senkronizasyonu BAŞARILI - Süre: {Duration} ==========",
                    duration
                );
            }
            else
            {
                _logger.LogWarning(
                    "========== Stok Senkronizasyonu HATALI - Süre: {Duration} ==========",
                    duration
                );
            }
        }
        catch (Exception ex)
        {
            var duration = DateTime.Now - startTime;
            _logger.LogError(
                ex,
                "========== Stok Senkronizasyonu HATA - Süre: {Duration} ==========",
                duration
            );
            throw;
        }
    }
}







