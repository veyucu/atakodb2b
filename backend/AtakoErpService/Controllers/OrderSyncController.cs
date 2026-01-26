using AtakoErpService.Services;
using Microsoft.AspNetCore.Mvc;

namespace AtakoErpService.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class OrderSyncController : ControllerBase
    {
        private readonly ILogger<OrderSyncController> _logger;
        private readonly OrderSyncService _orderSyncService;

        public OrderSyncController(
            ILogger<OrderSyncController> logger,
            OrderSyncService orderSyncService)
        {
            _logger = logger;
            _orderSyncService = orderSyncService;
        }

        /// <summary>
        /// Pending siparişleri Netsis'e senkronize et (REST API)
        /// </summary>
        [HttpPost("run")]
        public async Task<IActionResult> RunSync()
        {
            try
            {
                _logger.LogInformation("Sipariş senkronizasyonu başlatıldı");
                
                var (success, failed) = await _orderSyncService.SyncPendingOrdersAsync();
                
                return Ok(new
                {
                    success = true,
                    message = $"Senkronizasyon tamamlandı. Başarılı: {success}, Başarısız: {failed}",
                    successCount = success,
                    failedCount = failed
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Sipariş senkronizasyonu hatası");
                return StatusCode(500, new
                {
                    success = false,
                    message = "Senkronizasyon sırasında hata oluştu: " + ex.Message
                });
            }
        }

        /// <summary>
        /// Senkronizasyon durumu
        /// </summary>
        [HttpGet("status")]
        public IActionResult GetStatus()
        {
            return Ok(new
            {
                success = true,
                message = "Order Sync Service hazır",
                timestamp = DateTime.Now
            });
        }
    }
}
