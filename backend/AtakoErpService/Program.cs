using Serilog;
using AtakoErpService.Services;

// Serilog early initialization for startup errors
Log.Logger = new LoggerConfiguration()
    .WriteTo.Console()
    .WriteTo.File("Logs/startup-.txt", rollingInterval: RollingInterval.Day)
    .CreateBootstrapLogger();

try
{
    Log.Information("AtakodErpService başlatılıyor...");

    var builder = WebApplication.CreateBuilder(args);

    // Serilog configuration from appsettings.json
    builder.Host.UseSerilog((context, services, configuration) => configuration
        .ReadFrom.Configuration(context.Configuration)
        .ReadFrom.Services(services)
        .Enrich.FromLogContext());

    // Add services
    builder.Services.AddControllers();
    builder.Services.AddEndpointsApiExplorer();
    builder.Services.AddSwaggerGen(c =>
    {
        c.SwaggerDoc("v1", new() { Title = "Atakod ERP Service", Version = "v1" });
    });

    // Database service
    builder.Services.AddSingleton<IDatabaseService, DatabaseService>();
    
    // Cari Sync service
    builder.Services.AddScoped<ICariSyncService, CariSyncService>();
    
    // Stok Sync service
    builder.Services.AddScoped<StokSyncService>();
    
    // Resim Sync service
    builder.Services.AddScoped<ResimSyncService>();
    
    // Bakiye Sync service
    builder.Services.AddScoped<BakiyeSyncService>();
    
    // HttpClient for Laravel API
    builder.Services.AddHttpClient("LaravelApi", client =>
    {
        client.Timeout = TimeSpan.FromSeconds(30);
    });

    // CORS for Laravel B2B
    builder.Services.AddCors(options =>
    {
        options.AddPolicy("AllowAll", policy =>
        {
            policy.AllowAnyOrigin()
                  .AllowAnyMethod()
                  .AllowAnyHeader();
        });
    });

    var app = builder.Build();

    // Request logging
    app.UseSerilogRequestLogging();

    // Swagger (always enabled for development)
    app.UseSwagger();
    app.UseSwaggerUI(c =>
    {
        c.SwaggerEndpoint("/swagger/v1/swagger.json", "Atakod ERP Service v1");
        c.RoutePrefix = string.Empty; // Swagger at root
    });

    app.UseCors("AllowAll");
    app.UseAuthorization();
    app.MapControllers();

    Log.Information("AtakodErpService hazır. Port: {Port}", "5000");
    app.Run("http://0.0.0.0:5000");
}
catch (Exception ex)
{
    Log.Fatal(ex, "Uygulama başlatılamadı!");
}
finally
{
    Log.CloseAndFlush();
}
