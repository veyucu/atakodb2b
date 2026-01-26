using AtakoErpService.Models;

namespace AtakoErpService.BackgroundServices;

/// <summary>
/// Thread 2: Veri senkronizasyonu
/// Sırayla: StokSync → ResimSync → BakiyeSync
/// </summary>
public class DataSyncBackgroundService : BackgroundService
{
    private readonly ILogger<DataSyncBackgroundService> _logger;
    private readonly IServiceScopeFactory _scopeFactory;
    private readonly Services.SyncStatusService _statusService;
    private readonly Services.SyncSettingsService _settingsService;

    public DataSyncBackgroundService(
        ILogger<DataSyncBackgroundService> logger,
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
        _logger.LogInformation("DataSyncBackgroundService starting...");

        // İlk bekleme süresi
        var settings = _settingsService.GetSettings();
        await Task.Delay(TimeSpan.FromSeconds(settings.InitialDelaySeconds), stoppingToken);

        _logger.LogInformation("DataSyncBackgroundService started. Interval: {Interval} minutes", 
            _settingsService.GetThreadInterval(2));

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
                _logger.LogError(ex, "DataSync error");
                _statusService.AddError("DataSync", ex);
            }

            // Bir sonraki çalışma için bekle
            var intervalMinutes = _settingsService.GetThreadInterval(2);
            await Task.Delay(TimeSpan.FromMinutes(intervalMinutes), stoppingToken);
        }

        _logger.LogInformation("DataSyncBackgroundService stopped");
    }

    private async Task DoWorkAsync(CancellationToken stoppingToken)
    {
        var settings = _settingsService.GetSettings();
        
        if (!settings.Enabled)
        {
            _logger.LogDebug("Background sync disabled, skipping DataSync");
            return;
        }

        _statusService.UpdateStatus("DataSync", SyncStatus.Running);
        _statusService.ResetSubServices("DataSync");
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        int totalRecords = 0;

        try
        {
            using var scope = _scopeFactory.CreateScope();
            
            // Thread 2'deki servisleri sırayla çalıştır
            var thread2Services = _settingsService.GetServicesForThread(2);
            
            foreach (var serviceSettings in thread2Services)
            {
                if (stoppingToken.IsCancellationRequested) break;
                
                try
                {
                    int records = await RunServiceAsync(scope, serviceSettings.Name, stoppingToken);
                    _statusService.UpdateSubServiceStatus("DataSync", serviceSettings.Name, true, records);
                    totalRecords += records;
                }
                catch (Exception ex)
                {
                    _statusService.UpdateSubServiceStatus("DataSync", serviceSettings.Name, false, 0, ex.Message);
                    _logger.LogError(ex, "{Service} failed in DataSync", serviceSettings.Name);
                    // Bir servis hata alırsa diğerlerine devam et
                }
            }

            stopwatch.Stop();
            _statusService.UpdateStatus("DataSync", SyncStatus.Idle, totalRecords, (int)stopwatch.ElapsedMilliseconds);
        }
        catch (Exception ex)
        {
            stopwatch.Stop();
            _statusService.AddError("DataSync", ex);
            throw;
        }
    }

    private async Task<int> RunServiceAsync(IServiceScope scope, string serviceName, CancellationToken stoppingToken)
    {
        _logger.LogInformation("Running {Service}...", serviceName);

        return serviceName switch
        {
            "StokSync" => await RunStokSyncAsync(scope, stoppingToken),
            "ResimSync" => await RunResimSyncAsync(scope, stoppingToken),
            "BakiyeSync" => await RunBakiyeSyncAsync(scope, stoppingToken),
            _ => 0
        };
    }

    private async Task<int> RunStokSyncAsync(IServiceScope scope, CancellationToken stoppingToken)
    {
        var stokSync = scope.ServiceProvider.GetRequiredService<Services.StokSyncService>();
        var result = await stokSync.SyncToLaravelAsync();
        
        // Hataları kaydet
        if (result.Errors.Count > 0)
        {
            foreach (var error in result.Errors.Take(10)) // İlk 10 hatayı kaydet
            {
                _statusService.AddError("StokSync", error);
            }
        }
        
        return result.SuccessCount;
    }

    private async Task<int> RunResimSyncAsync(IServiceScope scope, CancellationToken stoppingToken)
    {
        var resimSync = scope.ServiceProvider.GetRequiredService<Services.ResimSyncService>();
        var result = await resimSync.SyncToLaravelAsync();
        
        // Hataları kaydet
        if (result.Errors.Count > 0)
        {
            foreach (var error in result.Errors.Take(10))
            {
                _statusService.AddError("ResimSync", error);
            }
        }
        
        return result.SuccessCount;
    }

    private async Task<int> RunBakiyeSyncAsync(IServiceScope scope, CancellationToken stoppingToken)
    {
        var bakiyeSync = scope.ServiceProvider.GetRequiredService<Services.BakiyeSyncService>();
        var result = await bakiyeSync.SyncToLaravelAsync();
        
        // Hataları kaydet
        if (result.Errors.Count > 0)
        {
            foreach (var error in result.Errors.Take(10))
            {
                _statusService.AddError("BakiyeSync", error);
            }
        }
        
        return result.SuccessCount;
    }
}
