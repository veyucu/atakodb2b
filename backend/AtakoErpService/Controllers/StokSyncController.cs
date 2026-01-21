using Microsoft.AspNetCore.Mvc;
using AtakoErpService.Services;

namespace AtakoErpService.Controllers;

[ApiController]
[Route("api/[controller]")]
public class StokSyncController : ControllerBase
{
    private readonly StokSyncService _stokSyncService;
    private readonly ILogger<StokSyncController> _logger;

    public StokSyncController(StokSyncService stokSyncService, ILogger<StokSyncController> logger)
    {
        _stokSyncService = stokSyncService;
        _logger = logger;
    }

    /// <summary>
    /// Bekleyen stokları listeler (ATV_B2BSTOK)
    /// </summary>
    [HttpGet("pending")]
    public async Task<IActionResult> GetPendingStoks()
    {
        _logger.LogInformation("Bekleyen stoklar isteniyor...");
        var pendingStoks = await _stokSyncService.GetPendingStokAsync();
        return Ok(pendingStoks);
    }

    /// <summary>
    /// Stokları Laravel'e senkronize eder
    /// </summary>
    [HttpPost("sync")]
    public async Task<IActionResult> SyncStoks()
    {
        _logger.LogInformation("Stok senkronizasyonu başlatılıyor...");
        var result = await _stokSyncService.SyncToLaravelAsync();
        
        if (result.Success)
        {
            return Ok(new
            {
                message = "Stok senkronizasyonu tamamlandı",
                inserted = result.InsertedCount,
                updated = result.UpdatedCount,
                errors = result.ErrorCount
            });
        }
        
        return Ok(new
        {
            message = "Stok senkronizasyonu tamamlandı (hatalarla)",
            inserted = result.InsertedCount,
            updated = result.UpdatedCount,
            errors = result.ErrorCount,
            errorDetails = result.Errors.Take(10)
        });
    }
}
