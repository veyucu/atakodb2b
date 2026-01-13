using AtakoDB2B.WindowsService.Models;
using Microsoft.Extensions.Logging;

namespace AtakoDB2B.WindowsService.Services;

public class SyncService : ISyncService
{
    private readonly INetsisDbService _netsisDb;
    private readonly IAtakoDB2BApiService _apiService;
    private readonly ILogger<SyncService> _logger;

    public SyncService(
        INetsisDbService netsisDb,
        IAtakoDB2BApiService apiService,
        ILogger<SyncService> logger)
    {
        _netsisDb = netsisDb;
        _apiService = apiService;
        _logger = logger;
    }

    public async Task<bool> SyncUsersAsync()
    {
        try
        {
            _logger.LogInformation("Kullanıcı senkronizasyonu başlatılıyor...");

            // Netsis'ten müşterileri çek
            var netsisCustomers = await _netsisDb.GetCustomersAsync();
            _logger.LogInformation("{Count} müşteri Netsis'ten çekildi", netsisCustomers.Count);

            if (!netsisCustomers.Any())
            {
                _logger.LogWarning("Senkronize edilecek müşteri bulunamadı");
                return true;
            }

            // API formatına dönüştür
            var apiUsers = netsisCustomers.Select(c => new ApiUserDto
            {
                musteri_kodu = c.cari_kod,
                name = string.IsNullOrWhiteSpace(c.cari_isim) ? c.cari_unvan1 : c.cari_isim,
                email = GenerateEmail(c.cari_kod, c.cari_email),
                password = null, // Varsa güncelleme, yoksa API'de default şifre atanacak
                user_type = "musteri",
                musteri_adi = c.cari_unvan1,
                adres = c.cari_adres1,
                ilce = c.cari_ilce,
                il = c.cari_il,
                gln_numarasi = c.cari_gln_kodu,
                telefon = c.cari_tel1,
                mail_adresi = c.cari_email,
                vergi_dairesi = c.cari_vergi_dairesi,
                vergi_kimlik_numarasi = c.cari_vergi_no,
                grup_kodu = c.cari_grup_kodu,
                plasiyer_kodu = c.plasiyer_kodu,
                is_active = true
            }).ToList();

            // Batch'ler halinde gönder (100'er 100'er)
            var batchSize = 100;
            var totalCreated = 0;
            var totalUpdated = 0;
            var totalErrors = new List<string>();

            for (int i = 0; i < apiUsers.Count; i += batchSize)
            {
                var batch = apiUsers.Skip(i).Take(batchSize).ToList();
                _logger.LogInformation("Batch {BatchNo} gönderiliyor ({Count} kullanıcı)...", i / batchSize + 1, batch.Count);

                var result = await _apiService.SyncUsersAsync(batch);
                
                totalCreated += result.Created;
                totalUpdated += result.Updated;
                totalErrors.AddRange(result.Errors);

                // API'ye yük bindirmemek için kısa bir bekleme
                await Task.Delay(500);
            }

            _logger.LogInformation(
                "Kullanıcı senkronizasyonu tamamlandı: {Created} oluşturuldu, {Updated} güncellendi, {Errors} hata",
                totalCreated,
                totalUpdated,
                totalErrors.Count
            );

            if (totalErrors.Any())
            {
                _logger.LogWarning("Hatalar: {Errors}", string.Join(", ", totalErrors.Take(10)));
            }

            return true;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Kullanıcı senkronizasyonu sırasında hata oluştu");
            return false;
        }
    }

