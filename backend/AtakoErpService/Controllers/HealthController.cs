using Microsoft.AspNetCore.Mvc;
using AtakoErpService.Services;

namespace AtakoErpService.Controllers;

[ApiController]
[Route("api/[controller]")]
public class HealthController : ControllerBase
{
    private readonly IDatabaseService _db;
    private readonly ILogger<HealthController> _logger;

    public HealthController(IDatabaseService db, ILogger<HealthController> logger)
    {
        _db = db;
        _logger = logger;
    }

    /// <summary>
    /// Servis durumunu kontrol eder
    /// </summary>
    [HttpGet]
    public async Task<IActionResult> Get()
    {
        _logger.LogInformation("Sağlık kontrolü yapılıyor...");
        
        var dbStatus = await _db.TestConnectionAsync();
        
        return Ok(new
        {
            status = "running",
            timestamp = DateTime.Now,
            database = dbStatus ? "connected" : "disconnected",
            version = "1.0.0",
            platform = Environment.Is64BitProcess ? "x64" : "x86"
        });
    }

    /// <summary>
    /// Veritabanı bağlantısını test eder
    /// </summary>
    [HttpGet("db-test")]
    public async Task<IActionResult> TestDatabase()
    {
        _logger.LogInformation("Veritabanı test ediliyor...");
        
        try
        {
            var result = await _db.QueryFirstOrDefaultAsync<dynamic>("SELECT GETDATE() as ServerTime, @@VERSION as Version");
            
            return Ok(new
            {
                success = true,
                serverTime = result?.ServerTime,
                version = result?.Version?.ToString()?.Split('\n')[0]
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Veritabanı testi başarısız");
            return StatusCode(500, new
            {
                success = false,
                error = ex.Message
            });
        }
    }
}
