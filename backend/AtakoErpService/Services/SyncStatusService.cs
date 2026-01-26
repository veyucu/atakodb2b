using System.Collections.Concurrent;
using System.Text.Json;
using System.Text.Json.Serialization;
using AtakoErpService.Models;

namespace AtakoErpService.Services;

/// <summary>
/// Tüm background sync servislerinin durumunu takip eden Singleton servis
/// Hatalar kalıcı olarak sync-errors.json dosyasında saklanır
/// </summary>
public class SyncStatusService
{
    private readonly ILogger<SyncStatusService> _logger;
    private readonly IConfiguration _config;
    private readonly ConcurrentDictionary<string, SyncServiceStatus> _statuses = new();
    private readonly ConcurrentQueue<SyncError> _recentErrors = new();
    private readonly SyncStatistics _statistics = new();
    private readonly string _errorsFilePath;
    private readonly object _fileLock = new();
    private const int MaxErrorsToKeep = 100;
    private const int MaxErrorsToSave = 500; // Dosyaya kaydedilecek max hata sayısı
    
    private readonly JsonSerializerOptions _jsonOptions = new()
    {
        WriteIndented = true,
        PropertyNamingPolicy = JsonNamingPolicy.CamelCase,
        Converters = { new JsonStringEnumConverter() }
    };

    public SyncStatusService(ILogger<SyncStatusService> logger, IConfiguration config)
    {
        _logger = logger;
        _config = config;
        _errorsFilePath = Path.Combine(AppContext.BaseDirectory, "sync-errors.json");
        _statistics.ServiceStartTime = DateTime.Now;
        
        // Kaydedilmiş hataları yükle
        LoadErrorsFromFile();
        
        // Servisleri başlangıçta kaydet
        InitializeServices();
    }

    private void LoadErrorsFromFile()
    {
        try
        {
            if (File.Exists(_errorsFilePath))
            {
                var json = File.ReadAllText(_errorsFilePath);
                var errors = JsonSerializer.Deserialize<List<SyncError>>(json, _jsonOptions);
                if (errors != null)
                {
                    foreach (var error in errors.OrderBy(e => e.Timestamp))
                    {
                        _recentErrors.Enqueue(error);
                    }
                    _statistics.TotalErrors = errors.Count;
                    _logger.LogInformation("Loaded {Count} errors from file", errors.Count);
                }
            }
        }
        catch (Exception ex)
        {
            _logger.LogWarning(ex, "Failed to load errors from file");
        }
    }

    private void SaveErrorsToFile()
    {
        lock (_fileLock)
        {
            try
            {
                var errorsToSave = _recentErrors.ToList()
                    .OrderByDescending(e => e.Timestamp)
                    .Take(MaxErrorsToSave)
                    .ToList();
                
                var json = JsonSerializer.Serialize(errorsToSave, _jsonOptions);
                File.WriteAllText(_errorsFilePath, json);
            }
            catch (Exception ex)
            {
                _logger.LogWarning(ex, "Failed to save errors to file");
            }
        }
    }

    private void InitializeServices()
    {
        var enabled = _config.GetValue<bool>("BackgroundSync:Enabled", true);
        var status = enabled ? SyncStatus.Idle : SyncStatus.Disabled;

        _statuses["OrderSync"] = new SyncServiceStatus
        {
            ServiceName = "OrderSync",
            DisplayName = "Sipariş Senkronizasyonu",
            Status = status
        };

        _statuses["DataSync"] = new SyncServiceStatus
        {
            ServiceName = "DataSync",
            DisplayName = "Veri Senkronizasyonu",
            Status = status,
            SubServices = new List<SubServiceStatus>
            {
                new() { Name = "StokSync" },
                new() { Name = "ResimSync" },
                new() { Name = "BakiyeSync" }
            }
        };

        _statuses["CariSync"] = new SyncServiceStatus
        {
            ServiceName = "CariSync",
            DisplayName = "Cari Senkronizasyonu",
            Status = status
        };
    }

    /// <summary>
    /// Servis durumunu güncelle
    /// </summary>
    public void UpdateStatus(string serviceName, SyncStatus status, int? recordsProcessed = null, int? durationMs = null)
    {
        if (_statuses.TryGetValue(serviceName, out var serviceStatus))
        {
            serviceStatus.Status = status;
            
            if (status == SyncStatus.Running)
            {
                // Çalışmaya başladı
                _logger.LogInformation("{Service} started", serviceName);
            }
            else if (status == SyncStatus.Idle)
            {
                // Başarıyla tamamlandı
                serviceStatus.LastRunTime = DateTime.Now;
                serviceStatus.SuccessCount++;
                _statistics.TotalSyncRuns++;
                
                if (recordsProcessed.HasValue)
                {
                    serviceStatus.TotalRecordsProcessed += recordsProcessed.Value;
                    _statistics.TotalRecordsProcessed += recordsProcessed.Value;
                }
                
                if (durationMs.HasValue)
                {
                    serviceStatus.LastRunDurationMs = durationMs.Value;
                }
                
                // Bir sonraki çalışma zamanını hesapla
                var intervalMinutes = GetIntervalMinutes(serviceName);
                serviceStatus.NextRunTime = DateTime.Now.AddMinutes(intervalMinutes);
                
                _logger.LogInformation("{Service} completed. Records: {Records}, Duration: {Duration}ms", 
                    serviceName, recordsProcessed ?? 0, durationMs ?? 0);
            }
        }
    }