    public async Task<bool> SyncProductsAsync()
    {
        try
        {
            _logger.LogInformation("Ürün senkronizasyonu başlatılıyor...");

            // Netsis'ten ürünleri çek
            var netsisProducts = await _netsisDb.GetProductsAsync();
            _logger.LogInformation("{Count} ürün Netsis'ten çekildi", netsisProducts.Count);

            if (!netsisProducts.Any())
            {
                _logger.LogWarning("Senkronize edilecek ürün bulunamadı");
                return true;
            }

            // API formatına dönüştür
            var apiProducts = netsisProducts.Select(p => new ApiProductDto
            {
                urun_kodu = p.sto_kod,
                urun_adi = p.sto_isim,
                barkod = p.barkod,
                muadil_kodu = p.muadil_kodu,
                satis_fiyati = p.sto_kdv_dahil_perakende > 0 ? p.sto_kdv_dahil_perakende : p.sto_perakende_vergi,
                kdv_orani = CalculateKdvRate(p.kdv_kodu),
                kurum_iskonto = p.kurum_iskonto,
                eczaci_kari = p.eczaci_kari,
                ticari_iskonto = p.ticari_iskonto,
                mf = p.mf,
                depocu_fiyati = p.depocu_fiyati,
                net_fiyat_manuel = p.net_fiyat,
                bakiye = p.sto_miktar,
                marka = p.sto_marka_kodu,
                grup = p.sto_grup_kodu,
                is_active = p.sto_pasif_mi == 0
            }).ToList();

            // Batch'ler halinde gönder
            var batchSize = 100;
            var totalCreated = 0;
            var totalUpdated = 0;
            var totalErrors = new List<string>();

            for (int i = 0; i < apiProducts.Count; i += batchSize)
            {
                var batch = apiProducts.Skip(i).Take(batchSize).ToList();
                _logger.LogInformation("Batch {BatchNo} gönderiliyor ({Count} ürün)...", i / batchSize + 1, batch.Count);

                var result = await _apiService.SyncProductsAsync(batch);
                
                totalCreated += result.Created;
                totalUpdated += result.Updated;
                totalErrors.AddRange(result.Errors);

                await Task.Delay(500);
            }

            _logger.LogInformation(
                "Ürün senkronizasyonu tamamlandı: {Created} oluşturuldu, {Updated} güncellendi, {Errors} hata",
                totalCreated,
                totalUpdated,
                totalErrors.Count
            );

            if (totalErrors.Any())
            {
                _logger.LogWarning("Hatalar: {Errors}", string.Join(", ", totalErrors.Take(10)));
            }

            return true;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Ürün senkronizasyonu sırasında hata oluştu");
            return false;
        }
    }

    public async Task<bool> SyncStockAsync()
    {
        try
        {
            _logger.LogInformation("Stok senkronizasyonu başlatılıyor...");

            // Netsis'ten tüm ürün kodlarını al
            var products = await _netsisDb.GetProductsAsync();
            var productCodes = products.Select(p => p.sto_kod).ToList();

            if (!productCodes.Any())
            {
                _logger.LogWarning("Stok senkronize edilecek ürün bulunamadı");
                return true;
            }

            // Stok bilgilerini çek
            var stocks = await _netsisDb.GetStockLevelsAsync(productCodes);
            _logger.LogInformation("{Count} ürün için stok bilgisi çekildi", stocks.Count);

            var successCount = 0;
            var errorCount = 0;

            // Her ürün için stok güncelle
            foreach (var stock in stocks)
            {
                try
                {
                    var success = await _apiService.UpdateProductStockAsync(stock.sto_kod, stock.miktar);
                    if (success)
                    {
                        successCount++;
                    }
                    else
                    {
                        errorCount++;
                    }
                }
                catch (Exception ex)
                {
                    _logger.LogWarning(ex, "Stok güncellenemedi: {ProductCode}", stock.sto_kod);
                    errorCount++;
                }
            }

            _logger.LogInformation(
                "Stok senkronizasyonu tamamlandı: {Success} başarılı, {Error} hata",
                successCount,
                errorCount
            );

            return true;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Stok senkronizasyonu sırasında hata oluştu");
            return false;
        }
    }

    private string GenerateEmail(string musteriKodu, string? existingEmail)
    {
        if (!string.IsNullOrWhiteSpace(existingEmail) && existingEmail.Contains("@"))
        {
            return existingEmail;
        }

        // E-posta yoksa müşteri kodundan oluştur
        return $"{musteriKodu.ToLower().Replace(" ", "")}@netsis.local";
    }

    private decimal CalculateKdvRate(int kdvKodu)
    {
        // Netsis KDV kodlarını orana çevir
        return kdvKodu switch
        {
            1 => 18.00m,
            2 => 8.00m,
            3 => 1.00m,
            _ => 0.00m
        };
    }
}







