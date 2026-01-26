using Microsoft.AspNetCore.Mvc;
using AtakoErpService.Services;

namespace AtakoErpService.Controllers;

[ApiController]
[Route("api/[controller]")]
public class CariSyncController : ControllerBase
{
    private readonly ICariSyncService _cariSync;
    private readonly ILogger<CariSyncController> _logger;

    public CariSyncController(ICariSyncService cariSync, ILogger<CariSyncController> logger)
    {
        _cariSync = cariSync;
        _logger = logger;
    }

    /// <summary>
    /// Bekleyen carileri listeler (ATV_B2BCARI)
    /// </summary>
    [HttpGet("pending")]
    public async Task<IActionResult> GetPendingCaris()
    {
        _logger.LogInformation("Bekleyen cariler isteniyor...");
        
        try
        {
            var caris = await _cariSync.GetPendingCariAsync();
            return Ok(new
            {
                success = true,
                count = caris.Count(),
                data = caris
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Bekleyen cariler getirilemedi");
            return StatusCode(500, new { success = false, error = ex.Message });
        }
    }

    /// <summary>
    /// Carileri Laravel'e senkronize eder
    /// </summary>
    [HttpPost("sync")]
    public async Task<IActionResult> SyncCaris()
    {
        _logger.LogInformation("Cari senkronizasyonu başlatılıyor...");
        
        try
        {
            var result = await _cariSync.SyncToLaravelAsync();
            
            return Ok(new
            {
                success = result.Success,
                inserted = result.InsertedCount,
                updated = result.UpdatedCount,
                errors = result.ErrorCount,
                errorDetails = result.Errors,
                syncTime = result.SyncTime
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Cari senkronizasyonu hatası");
            return StatusCode(500, new { success = false, error = ex.Message });
        }
    }
}
