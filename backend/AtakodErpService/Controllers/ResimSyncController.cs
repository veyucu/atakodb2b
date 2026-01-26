using Microsoft.AspNetCore.Mvc;
using AtakoErpService.Services;

namespace AtakoErpService.Controllers;

[ApiController]
[Route("api/[controller]")]
public class ResimSyncController : ControllerBase
{
    private readonly ResimSyncService _resimSyncService;
    private readonly ILogger<ResimSyncController> _logger;

    public ResimSyncController(ResimSyncService resimSyncService, ILogger<ResimSyncController> logger)
    {
        _resimSyncService = resimSyncService;
        _logger = logger;
    }

    /// <summary>
    /// Bekleyen resimleri listeler (ATV_B2BRESIM)
    /// </summary>
    [HttpGet("pending")]
    public async Task<IActionResult> GetPendingResimler()
    {
        _logger.LogInformation("Bekleyen resimler isteniyor...");
        var pendingResimler = await _resimSyncService.GetPendingResimAsync();
        
        // Binary veriyi response'da gösterme, sadece meta bilgileri göster
        var summary = pendingResimler.Select(r => new
        {
            r.STOK_KODU,
            HasImage = r.RESIM != null && r.RESIM.Length > 0,
            ImageSize = r.RESIM?.Length ?? 0,
            r.ISLEM
        });
        
        return Ok(summary);
    }

    /// <summary>
    /// Resimleri Laravel'e senkronize eder
    /// Sadece mevcut ürünlerin resmini günceller
    /// </summary>
    [HttpPost("sync")]
    public async Task<IActionResult> SyncResimler()
    {
        _logger.LogInformation("Resim senkronizasyonu başlatılıyor...");
        var result = await _resimSyncService.SyncToLaravelAsync();
        
        if (result.Success)
        {
            return Ok(new
            {
                message = "Resim senkronizasyonu tamamlandı",
                updated = result.UpdatedCount,
                skipped = result.SkippedCount,
                errors = result.ErrorCount
            });
        }
        
        return Ok(new
        {
            message = "Resim senkronizasyonu tamamlandı (hatalarla)",
            updated = result.UpdatedCount,
            skipped = result.SkippedCount,
            errors = result.ErrorCount,
            errorDetails = result.Errors.Take(10)
        });
    }
}
