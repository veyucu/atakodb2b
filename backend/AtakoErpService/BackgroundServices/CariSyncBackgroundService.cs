using AtakoErpService.Models;

namespace AtakoErpService.BackgroundServices;

/// <summary>
/// Thread 3: Cari senkronizasyonu
/// Müşteri bilgilerini Laravel'e aktarır
/// </summary>
public class CariSyncBackgroundService : BackgroundService
{
    private readonly ILogger<CariSyncBackgroundService> _logger;
    private readonly IServiceScopeFactory _scopeFactory;
    private readonly Services.SyncStatusService _statusService;
    private readonly Services.SyncSettingsService _settingsService;

    public CariSyncBackgroundService(
        ILogger<CariSyncBackgroundService> logger,
        IServiceScopeFactory scopeFactory,
        Services.SyncStatusService statusService,
        Services.SyncSettingsService settingsService)
    {
        _logger = logger;
        _scopeFactory = scopeFactory;
        _statusService = statusService;
        _settingsService = settingsService;
    }

    protected override async Task ExecuteAsync(CancellationToken stoppingToken)
    {
        _logger.LogInformation("CariSyncBackgroundService starting...");

        // İlk bekleme süresi
        var settings = _settingsService.GetSettings();
        await Task.Delay(TimeSpan.FromSeconds(settings.InitialDelaySeconds), stoppingToken);

        _logger.LogInformation("CariSyncBackgroundService started. Interval: {Interval} minutes", 
            _settingsService.GetServiceInterval("CariSync"));

        while (!stoppingToken.IsCancellationRequested)
        {
            try
            {
                await DoWorkAsync(stoppingToken);
            }
            catch (OperationCanceledException)
            {
                // Graceful shutdown
                break;
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "CariSync error");
                _statusService.AddError("CariSync", ex);
            }

            // Bir sonraki çalışma için bekle
            var intervalMinutes = _settingsService.GetServiceInterval("CariSync");
            await Task.Delay(TimeSpan.FromMinutes(intervalMinutes), stoppingToken);
        }

        _logger.LogInformation("CariSyncBackgroundService stopped");
    }

    private async Task DoWorkAsync(CancellationToken stoppingToken)
    {
        var settings = _settingsService.GetSettings();
        
        if (!settings.Enabled)
        {
            _logger.LogDebug("Background sync disabled, skipping CariSync");
            return;
        }

        var serviceSettings = settings.Services.FirstOrDefault(s => s.Name == "CariSync");
        if (serviceSettings == null || !serviceSettings.Enabled)
        {
            _logger.LogDebug("CariSync disabled, skipping");
            return;
        }

        _statusService.UpdateStatus("CariSync", SyncStatus.Running);
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        int recordsProcessed = 0;

        try
        {
            using var scope = _scopeFactory.CreateScope();
            var cariSync = scope.ServiceProvider.GetRequiredService<Services.ICariSyncService>();

            var result = await cariSync.SyncToLaravelAsync();
            recordsProcessed = result.SuccessCount;
            
            // Debug: Hata sayısını logla
            _logger.LogInformation(
                "CariSync tamamlandı. Başarılı: {Success}, Hata: {Error}, Errors.Count: {ErrorsCount}",
                result.SuccessCount, result.ErrorCount, result.Errors.Count);
            
            // Hataları kaydet (ErrorCount veya Errors listesinden hangisi doluysa)
            if (result.ErrorCount > 0 || result.Errors.Count > 0)
            {
                foreach (var error in result.Errors.Take(10))
                {
                    _statusService.AddError("CariSync", error);
                    _logger.LogWarning("Dashboard'a hata eklendi: {Error}", error);
                }
                
                // Eğer Errors listesi boş ama ErrorCount > 0 ise genel bir hata ekle
                if (result.Errors.Count == 0 && result.ErrorCount > 0)
                {
                    _statusService.AddError("CariSync", $"Toplam {result.ErrorCount} adet senkronizasyon hatası oluştu");
                }
            }

            stopwatch.Stop();
            _statusService.UpdateStatus("CariSync", SyncStatus.Idle, recordsProcessed, (int)stopwatch.ElapsedMilliseconds);
        }
        catch (Exception ex)
        {
            stopwatch.Stop();
            _statusService.AddError("CariSync", ex);
            throw;
        }
    }
}
