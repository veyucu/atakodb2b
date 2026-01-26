using System.Text.Json;
using System.Text.Json.Serialization;

namespace AtakoErpService.Services;

/// <summary>
/// Runtime'da değiştirilebilen ayarları yöneten servis
/// Ayarlar sync-settings.json dosyasında saklanır
/// </summary>
public class SyncSettingsService
{
    private readonly ILogger<SyncSettingsService> _logger;
    private readonly string _settingsFilePath;
    private SyncSettings _settings;
    private readonly object _lock = new();
    private readonly JsonSerializerOptions _jsonOptions = new()
    {
        WriteIndented = true,
        PropertyNamingPolicy = JsonNamingPolicy.CamelCase,
        Converters = { new JsonStringEnumConverter() }
    };

    public SyncSettingsService(ILogger<SyncSettingsService> logger, IConfiguration config)
    {
        _logger = logger;
        _settingsFilePath = Path.Combine(AppContext.BaseDirectory, "sync-settings.json");
        _settings = LoadSettings(config);
    }

    private SyncSettings LoadSettings(IConfiguration config)
    {
        // Önce dosyadan yükle
        if (File.Exists(_settingsFilePath))
        {
            try
            {
                var json = File.ReadAllText(_settingsFilePath);
                var settings = JsonSerializer.Deserialize<SyncSettings>(json, _jsonOptions);
                if (settings != null)
                {
                    _logger.LogInformation("Settings loaded from {Path}", _settingsFilePath);
                    return settings;
                }
            }
            catch (Exception ex)
            {
                _logger.LogWarning(ex, "Failed to load settings from file, using defaults");
            }
        }

        // Dosya yoksa veya hata varsa appsettings.json'dan oku ve varsayılanları oluştur
        var defaultSettings = new SyncSettings
        {
            Enabled = config.GetValue<bool>("BackgroundSync:Enabled", true),
            InitialDelaySeconds = config.GetValue<int>("BackgroundSync:InitialDelaySeconds", 30),
            LaravelApi = new LaravelApiSettings
            {
                BaseUrl = config.GetValue<string>("LaravelApi:BaseUrl") ?? "http://localhost:8000",
                ApiKey = config.GetValue<string>("LaravelApi:ApiKey") ?? "your-secret-erp-api-key"
            },
            Services = new List<ServiceSettings>
            {
                new()
                {
                    Name = "OrderSync",
                    DisplayName = "Sipariş Senkronizasyonu",
                    Description = "Web siparişlerini Netsis ERP'ye aktarır",
                    Enabled = true,
                    IntervalMinutes = 5,
                    Order = 1,
                    Thread = 1
                },
                new()
                {
                    Name = "StokSync",
                    DisplayName = "Stok Senkronizasyonu",
                    Description = "Netsis stoklarını Laravel'e aktarır",
                    Enabled = true,
                    IntervalMinutes = 5,
                    Order = 1,
                    Thread = 2
                },
                new()
                {
                    Name = "ResimSync",
                    DisplayName = "Resim Senkronizasyonu",
                    Description = "Ürün resimlerini Laravel'e aktarır",
                    Enabled = true,
                    IntervalMinutes = 5,
                    Order = 2,
                    Thread = 2
                },
                new()
                {
                    Name = "BakiyeSync",
                    DisplayName = "Bakiye Senkronizasyonu",
                    Description = "Cari bakiyelerini Laravel'e aktarır",
                    Enabled = true,
                    IntervalMinutes = 5,
                    Order = 3,
                    Thread = 2
                },
                new()
                {
                    Name = "CariSync",
                    DisplayName = "Cari Senkronizasyonu",
                    Description = "Müşteri bilgilerini Laravel'e aktarır",
                    Enabled = true,
                    IntervalMinutes = 5,
                    Order = 1,
                    Thread = 3
                }
            }
        };

        // Varsayılan ayarları dosyaya kaydet
        SaveSettings(defaultSettings);
        return defaultSettings;
    }

