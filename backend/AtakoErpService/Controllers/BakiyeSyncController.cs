using Microsoft.AspNetCore.Mvc;
using AtakoErpService.Services;

namespace AtakoErpService.Controllers;

[ApiController]
[Route("api/[controller]")]
public class BakiyeSyncController : ControllerBase
{
    private readonly BakiyeSyncService _bakiyeSyncService;
    private readonly ILogger<BakiyeSyncController> _logger;

    public BakiyeSyncController(BakiyeSyncService bakiyeSyncService, ILogger<BakiyeSyncController> logger)
    {
        _bakiyeSyncService = bakiyeSyncService;
        _logger = logger;
    }

    /// <summary>
    /// Bekleyen bakiyeleri listeler (ATV_B2BBAKIYE)
    /// </summary>
    [HttpGet("pending")]
    public async Task<IActionResult> GetPendingBakiyeler()
    {
        _logger.LogInformation("Bekleyen bakiyeler isteniyor...");
        var pendingBakiyeler = await _bakiyeSyncService.GetPendingBakiyeAsync();
        return Ok(pendingBakiyeler);
    }

    /// <summary>
    /// Bakiyeleri Laravel'e senkronize eder
    /// Sadece mevcut ürünlerin bakiyesini günceller
    /// </summary>
    [HttpPost("sync")]
    public async Task<IActionResult> SyncBakiyeler()
    {
        _logger.LogInformation("Bakiye senkronizasyonu başlatılıyor...");
        var result = await _bakiyeSyncService.SyncToLaravelAsync();
        
        if (result.Success)
        {
            return Ok(new
            {
                message = "Bakiye senkronizasyonu tamamlandı",
                updated = result.UpdatedCount,
                skipped = result.SkippedCount,
                errors = result.ErrorCount
            });
        }
        
        return Ok(new
        {
            message = "Bakiye senkronizasyonu tamamlandı (hatalarla)",
            updated = result.UpdatedCount,
            skipped = result.SkippedCount,
            errors = result.ErrorCount,
            errorDetails = result.Errors.Take(10)
        });
    }
}
