using AtakoDB2B.WindowsService.Models;
using Dapper;
using Microsoft.Data.SqlClient;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;

namespace AtakoDB2B.WindowsService.Services;

public class NetsisDbService : INetsisDbService
{
    private readonly NetsisConfig _config;
    private readonly ILogger<NetsisDbService> _logger;

    public NetsisDbService(IOptions<NetsisConfig> config, ILogger<NetsisDbService> logger)
    {
        _config = config.Value;
        _logger = logger;
    }

    private SqlConnection GetConnection()
    {
        return new SqlConnection(_config.ConnectionString);
    }

    public async Task<bool> TestConnectionAsync()
    {
        try
        {
            using var connection = GetConnection();
            await connection.OpenAsync();
            _logger.LogInformation("Netsis veritabanı bağlantısı başarılı");
            return true;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Netsis veritabanı bağlantısı başarısız");
            return false;
        }
    }

    public async Task<List<NetsisCustomer>> GetCustomersAsync(DateTime? lastSyncDate = null)
    {
        try
        {
            using var connection = GetConnection();
            
            var query = @"
                SELECT 
                    cari_kod,
                    cari_isim,
                    cari_unvan1,
                    cari_adres1,
                    cari_ilce,
                    cari_il,
                    cari_tel1,
                    cari_email,
                    cari_vergi_dairesi,
                    cari_vergi_no,
                    cari_gln_kodu,
                    cari_grup_kodu,
                    plasiyer_kodu,
                    cari_hareket_tipi,
                    cari_kayit_tarihi,
                    cari_guncelleme_tarihi
                FROM CARI_HESAPLAR
                WHERE cari_hareket_tipi = 0 -- Müşteri (0=Müşteri, 1=Tedarikçi)
                    AND (cari_pasif_mi = 0 OR cari_pasif_mi IS NULL)";

            if (lastSyncDate.HasValue)
            {
                query += " AND (cari_guncelleme_tarihi >= @LastSyncDate OR cari_kayit_tarihi >= @LastSyncDate)";
            }

            query += " ORDER BY cari_kod";

            var customers = await connection.QueryAsync<NetsisCustomer>(
                query,
                new { LastSyncDate = lastSyncDate },
                commandTimeout: _config.Timeout
            );

            _logger.LogInformation("Netsis'ten {Count} müşteri çekildi", customers.Count());
            return customers.ToList();
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Netsis'ten müşteriler çekilirken hata oluştu");
            throw;
        }
    }

    public async Task<List<NetsisProduct>> GetProductsAsync(DateTime? lastSyncDate = null)
    {
        try
        {
            using var connection = GetConnection();
            
            var query = @"
                SELECT 
                    sto_kod,
                    sto_isim,
                    barkod,
                    muadil_kodu,
                    sto_perakende_vergi,
                    sto_kdv_dahil_perakende,
                    sto_kdv_haric_perakende,
                    kdv_kodu,
                    kurum_iskonto,
                    eczaci_kari,
                    ticari_iskonto,
                    mf,
                    depocu_fiyati,
                    net_fiyat,
                    sto_miktar,
                    sto_marka_kodu,
                    sto_grup_kodu,
                    sto_birim1_ad,
                    sto_pasif_mi,
                    sto_kayit_tarihi,
                    sto_guncelleme_tarihi
                FROM STOKLAR
                WHERE (sto_pasif_mi = 0 OR sto_pasif_mi IS NULL)";

            if (lastSyncDate.HasValue)
            {
                query += " AND (sto_guncelleme_tarihi >= @LastSyncDate OR sto_kayit_tarihi >= @LastSyncDate)";
            }

            query += " ORDER BY sto_kod";

            var products = await connection.QueryAsync<NetsisProduct>(
                query,
                new { LastSyncDate = lastSyncDate },
                commandTimeout: _config.Timeout
            );

            _logger.LogInformation("Netsis'ten {Count} ürün çekildi", products.Count());
            return products.ToList();
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Netsis'ten ürünler çekilirken hata oluştu");
            throw;
        }
    }

    public async Task<List<NetsisStock>> GetStockLevelsAsync(List<string> productCodes)
    {
        try
        {
            using var connection = GetConnection();
            
            var query = @"
                SELECT 
                    sto_kod,
                    SUM(har_miktar) as miktar,
                    har_depo_kodu as depo_kodu,
                    MAX(har_tarihi) as hareket_tarihi
                FROM STOK_HAREKETLERI
                WHERE sto_kod IN @ProductCodes
                GROUP BY sto_kod, har_depo_kodu";

            var stocks = await connection.QueryAsync<NetsisStock>(
                query,
                new { ProductCodes = productCodes },
                commandTimeout: _config.Timeout
            );

            _logger.LogInformation("Netsis'ten {Count} ürün için stok bilgisi çekildi", stocks.Count());
            return stocks.ToList();
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Netsis'ten stok bilgileri çekilirken hata oluştu");
            throw;
        }
    }

    public async Task<List<NetsisCustomer>> GetChangedCustomersAsync(DateTime sinceDate)
    {
        return await GetCustomersAsync(sinceDate);
    }

    public async Task<List<NetsisProduct>> GetChangedProductsAsync(DateTime sinceDate)
    {
        return await GetProductsAsync(sinceDate);
    }
}







