using AtakoErpService.Models;

namespace AtakoErpService.BackgroundServices;

/// <summary>
/// Thread 1: Sipariş senkronizasyonu
/// Web siparişlerini Netsis ERP'ye aktarır
/// </summary>
public class OrderSyncBackgroundService : BackgroundService
{
    private readonly ILogger<OrderSyncBackgroundService> _logger;
    private readonly IServiceScopeFactory _scopeFactory;
    private readonly Services.SyncStatusService _statusService;
    private readonly Services.SyncSettingsService _settingsService;

    public OrderSyncBackgroundService(
        ILogger<OrderSyncBackgroundService> logger,
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
        _logger.LogInformation("OrderSyncBackgroundService starting...");

        // İlk bekleme süresi
        var settings = _settingsService.GetSettings();
        await Task.Delay(TimeSpan.FromSeconds(settings.InitialDelaySeconds), stoppingToken);

        _logger.LogInformation("OrderSyncBackgroundService started. Interval: {Interval} minutes", 
            _settingsService.GetServiceInterval("OrderSync"));

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
                _logger.LogError(ex, "OrderSync error");
                _statusService.AddError("OrderSync", ex);
            }

            // Bir sonraki çalışma için bekle
            var intervalMinutes = _settingsService.GetServiceInterval("OrderSync");
            await Task.Delay(TimeSpan.FromMinutes(intervalMinutes), stoppingToken);
        }

        _logger.LogInformation("OrderSyncBackgroundService stopped");
    }

    private async Task DoWorkAsync(CancellationToken stoppingToken)
    {
        var settings = _settingsService.GetSettings();
        
        if (!settings.Enabled)
        {
            _logger.LogDebug("Background sync disabled, skipping OrderSync");
            return;
        }

        var serviceSettings = settings.Services.FirstOrDefault(s => s.Name == "OrderSync");
        if (serviceSettings == null || !serviceSettings.Enabled)
        {
            _logger.LogDebug("OrderSync disabled, skipping");
            return;
        }

        _statusService.UpdateStatus("OrderSync", SyncStatus.Running);
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        int recordsProcessed = 0;

        try
        {
            using var scope = _scopeFactory.CreateScope();
            var orderSync = scope.ServiceProvider.GetRequiredService<Services.OrderSyncService>();

            var tupleResult = await orderSync.SyncPendingOrdersAsync();
            var result = OrderSyncResult.FromTuple(tupleResult);
            recordsProcessed = result.SuccessCount;

            stopwatch.Stop();
            _statusService.UpdateStatus("OrderSync", SyncStatus.Idle, recordsProcessed, (int)stopwatch.ElapsedMilliseconds);
        }
        catch (Exception ex)
        {
            stopwatch.Stop();
            _statusService.AddError("OrderSync", ex);
            throw;
        }
    }
}
