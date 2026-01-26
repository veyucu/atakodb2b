using Microsoft.AspNetCore.Mvc;
using AtakoErpService.Models;
using AtakoErpService.Services;
using System.Text;

namespace AtakoErpService.Controllers;

[ApiController]
[Route("api/[controller]")]
public class SyncStatusController : ControllerBase
{
    private readonly ILogger<SyncStatusController> _logger;
    private readonly SyncStatusService _statusService;
    private readonly SyncSettingsService _settingsService;
    private readonly IServiceScopeFactory _scopeFactory;

    public SyncStatusController(
        ILogger<SyncStatusController> logger,
        SyncStatusService statusService,
        SyncSettingsService settingsService,
        IServiceScopeFactory scopeFactory)
    {
        _logger = logger;
        _statusService = statusService;
        _settingsService = settingsService;
        _scopeFactory = scopeFactory;
    }

    /// <summary>
    /// Tüm sync servislerinin durumunu JSON olarak döner
    /// </summary>
    [HttpGet]
    public ActionResult<SyncDashboardDto> GetStatus()
    {
        return Ok(_statusService.GetDashboard());
    }

    /// <summary>
    /// Ayarları getir
    /// </summary>
    [HttpGet("settings")]
    public ActionResult<SyncSettings> GetSettings()
    {
        return Ok(_settingsService.GetSettings());
    }

    /// <summary>
    /// Ayarları güncelle
    /// </summary>
    [HttpPost("settings")]
    public ActionResult UpdateSettings([FromBody] SyncSettings settings)
    {
        _settingsService.UpdateSettings(settings);
        return Ok(new { success = true, message = "Ayarlar güncellendi" });
    }

    /// <summary>
    /// Laravel API ayarlarını güncelle
    /// </summary>
    [HttpPost("settings/api")]
    public ActionResult UpdateApiSettings([FromBody] LaravelApiSettings apiSettings)
    {
        _settingsService.UpdateLaravelApi(apiSettings);
        return Ok(new { success = true, message = "API ayarları güncellendi" });
    }

    /// <summary>
    /// Tek bir servisin ayarlarını güncelle
    /// </summary>
    [HttpPost("settings/service/{serviceName}")]
    public ActionResult UpdateServiceSettings(string serviceName, [FromBody] ServiceSettings serviceSettings)
    {
        _settingsService.UpdateServiceSettings(serviceName, serviceSettings);
        return Ok(new { success = true, message = $"{serviceName} ayarları güncellendi" });
    }

    /// <summary>
    /// Global sync'i aç/kapat
    /// </summary>
    [HttpPost("toggle")]
    public ActionResult ToggleSync([FromBody] ToggleRequest request)
    {
        _settingsService.SetGlobalEnabled(request.Enabled);
        return Ok(new { success = true, enabled = request.Enabled });
    }

    /// <summary>
    /// Tek bir servisi aç/kapat
    /// </summary>
    [HttpPost("toggle/{serviceName}")]
    public ActionResult ToggleService(string serviceName, [FromBody] ToggleRequest request)
    {
        _settingsService.SetServiceEnabled(serviceName, request.Enabled);
        return Ok(new { success = true, serviceName, enabled = request.Enabled });
    }

