using AtakoDB2B.WindowsService.Services;
using Microsoft.Extensions.Logging;
using Quartz;

namespace AtakoDB2B.WindowsService.Jobs;

[DisallowConcurrentExecution]
public class UserSyncJob : IJob
{
    private readonly ISyncService _syncService;
    private readonly ILogger<UserSyncJob> _logger;

    public UserSyncJob(ISyncService syncService, ILogger<UserSyncJob> logger)
    {
        _syncService = syncService;
        _logger = logger;
    }

    public async Task Execute(IJobExecutionContext context)
    {
        _logger.LogInformation("========== Kullanıcı Senkronizasyonu Job Başlatıldı ==========");
        var startTime = DateTime.Now;

        try
        {
            var success = await _syncService.SyncUsersAsync();
            
            var duration = DateTime.Now - startTime;
            if (success)
            {
                _logger.LogInformation(
                    "========== Kullanıcı Senkronizasyonu BAŞARILI - Süre: {Duration} ==========",
                    duration
                );
            }
            else
            {
                _logger.LogWarning(
                    "========== Kullanıcı Senkronizasyonu HATALI - Süre: {Duration} ==========",
                    duration
                );
            }
        }
        catch (Exception ex)
        {
            var duration = DateTime.Now - startTime;
            _logger.LogError(
                ex,
                "========== Kullanıcı Senkronizasyonu HATA - Süre: {Duration} ==========",
                duration
            );
            throw;
        }
    }
}







