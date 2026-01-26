using System.Data;
using System.Data.SqlClient;
using Dapper;

namespace AtakoErpService.Services;

public interface IDatabaseService
{
    Task<IEnumerable<T>> QueryAsync<T>(string sql, object? parameters = null);
    Task<T?> QueryFirstOrDefaultAsync<T>(string sql, object? parameters = null);
    Task<int> ExecuteAsync(string sql, object? parameters = null);
    Task<bool> TestConnectionAsync();
}

public class DatabaseService : IDatabaseService
{
    private readonly string _connectionString;
    private readonly ILogger<DatabaseService> _logger;

    public DatabaseService(IConfiguration configuration, ILogger<DatabaseService> logger)
    {
        _connectionString = configuration.GetConnectionString("ErpDatabase") 
            ?? throw new InvalidOperationException("ErpDatabase connection string not found");
        _logger = logger;
    }

    private IDbConnection CreateConnection() => new SqlConnection(_connectionString);

    public async Task<IEnumerable<T>> QueryAsync<T>(string sql, object? parameters = null)
    {
        try
        {
            using var connection = CreateConnection();
            _logger.LogDebug("SQL Query: {Sql}", sql);
            return await connection.QueryAsync<T>(sql, parameters);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "SQL Query hatası: {Sql}", sql);
            throw;
        }
    }

    public async Task<T?> QueryFirstOrDefaultAsync<T>(string sql, object? parameters = null)
    {
        try
        {
            using var connection = CreateConnection();
            _logger.LogDebug("SQL Query: {Sql}", sql);
            return await connection.QueryFirstOrDefaultAsync<T>(sql, parameters);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "SQL Query hatası: {Sql}", sql);
            throw;
        }
    }

    public async Task<int> ExecuteAsync(string sql, object? parameters = null)
    {
        try
        {
            using var connection = CreateConnection();
            _logger.LogDebug("SQL Execute: {Sql}", sql);
            var result = await connection.ExecuteAsync(sql, parameters);
            _logger.LogInformation("SQL Execute başarılı. Etkilenen satır: {Count}", result);
            return result;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "SQL Execute hatası: {Sql}", sql);
            throw;
        }
    }

    public async Task<bool> TestConnectionAsync()
    {
        try
        {
            using var connection = CreateConnection();
            await connection.QueryAsync("SELECT 1");
            _logger.LogInformation("Veritabanı bağlantısı başarılı!");
            return true;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Veritabanı bağlantısı başarısız!");
            return false;
        }
    }
}