    /// <summary>
    /// Alt servis durumunu güncelle (DataSync için)
    /// </summary>
    public void UpdateSubServiceStatus(string parentService, string subServiceName, bool completed, int recordsProcessed = 0, string? error = null)
    {
        if (_statuses.TryGetValue(parentService, out var serviceStatus))
        {
            var subService = serviceStatus.SubServices.FirstOrDefault(s => s.Name == subServiceName);
            if (subService != null)
            {
                subService.Completed = completed;
                subService.RecordsProcessed = recordsProcessed;
                subService.Error = error;
                
                // Alt servis hatası varsa ana hatalar listesine de ekle
                if (!string.IsNullOrEmpty(error))
                {
                    AddErrorInternal(subServiceName, error, null);
                }
            }
        }
    }

    /// <summary>
    /// Alt servisleri sıfırla (yeni sync döngüsü başlarken)
    /// </summary>
    public void ResetSubServices(string parentService)
    {
        if (_statuses.TryGetValue(parentService, out var serviceStatus))
        {
            foreach (var sub in serviceStatus.SubServices)
            {
                sub.Completed = false;
                sub.RecordsProcessed = 0;
                sub.Error = null;
            }
        }
    }

    /// <summary>
    /// Hata kaydet (Exception ile)
    /// </summary>
    public void AddError(string serviceName, Exception ex)
    {
        AddErrorInternal(serviceName, ex.Message, ex.StackTrace);
        
        // Servis durumunu güncelle
        if (_statuses.TryGetValue(serviceName, out var serviceStatus))
        {
            serviceStatus.Status = SyncStatus.Error;
            serviceStatus.ErrorCount++;
            serviceStatus.LastError = ex.Message;
            serviceStatus.LastErrorTime = DateTime.Now;
            
            // Bir sonraki çalışma zamanını hesapla (hata olsa bile devam edecek)
            var intervalMinutes = GetIntervalMinutes(serviceName);
            serviceStatus.NextRunTime = DateTime.Now.AddMinutes(intervalMinutes);
        }

        _logger.LogError(ex, "{Service} error: {Message}", serviceName, ex.Message);
    }

    /// <summary>
    /// Hata kaydet (string mesaj ile)
    /// </summary>
    public void AddError(string serviceName, string message)
    {
        AddErrorInternal(serviceName, message, null);
        
        if (_statuses.TryGetValue(serviceName, out var serviceStatus))
        {
            serviceStatus.ErrorCount++;
            serviceStatus.LastError = message;
            serviceStatus.LastErrorTime = DateTime.Now;
        }

        _logger.LogError("{Service} error: {Message}", serviceName, message);
    }

    private void AddErrorInternal(string serviceName, string message, string? stackTrace)
    {
        var error = new SyncError
        {
            Timestamp = DateTime.Now,
            ServiceName = serviceName,
            Message = message,
            StackTrace = stackTrace
        };

        _recentErrors.Enqueue(error);
        _statistics.TotalErrors++;

        // Max error sayısını aşarsa eski hataları sil (memory'den)
        while (_recentErrors.Count > MaxErrorsToKeep)
        {
            _recentErrors.TryDequeue(out _);
        }

        // Dosyaya kaydet
        SaveErrorsToFile();
    }

    /// <summary>
    /// Dashboard verilerini getir
    /// </summary>
    public SyncDashboardDto GetDashboard()
    {
        return new SyncDashboardDto
        {
            ServerTime = DateTime.Now,
            BackgroundSyncEnabled = _config.GetValue<bool>("BackgroundSync:Enabled", true),
            Services = _statuses.Values.ToList(),
            RecentErrors = _recentErrors.OrderByDescending(e => e.Timestamp).Take(50).ToList(),
            Statistics = _statistics
        };
    }

    /// <summary>
    /// Tüm hataları getir (dosyadan)
    /// </summary>
    public List<SyncError> GetAllErrors()
    {
        try
        {
            if (File.Exists(_errorsFilePath))
            {
                var json = File.ReadAllText(_errorsFilePath);
                var errors = JsonSerializer.Deserialize<List<SyncError>>(json, _jsonOptions);
                return errors?.OrderByDescending(e => e.Timestamp).ToList() ?? new List<SyncError>();
            }
        }
        catch (Exception ex)
        {
            _logger.LogWarning(ex, "Failed to read errors from file");
        }
        
        return _recentErrors.OrderByDescending(e => e.Timestamp).ToList();
    }

    /// <summary>
    /// Tek bir servisin durumunu getir
    /// </summary>
    public SyncServiceStatus? GetServiceStatus(string serviceName)
    {
        _statuses.TryGetValue(serviceName, out var status);
        return status;
    }

    /// <summary>
    /// Servis şu anda çalışıyor mu?
    /// </summary>
    public bool IsServiceRunning(string serviceName)
    {
        if (_statuses.TryGetValue(serviceName, out var status))
        {
            return status.Status == SyncStatus.Running;
        }
        return false;
    }

    private int GetIntervalMinutes(string serviceName)
    {
        return serviceName switch
        {
            "OrderSync" => _config.GetValue<int>("BackgroundSync:OrderSyncIntervalMinutes", 5),
            "DataSync" => _config.GetValue<int>("BackgroundSync:DataSyncIntervalMinutes", 5),
            "CariSync" => _config.GetValue<int>("BackgroundSync:CariSyncIntervalMinutes", 5),
            _ => 5
        };
    }
}

