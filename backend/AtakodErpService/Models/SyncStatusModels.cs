namespace AtakoErpService.Models;

/// <summary>
/// Tek bir sync servisinin durumunu temsil eder
/// </summary>
public class SyncServiceStatus
{
    public string ServiceName { get; set; } = string.Empty;
    public string DisplayName { get; set; } = string.Empty;
    public SyncStatus Status { get; set; } = SyncStatus.Idle;
    public DateTime? LastRunTime { get; set; }
    public DateTime? NextRunTime { get; set; }
    public int LastRunDurationMs { get; set; }
    public int SuccessCount { get; set; }
    public int ErrorCount { get; set; }
    public int TotalRecordsProcessed { get; set; }
    public string? LastError { get; set; }
    public DateTime? LastErrorTime { get; set; }
    public List<SubServiceStatus> SubServices { get; set; } = new();
}

/// <summary>
/// Alt servis durumu (DataSync için: Stok, Resim, Bakiye)
/// </summary>
public class SubServiceStatus
{
    public string Name { get; set; } = string.Empty;
    public bool Completed { get; set; }
    public int RecordsProcessed { get; set; }
    public string? Error { get; set; }
}

/// <summary>
/// Sync durumu enum
/// </summary>
public enum SyncStatus
{
    Idle,
    Running,
    Error,
    Disabled
}

/// <summary>
/// Hata kaydı
/// </summary>
public class SyncError
{
    public DateTime Timestamp { get; set; }
    public string ServiceName { get; set; } = string.Empty;
    public string Message { get; set; } = string.Empty;
    public string? StackTrace { get; set; }
}

/// <summary>
/// Dashboard için özet DTO
/// </summary>
public class SyncDashboardDto
{
    public DateTime ServerTime { get; set; }
    public bool BackgroundSyncEnabled { get; set; }
    public List<SyncServiceStatus> Services { get; set; } = new();
    public List<SyncError> RecentErrors { get; set; } = new();
    public SyncStatistics Statistics { get; set; } = new();
}

/// <summary>
/// Genel istatistikler
/// </summary>
public class SyncStatistics
{
    public DateTime ServiceStartTime { get; set; }
    public TimeSpan Uptime => DateTime.Now - ServiceStartTime;
    public int TotalSyncRuns { get; set; }
    public int TotalErrors { get; set; }
    public int TotalRecordsProcessed { get; set; }
}

/// <summary>
/// Manuel tetikleme sonucu
/// </summary>
public class TriggerResult
{
    public bool Success { get; set; }
    public string Message { get; set; } = string.Empty;
    public string? Error { get; set; }
}
