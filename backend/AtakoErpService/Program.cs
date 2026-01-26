using Serilog;
using AtakoErpService.Services;
using AtakoErpService.BackgroundServices;
using AtakoErpService.Middleware;
using System.Text.Json.Serialization;

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
    builder.Services.AddControllers()
        .AddJsonOptions(options =>
        {
            options.JsonSerializerOptions.Converters.Add(new JsonStringEnumConverter());
            options.JsonSerializerOptions.PropertyNamingPolicy = System.Text.Json.JsonNamingPolicy.CamelCase;
            options.JsonSerializerOptions.Encoder = System.Text.Encodings.Web.JavaScriptEncoder.UnsafeRelaxedJsonEscaping;
        });
    builder.Services.AddEndpointsApiExplorer();
    builder.Services.AddSwaggerGen(c =>
    {
        c.SwaggerDoc("v1", new() { Title = "Atakod ERP Service", Version = "v1" });
    });

    // Database service
    builder.Services.AddSingleton<IDatabaseService, DatabaseService>();
    
    // ===== Sync Status & Settings (Singleton - shared across all services) =====
    builder.Services.AddSingleton<SyncStatusService>();
    builder.Services.AddSingleton<SyncSettingsService>();
    
    // ===== Sync Services (Scoped) =====
    // Cari Sync service
    builder.Services.AddScoped<ICariSyncService, CariSyncService>();
    
    // Stok Sync service
    builder.Services.AddScoped<StokSyncService>();
    
    // Resim Sync service
    builder.Services.AddScoped<ResimSyncService>();
    
    // Bakiye Sync service
    builder.Services.AddScoped<BakiyeSyncService>();
    
    // Order Sync service (Web -> Netsis) - REST API
    builder.Services.AddScoped<OrderSyncService>();
    
    // Cari Ekstre service
    builder.Services.AddScoped<CariEkstreService>();
    
    // Fatura Detay service
    builder.Services.AddScoped<FaturaDetayService>();
    
    // ===== Background Services (Hosted) =====
    builder.Services.AddHostedService<OrderSyncBackgroundService>();
    builder.Services.AddHostedService<DataSyncBackgroundService>();
    builder.Services.AddHostedService<CariSyncBackgroundService>();
    
    // HttpClient for Laravel API
    builder.Services.AddHttpClient("LaravelApi", client =>
    {
        client.Timeout = TimeSpan.FromSeconds(30);
    });

    // CORS for Laravel B2B - Production: restrict to specific origins
    builder.Services.AddCors(options =>
    {
        options.AddPolicy("AllowLaravel", corsBuilder =>
        {
            corsBuilder.WithOrigins(
                    "http://localhost:8080",
                    "http://localhost:8000",
                    "http://atakod.tr",
                    "https://atakod.tr"
                )
                .AllowAnyMethod()
                .AllowAnyHeader();
        });
    });

    var app = builder.Build();

    // Configure the HTTP request pipeline
    
    // Swagger only in Development
    if (app.Environment.IsDevelopment())
    {
        app.UseSwagger();
        app.UseSwaggerUI();
    }

    app.UseCors("AllowLaravel");
    
    // API Key validation middleware
    app.UseMiddleware<ApiKeyMiddleware>();
    
    app.UseSerilogRequestLogging();
    app.MapControllers();

    // Startup complete
    var port = app.Configuration["Urls"]?.Split(':').LastOrDefault() ?? "5000";
    Log.Information("AtakodErpService hazır. Port: {Port}", port);
    Log.Information("Dashboard: http://localhost:{Port}/api/SyncStatus/dashboard", port);

    app.Run();
}
catch (Exception ex)
{
    Log.Fatal(ex, "Uygulama başlatılırken hata oluştu");
}
finally
{
    Log.CloseAndFlush();
}