    public SyncSettings GetSettings()
    {
        lock (_lock)
        {
            return _settings;
        }
    }

    public void UpdateSettings(SyncSettings newSettings)
    {
        lock (_lock)
        {
            _settings = newSettings;
            SaveSettings(newSettings);
            _logger.LogInformation("Settings updated and saved");
        }
    }

    public void UpdateLaravelApi(LaravelApiSettings apiSettings)
    {
        lock (_lock)
        {
            _settings.LaravelApi = apiSettings;
            SaveSettings(_settings);
            _logger.LogInformation("Laravel API settings updated: {Url}", apiSettings.BaseUrl);
        }
    }

    public void UpdateServiceSettings(string serviceName, ServiceSettings serviceSettings)
    {
        lock (_lock)
        {
            var existing = _settings.Services.FirstOrDefault(s => s.Name == serviceName);
            if (existing != null)
            {
                var index = _settings.Services.IndexOf(existing);
                _settings.Services[index] = serviceSettings;
            }
            else
            {
                _settings.Services.Add(serviceSettings);
            }
            SaveSettings(_settings);
            _logger.LogInformation("Service {Name} settings updated", serviceName);
        }
    }

    public void SetServiceEnabled(string serviceName, bool enabled)
    {
        lock (_lock)
        {
            var service = _settings.Services.FirstOrDefault(s => s.Name == serviceName);
            if (service != null)
            {
                service.Enabled = enabled;
                SaveSettings(_settings);
                _logger.LogInformation("Service {Name} enabled: {Enabled}", serviceName, enabled);
            }
        }
    }

    public void SetGlobalEnabled(bool enabled)
    {
        lock (_lock)
        {
            _settings.Enabled = enabled;
            SaveSettings(_settings);
            _logger.LogInformation("Global sync enabled: {Enabled}", enabled);
        }
    }

    private void SaveSettings(SyncSettings settings)
    {
        try
        {
            var json = JsonSerializer.Serialize(settings, _jsonOptions);
            File.WriteAllText(_settingsFilePath, json);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to save settings to {Path}", _settingsFilePath);
        }
    }

    /// <summary>
    /// Belirli bir thread için çalışacak servisleri sıralı olarak getir
    /// </summary>
    public List<ServiceSettings> GetServicesForThread(int threadNumber)
    {
        lock (_lock)
        {
            return _settings.Services
                .Where(s => s.Thread == threadNumber && s.Enabled)
                .OrderBy(s => s.Order)
                .ToList();
        }
    }

    /// <summary>
    /// Servisin interval süresini getir
    /// </summary>
    public int GetServiceInterval(string serviceName)
    {
        lock (_lock)
        {
            var service = _settings.Services.FirstOrDefault(s => s.Name == serviceName);
            return service?.IntervalMinutes ?? 5;
        }
    }

    /// <summary>
    /// Thread'in interval süresini getir (o thread'deki tüm servisler için ortak)
    /// </summary>
    public int GetThreadInterval(int threadNumber)
    {
        lock (_lock)
        {
            var services = _settings.Services.Where(s => s.Thread == threadNumber).ToList();
            return services.FirstOrDefault()?.IntervalMinutes ?? 5;
        }
    }
}

#region Settings Models

public class SyncSettings
{
    public bool Enabled { get; set; } = true;
    public int InitialDelaySeconds { get; set; } = 30;
    public LaravelApiSettings LaravelApi { get; set; } = new();
    public List<ServiceSettings> Services { get; set; } = new();
}

public class LaravelApiSettings
{
    public string BaseUrl { get; set; } = "http://localhost:8000";
    public string ApiKey { get; set; } = "your-secret-erp-api-key";
}

public class ServiceSettings
{
    public string Name { get; set; } = string.Empty;
    public string DisplayName { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public bool Enabled { get; set; } = true;
    public int IntervalMinutes { get; set; } = 5;
    public int Order { get; set; } = 1;
    public int Thread { get; set; } = 1;
}

#endregion