    /// <summary>
    /// Servisi manuel tetikle
    /// </summary>
    [HttpPost("trigger/{serviceName}")]
    public async Task<ActionResult<TriggerResult>> TriggerSync(string serviceName)
    {
        // Zaten çalışıyorsa hata dön
        if (_statusService.IsServiceRunning(serviceName))
        {
            return BadRequest(new TriggerResult
            {
                Success = false,
                Message = $"{serviceName} zaten çalışıyor"
            });
        }

        try
        {
            _logger.LogInformation("Manual trigger: {Service}", serviceName);
            
            using var scope = _scopeFactory.CreateScope();
            int recordsProcessed = 0;

            switch (serviceName.ToLower())
            {
                case "ordersync":
                    _statusService.UpdateStatus("OrderSync", SyncStatus.Running);
                    var orderSync = scope.ServiceProvider.GetRequiredService<OrderSyncService>();
                    var orderTuple = await orderSync.SyncPendingOrdersAsync();
                    var orderResult = OrderSyncResult.FromTuple(orderTuple);
                    recordsProcessed = orderResult.SuccessCount;
                    _statusService.UpdateStatus("OrderSync", SyncStatus.Idle, recordsProcessed);
                    break;

                case "stoksync":
                    var stokSync = scope.ServiceProvider.GetRequiredService<StokSyncService>();
                    var stokResult = await stokSync.SyncToLaravelAsync();
                    recordsProcessed = stokResult.SuccessCount;
                    break;

                case "resimsync":
                    var resimSync = scope.ServiceProvider.GetRequiredService<ResimSyncService>();
                    var resimResult = await resimSync.SyncToLaravelAsync();
                    recordsProcessed = resimResult.SuccessCount;
                    break;

                case "bakiyesync":
                    var bakiyeSync = scope.ServiceProvider.GetRequiredService<BakiyeSyncService>();
                    var bakiyeResult = await bakiyeSync.SyncToLaravelAsync();
                    recordsProcessed = bakiyeResult.SuccessCount;
                    break;

                case "carisync":
                    _statusService.UpdateStatus("CariSync", SyncStatus.Running);
                    var cariSync = scope.ServiceProvider.GetRequiredService<ICariSyncService>();
                    var cariResult = await cariSync.SyncToLaravelAsync();
                    recordsProcessed = cariResult.SuccessCount;
                    
                    // Hataları dashboard'a kaydet
                    if (cariResult.ErrorCount > 0 || cariResult.Errors.Count > 0)
                    {
                        foreach (var error in cariResult.Errors.Take(10))
                        {
                            _statusService.AddError("CariSync", error);
                        }
                    }
                    
                    _statusService.UpdateStatus("CariSync", SyncStatus.Idle, recordsProcessed);
                    break;


                case "datasync":
                    // Tüm Thread 2 servislerini sırayla çalıştır
                    _statusService.UpdateStatus("DataSync", SyncStatus.Running);
                    _statusService.ResetSubServices("DataSync");
                    
                    var stok = scope.ServiceProvider.GetRequiredService<StokSyncService>();
                    var stokRes = await stok.SyncToLaravelAsync();
                    _statusService.UpdateSubServiceStatus("DataSync", "StokSync", true, stokRes.SuccessCount);
                    recordsProcessed += stokRes.SuccessCount;

                    var resim = scope.ServiceProvider.GetRequiredService<ResimSyncService>();
                    var resimRes = await resim.SyncToLaravelAsync();
                    _statusService.UpdateSubServiceStatus("DataSync", "ResimSync", true, resimRes.SuccessCount);
                    recordsProcessed += resimRes.SuccessCount;

                    var bakiye = scope.ServiceProvider.GetRequiredService<BakiyeSyncService>();
                    var bakiyeRes = await bakiye.SyncToLaravelAsync();
                    _statusService.UpdateSubServiceStatus("DataSync", "BakiyeSync", true, bakiyeRes.SuccessCount);
                    recordsProcessed += bakiyeRes.SuccessCount;

                    _statusService.UpdateStatus("DataSync", SyncStatus.Idle, recordsProcessed);
                    break;

                default:
                    return NotFound(new TriggerResult
                    {
                        Success = false,
                        Message = $"Bilinmeyen servis: {serviceName}"
                    });
            }

            return Ok(new TriggerResult
            {
                Success = true,
                Message = $"{serviceName} başarıyla tamamlandı. {recordsProcessed} kayıt işlendi."
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Manual trigger failed: {Service}", serviceName);
            _statusService.AddError(serviceName, ex);
            
            return StatusCode(500, new TriggerResult
            {
                Success = false,
                Message = "Senkronizasyon hatası",
                Error = ex.Message
            });
        }
    }

    /// <summary>
    /// Son hataları getir
    /// </summary>
    [HttpGet("errors")]
    public ActionResult<List<SyncError>> GetErrors([FromQuery] int count = 50)
    {
        var dashboard = _statusService.GetDashboard();
        return Ok(dashboard.RecentErrors.Take(count).ToList());
    }

    /// <summary>
    /// HTML Dashboard sayfası
    /// </summary>
    [HttpGet("dashboard")]
    [Produces("text/html")]
    public ContentResult GetDashboard()
    {
        var html = GenerateDashboardHtml();
        return Content(html, "text/html", Encoding.UTF8);
    }

    private string GenerateDashboardHtml()
    {
        return @"<!DOCTYPE html>
<html lang=""tr"">
<head>
    <meta charset=""UTF-8"">
    <meta name=""viewport"" content=""width=device-width, initial-scale=1.0"">
    <title>Sync Status Dashboard</title>
    <style>
        :root {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --accent-blue: #3b82f6;
            --accent-green: #22c55e;
            --accent-yellow: #eab308;
            --accent-red: #ef4444;
            --border-color: #475569;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container { max-width: 1400px; margin: 0 auto; }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }
        
        h1 {
            font-size: 1.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-idle { background: rgba(34, 197, 94, 0.2); color: var(--accent-green); }
        .status-running { background: rgba(234, 179, 8, 0.2); color: var(--accent-yellow); }
        .status-error { background: rgba(239, 68, 68, 0.2); color: var(--accent-red); }
        .status-disabled { background: rgba(100, 116, 139, 0.2); color: var(--text-secondary); }
        
        .toggle-btn {
            background: var(--accent-blue);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .toggle-btn:hover { background: #2563eb; }
        .toggle-btn.off { background: var(--accent-red); }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 24px; }
        
        .card {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border-color);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .service-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: var(--bg-tertiary);
            border-radius: 8px;
            margin-bottom: 8px;
        }
        
        .service-info h3 { font-size: 0.95rem; margin-bottom: 4px; }
        .service-info p { font-size: 0.8rem; color: var(--text-secondary); }
        
        .service-actions { display: flex; gap: 8px; align-items: center; }
        
        .trigger-btn {
            background: var(--bg-primary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s;
        }
        
        .trigger-btn:hover { background: var(--accent-blue); border-color: var(--accent-blue); }
        .trigger-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        
        .sub-services {
            margin-left: 24px;
            margin-top: 8px;
            padding-left: 12px;
            border-left: 2px solid var(--border-color);
        }
        
        .sub-service {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            font-size: 0.85rem;
        }
        
        .sub-service .check { color: var(--accent-green); }
        .sub-service .pending { color: var(--text-secondary); }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .stat-card {
            background: var(--bg-secondary);
            padding: 16px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid var(--border-color);
        }
        
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--accent-blue); }
        .stat-label { font-size: 0.75rem; color: var(--text-secondary); margin-top: 4px; }
        
        .errors-card { grid-column: 1 / -1; }
        
        .error-row {
            display: flex;
            gap: 12px;
            padding: 10px;
            background: rgba(239, 68, 68, 0.1);
            border-radius: 6px;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }
        
        .error-time { color: var(--text-secondary); white-space: nowrap; }
        .error-service { color: var(--accent-red); font-weight: 500; }
        
        .settings-section { margin-top: 24px; }
        
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 16px;
        }
        
        .input-group { margin-bottom: 12px; }
        .input-group label { display: block; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 4px; }
        .input-group input, .input-group select {
            width: 100%;
            padding: 8px 12px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 0.9rem;
        }
        
        .save-btn {
            background: var(--accent-green);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 12px;
        }
        
        .save-btn:hover { background: #16a34a; }
        
        .refresh-info {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.8rem;
            margin-top: 20px;
        }

        .tab-buttons {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .tab-btn {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: var(--accent-blue);
            color: white;
            border-color: var(--accent-blue);
        }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .running-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-yellow);
            animation: pulse 1s infinite;
        }
    </style>
</head>
<body>
    <div class=""container"">
        <header>
            <h1>
                <span>⚡</span> Sync Status Dashboard
            </h1>
            <div style=""display: flex; align-items: center; gap: 16px;"">
                <span id=""serverTime"" style=""color: var(--text-secondary); font-size: 0.9rem;""></span>
                <button id=""globalToggle"" class=""toggle-btn"" onclick=""toggleGlobalSync()"">
                    Sync Aktif
                </button>
            </div>
        </header>

        <div class=""tab-buttons"">
            <button class=""tab-btn active"" onclick=""showTab('status')"">📊 Durum</button>
            <button class=""tab-btn"" onclick=""showTab('settings')"">⚙️ Ayarlar</button>
            <button class=""tab-btn"" onclick=""showTab('errors')"">⚠️ Hatalar</button>
        </div>

        <!-- Status Tab -->
        <div id=""statusTab"" class=""tab-content active"">
            <div class=""stats-grid"">
                <div class=""stat-card"">
                    <div class=""stat-value"" id=""statUptime"">-</div>
                    <div class=""stat-label"">Çalışma Süresi</div>
                </div>
                <div class=""stat-card"">
                    <div class=""stat-value"" id=""statRuns"">0</div>
                    <div class=""stat-label"">Toplam Çalışma</div>
                </div>
                <div class=""stat-card"">
                    <div class=""stat-value"" id=""statRecords"">0</div>
                    <div class=""stat-label"">Toplam Kayıt</div>
                </div>
                <div class=""stat-card"">
                    <div class=""stat-value"" id=""statErrors"">0</div>
                    <div class=""stat-label"">Toplam Hata</div>
                </div>
            </div>

            <div class=""grid"" id=""servicesGrid"">
                <!-- Services will be rendered here -->
            </div>
        </div>

        <!-- Settings Tab -->
        <div id=""settingsTab"" class=""tab-content"">
            <div class=""settings-grid"">
                <div class=""card"">
                    <h3 class=""card-title"" style=""margin-bottom: 16px;"">🔗 Laravel API Ayarları</h3>
                    <div class=""input-group"">
                        <label>API Base URL</label>
                        <input type=""text"" id=""apiBaseUrl"" placeholder=""http://localhost:8000"">
                    </div>
                    <div class=""input-group"">
                        <label>API Key</label>
                        <input type=""password"" id=""apiKey"" placeholder=""your-secret-api-key"">
                    </div>
                    <button class=""save-btn"" onclick=""saveApiSettings()"">💾 API Ayarlarını Kaydet</button>
                </div>

                <div class=""card"">
                    <h3 class=""card-title"" style=""margin-bottom: 16px;"">⏱️ Genel Ayarlar</h3>
                    <div class=""input-group"">
                        <label>İlk Bekleme Süresi (saniye)</label>
                        <input type=""number"" id=""initialDelay"" min=""0"" max=""300"">
                    </div>
                    <button class=""save-btn"" onclick=""saveGeneralSettings()"">💾 Genel Ayarları Kaydet</button>
                </div>
            </div>

            <div class=""card"" style=""margin-top: 20px;"">
                <h3 class=""card-title"" style=""margin-bottom: 16px;"">📋 Servis Ayarları</h3>
                <div id=""serviceSettingsGrid"" class=""settings-grid"">
                    <!-- Service settings will be rendered here -->
                </div>
            </div>
        </div>

        <!-- Errors Tab -->
        <div id=""errorsTab"" class=""tab-content"">
            <div class=""card errors-card"">
                <div class=""card-header"">
                    <h3 class=""card-title"">⚠️ Son Hatalar</h3>
                </div>
                <div id=""errorsList"">
                    <!-- Errors will be rendered here -->
                </div>
            </div>
        </div>

        <p class=""refresh-info"">Sayfa her 10 saniyede otomatik yenilenir</p>
    </div>

    <script>
        let currentSettings = null;
        let globalEnabled = true;

        async function fetchStatus() {
            try {
                const response = await fetch('/api/SyncStatus');
                const data = await response.json();
                updateUI(data);
            } catch (error) {
                console.error('Fetch error:', error);
            }
        }

        async function fetchSettings() {
            try {
                const response = await fetch('/api/SyncStatus/settings');
                currentSettings = await response.json();
                updateSettingsUI(currentSettings);
            } catch (error) {
                console.error('Settings fetch error:', error);
            }
        }

        function updateUI(data) {
            // Server time
            document.getElementById('serverTime').textContent = new Date(data.serverTime).toLocaleTimeString('tr-TR');
            
            // Global toggle
            globalEnabled = data.backgroundSyncEnabled;
            const toggleBtn = document.getElementById('globalToggle');
            toggleBtn.textContent = globalEnabled ? 'Sync Aktif' : 'Sync Pasif';
            toggleBtn.className = 'toggle-btn' + (globalEnabled ? '' : ' off');

            // Stats
            const uptime = data.statistics?.uptime || '00:00:00';
            const parts = uptime.split(':');
            const hours = parseInt(parts[0]) || 0;
            const minutes = parseInt(parts[1]) || 0;
            document.getElementById('statUptime').textContent = hours > 0 ? `${hours}s ${minutes}d` : `${minutes}d`;
            document.getElementById('statRuns').textContent = data.statistics?.totalSyncRuns || 0;
            document.getElementById('statRecords').textContent = data.statistics?.totalRecordsProcessed || 0;
            document.getElementById('statErrors').textContent = data.statistics?.totalErrors || 0;

            // Services
            const grid = document.getElementById('servicesGrid');
            grid.innerHTML = data.services.map(service => renderServiceCard(service)).join('');

            // Errors
            const errorsList = document.getElementById('errorsList');
            if (data.recentErrors && data.recentErrors.length > 0) {
                errorsList.innerHTML = data.recentErrors.map(error => `
                    <div class=""error-row"">
                        <span class=""error-time"">${new Date(error.timestamp).toLocaleString('tr-TR')}</span>
                        <span class=""error-service"">${error.serviceName}</span>
                        <span>${error.message}</span>
                    </div>
                `).join('');
            } else {
                errorsList.innerHTML = '<p style=""color: var(--text-secondary); text-align: center; padding: 20px;"">Hata kaydı yok</p>';
            }
        }

        function renderServiceCard(service) {
            const statusClass = 'status-' + service.status.toLowerCase();
            const statusText = {
                'Idle': 'Bekliyor',
                'Running': 'Çalışıyor',
                'Error': 'Hata',
                'Disabled': 'Devre Dışı'
            }[service.status] || service.status;

            const lastRun = service.lastRunTime 
                ? new Date(service.lastRunTime).toLocaleTimeString('tr-TR')
                : '-';
            const nextRun = service.nextRunTime 
                ? new Date(service.nextRunTime).toLocaleTimeString('tr-TR')
                : '-';

            let subServicesHtml = '';
            if (service.subServices && service.subServices.length > 0) {
                subServicesHtml = `
                    <div class=""sub-services"">
                        ${service.subServices.map(sub => `
                            <div class=""sub-service"">
                                <span>${sub.completed ? '<span class=""check"">✓</span>' : '<span class=""pending"">○</span>'} ${sub.name}</span>
                                <span>${sub.recordsProcessed} kayıt</span>
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            return `
                <div class=""card"">
                    <div class=""card-header"">
                        <h3 class=""card-title"">
                            ${service.status === 'Running' ? '<div class=""running-indicator""></div>' : ''}
                            ${service.displayName || service.serviceName}
                        </h3>
                        <span class=""status-badge ${statusClass}"">${statusText}</span>
                    </div>
                    <div class=""service-row"">
                        <div class=""service-info"">
                            <p>Son Çalışma: ${lastRun}</p>
                            <p>Sonraki: ${nextRun}</p>
                            <p>İşlenen: ${service.totalRecordsProcessed || 0} kayıt</p>
                        </div>
                        <div class=""service-actions"">
                            <button class=""trigger-btn"" onclick=""triggerSync('${service.serviceName}')"" 
                                ${service.status === 'Running' ? 'disabled' : ''}>
                                ▶ Çalıştır
                            </button>
                        </div>
                    </div>
                    ${subServicesHtml}
                    ${service.lastError ? `<p style=""color: var(--accent-red); font-size: 0.8rem; margin-top: 8px;"">Son Hata: ${service.lastError}</p>` : ''}
                </div>
            `;
        }

        function updateSettingsUI(settings) {
            document.getElementById('apiBaseUrl').value = settings.laravelApi?.baseUrl || '';
            document.getElementById('apiKey').value = settings.laravelApi?.apiKey || '';
            document.getElementById('initialDelay').value = settings.initialDelaySeconds || 30;

            // Service settings
            const grid = document.getElementById('serviceSettingsGrid');
            grid.innerHTML = settings.services.map(service => `
                <div class=""card"" style=""padding: 16px;"">
                    <h4 style=""margin-bottom: 12px;"">${service.displayName}</h4>
                    <div class=""input-group"">
                        <label>Aktif</label>
                        <select id=""service_${service.name}_enabled"" onchange=""saveServiceSetting('${service.name}')"">
                            <option value=""true"" ${service.enabled ? 'selected' : ''}>Evet</option>
                            <option value=""false"" ${!service.enabled ? 'selected' : ''}>Hayır</option>
                        </select>
                    </div>
                    <div class=""input-group"">
                        <label>Interval (dakika)</label>
                        <input type=""number"" id=""service_${service.name}_interval"" value=""${service.intervalMinutes}"" min=""1"" max=""60"" onchange=""saveServiceSetting('${service.name}')"">
                    </div>
                    <div class=""input-group"">
                        <label>Thread</label>
                        <select id=""service_${service.name}_thread"" onchange=""saveServiceSetting('${service.name}')"">
                            <option value=""1"" ${service.thread === 1 ? 'selected' : ''}>Thread 1 (Order)</option>
                            <option value=""2"" ${service.thread === 2 ? 'selected' : ''}>Thread 2 (Data)</option>
                            <option value=""3"" ${service.thread === 3 ? 'selected' : ''}>Thread 3 (Cari)</option>
                        </select>
                    </div>
                    <div class=""input-group"">
                        <label>Sıra</label>
                        <input type=""number"" id=""service_${service.name}_order"" value=""${service.order}"" min=""1"" max=""10"" onchange=""saveServiceSetting('${service.name}')"">
                    </div>
                </div>
            `).join('');
        }

        async function triggerSync(serviceName) {
            try {
                const response = await fetch(`/api/SyncStatus/trigger/${serviceName}`, { method: 'POST' });
                const result = await response.json();
                alert(result.message);
                fetchStatus();
            } catch (error) {
                alert('Hata: ' + error.message);
            }
        }

        async function toggleGlobalSync() {
            try {
                await fetch('/api/SyncStatus/toggle', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ enabled: !globalEnabled })
                });
                fetchStatus();
            } catch (error) {
                alert('Hata: ' + error.message);
            }
        }

        async function saveApiSettings() {
            try {
                await fetch('/api/SyncStatus/settings/api', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        baseUrl: document.getElementById('apiBaseUrl').value,
                        apiKey: document.getElementById('apiKey').value
                    })
                });
                alert('API ayarları kaydedildi');
            } catch (error) {
                alert('Hata: ' + error.message);
            }
        }

        async function saveGeneralSettings() {
            currentSettings.initialDelaySeconds = parseInt(document.getElementById('initialDelay').value);
            try {
                await fetch('/api/SyncStatus/settings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(currentSettings)
                });
                alert('Genel ayarlar kaydedildi');
            } catch (error) {
                alert('Hata: ' + error.message);
            }
        }

        async function saveServiceSetting(serviceName) {
            const service = currentSettings.services.find(s => s.name === serviceName);
            if (service) {
                service.enabled = document.getElementById(`service_${serviceName}_enabled`).value === 'true';
                service.intervalMinutes = parseInt(document.getElementById(`service_${serviceName}_interval`).value);
                service.thread = parseInt(document.getElementById(`service_${serviceName}_thread`).value);
                service.order = parseInt(document.getElementById(`service_${serviceName}_order`).value);

                try {
                    await fetch(`/api/SyncStatus/settings/service/${serviceName}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(service)
                    });
                } catch (error) {
                    alert('Hata: ' + error.message);
                }
            }
        }

        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabName + 'Tab').classList.add('active');
            event.target.classList.add('active');
            
            if (tabName === 'settings') {
                fetchSettings();
            }
        }

        // Initial load
        fetchStatus();
        fetchSettings();
        
        // Auto refresh every 10 seconds
        setInterval(fetchStatus, 10000);
    </script>
</body>
</html>";
    }
}

public class ToggleRequest
{
    public bool Enabled { get; set; }
}
