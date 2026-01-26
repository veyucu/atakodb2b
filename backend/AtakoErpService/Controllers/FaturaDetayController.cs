using AtakoErpService.Services;
using AtakoErpService.Models;
using Microsoft.AspNetCore.Mvc;

namespace AtakoErpService.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class FaturaDetayController : ControllerBase
    {
        private readonly ILogger<FaturaDetayController> _logger;
        private readonly FaturaDetayService _faturaService;

        public FaturaDetayController(
            ILogger<FaturaDetayController> logger,
            FaturaDetayService faturaService)
        {
            _logger = logger;
            _faturaService = faturaService;
        }

        /// <summary>
        /// Fatura detayını getirir (kalemler + toplamlar)
        /// </summary>
        /// <param name="belgeNo">Belge/Fatura numarası (zorunlu)</param>
        /// <param name="musteriKodu">Müşteri kodu (zorunlu)</param>
        [HttpGet]
        public async Task<IActionResult> GetFaturaDetay(
            [FromQuery] string belgeNo,
            [FromQuery] string musteriKodu)
        {
            try
            {
                if (string.IsNullOrEmpty(belgeNo) || string.IsNullOrEmpty(musteriKodu))
                {
                    return BadRequest(new FaturaDetayResponse
                    {
                        Success = false,
                        Message = "Belge no ve müşteri kodu zorunludur"
                    });
                }

                _logger.LogInformation("Fatura detay isteği: {BelgeNo}, {MusteriKodu}", belgeNo, musteriKodu);

                var result = await _faturaService.GetFaturaDetayAsync(belgeNo, musteriKodu);
                
                if (!result.Success)
                {
                    return BadRequest(result);
                }

                return Ok(result);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Fatura detay hatası: {BelgeNo}", belgeNo);
                return StatusCode(500, new FaturaDetayResponse
                {
                    Success = false,
                    Message = "Sunucu hatası: " + ex.Message
                });
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
                message = "Fatura Detay Service hazır",
                timestamp = DateTime.Now
            });
        }
    }
}
