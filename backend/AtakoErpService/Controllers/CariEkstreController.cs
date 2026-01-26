using AtakoErpService.Services;
using AtakoErpService.Models;
using Microsoft.AspNetCore.Mvc;

namespace AtakoErpService.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class CariEkstreController : ControllerBase
    {
        private readonly ILogger<CariEkstreController> _logger;
        private readonly CariEkstreService _ekstreService;

        public CariEkstreController(
            ILogger<CariEkstreController> logger,
            CariEkstreService ekstreService)
        {
            _logger = logger;
            _ekstreService = ekstreService;
        }

        /// <summary>
        /// Cari hesap ekstresini getirir
        /// </summary>
        /// <param name="musteriKodu">Müşteri/Cari kodu (zorunlu)</param>
        /// <param name="baslangicTarihi">Başlangıç tarihi (YYYY-MM-DD, varsayılan: son 30 gün)</param>
        /// <param name="bitisTarihi">Bitiş tarihi (YYYY-MM-DD, varsayılan: bugün)</param>
        [HttpGet]
        public async Task<IActionResult> GetEkstre(
            [FromQuery] string musteriKodu,
            [FromQuery] string? baslangicTarihi = null,
            [FromQuery] string? bitisTarihi = null)
        {
            try
            {
                if (string.IsNullOrEmpty(musteriKodu))
                {
                    return BadRequest(new CariEkstreResponse
                    {
                        Success = false,
                        Message = "Müşteri kodu zorunludur"
                    });
                }

                // Varsayılan tarihler: son 30 gün
                var bitis = string.IsNullOrEmpty(bitisTarihi) 
                    ? DateTime.Now.ToString("yyyy-MM-dd") 
                    : bitisTarihi;
                    
                var baslangic = string.IsNullOrEmpty(baslangicTarihi) 
                    ? DateTime.Now.AddDays(-30).ToString("yyyy-MM-dd") 
                    : baslangicTarihi;

                _logger.LogInformation("Cari ekstre isteği: {MusteriKodu}, {Baslangic} - {Bitis}", 
                    musteriKodu, baslangic, bitis);

                var result = await _ekstreService.GetEkstreAsync(musteriKodu, baslangic, bitis);
                
                if (!result.Success)
                {
                    return BadRequest(result);
                }

                return Ok(result);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Cari ekstre hatası: {MusteriKodu}", musteriKodu);
                return StatusCode(500, new CariEkstreResponse
                {
                    Success = false,
                    Message = "Sunucu hatası: " + ex.Message
                });
            }
        }

        /// <summary>
        /// Carinin güncel bakiyesini getirir
        /// </summary>
        [HttpGet("bakiye/{musteriKodu}")]
        public async Task<IActionResult> GetBakiye(string musteriKodu)
        {
            try
            {
                if (string.IsNullOrEmpty(musteriKodu))
                {
                    return BadRequest(new { success = false, message = "Müşteri kodu zorunludur" });
                }

                var bakiye = await _ekstreService.GetGuncelBakiyeAsync(musteriKodu);
                
                return Ok(new
                {
                    success = true,
                    musteriKodu = musteriKodu,
                    bakiye = bakiye
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Bakiye hatası: {MusteriKodu}", musteriKodu);
                return StatusCode(500, new { success = false, message = ex.Message });
            }
        }

        /// <summary>
        /// Servis durumu
        /// </summary>
        [HttpGet("status")]
        public IActionResult GetStatus()
        {
            return Ok(new
            {
                success = true,
                message = "Cari Ekstre Service hazır",
                timestamp = DateTime.Now
            });
        }
    }
}
