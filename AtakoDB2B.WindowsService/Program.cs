using AtakoDB2B.WindowsService.Jobs;
using AtakoDB2B.WindowsService.Services;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Hosting;
using Quartz;
using Serilog;

namespace AtakoDB2B.WindowsService;

public class Program
{
    public static void Main(string[] args)
    {
        // Serilog yapılandırması
        Log.Logger = new LoggerConfiguration()
            .WriteTo.Console()
            .WriteTo.File("logs/atakodb2b-service-.txt", rollingInterval: RollingInterval.Day)
            .CreateBootstrapLogger();

        try
        {
            Log.Information("atakodb2b Windows Service başlatılıyor...");
            CreateHostBuilder(args).Build().Run();
            Log.Information("atakodb2b Windows Service başarıyla durduruldu.");
        }
        catch (Exception ex)
        {
            Log.Fatal(ex, "atakodb2b Windows Service başlatılamadı!");
            throw;
        }
        finally
        {
            Log.CloseAndFlush();
        }
    }

    public static IHostBuilder CreateHostBuilder(string[] args) =>
        Host.CreateDefaultBuilder(args)
            .UseWindowsService(options =>
            {
                options.ServiceName = "atakodb2b Sync Service";
            })
            .UseSerilog((context, services, configuration) => configuration
                .ReadFrom.Configuration(context.Configuration)
                .ReadFrom.Services(services)
                .Enrich.FromLogContext())
            .ConfigureServices((hostContext, services) =>
            {
                // Konfigürasyon
                var configuration = hostContext.Configuration;
                services.Configure<NetsisConfig>(configuration.GetSection("Netsis"));
                services.Configure<ApiConfig>(configuration.GetSection("Api"));

                // HttpClient ile API servisi
                services.AddHttpClient<IAtakoDB2BApiService, AtakoDB2BApiService>()
                    .AddPolicyHandler(RetryPolicies.GetRetryPolicy())
                    .AddPolicyHandler(RetryPolicies.GetCircuitBreakerPolicy());

                // Servisler
                services.AddSingleton<INetsisDbService, NetsisDbService>();
                services.AddSingleton<IAtakoDB2BApiService, AtakoDB2BApiService>();
                services.AddSingleton<ISyncService, SyncService>();

                // Quartz.NET - Job Scheduler
                services.AddQuartz(q =>
                {
                    q.UseMicrosoftDependencyInjectionJobFactory();

                    // Kullanıcı Senkronizasyonu Job'ı
                    var userSyncJobKey = new JobKey("UserSyncJob");
                    q.AddJob<UserSyncJob>(opts => opts.WithIdentity(userSyncJobKey));
                    
                    q.AddTrigger(opts => opts
                        .ForJob(userSyncJobKey)
                        .WithIdentity("UserSyncJob-trigger")
                        .WithCronSchedule(configuration["Schedules:UserSync"] ?? "0 0 2 * * ?") // Her gece 02:00
                    );

                    // Ürün Senkronizasyonu Job'ı
                    var productSyncJobKey = new JobKey("ProductSyncJob");
                    q.AddJob<ProductSyncJob>(opts => opts.WithIdentity(productSyncJobKey));
                    
                    q.AddTrigger(opts => opts
                        .ForJob(productSyncJobKey)
                        .WithIdentity("ProductSyncJob-trigger")
                        .WithCronSchedule(configuration["Schedules:ProductSync"] ?? "0 0 3 * * ?") // Her gece 03:00
                    );

                    // Stok Senkronizasyonu Job'ı
                    var stockSyncJobKey = new JobKey("StockSyncJob");
                    q.AddJob<StockSyncJob>(opts => opts.WithIdentity(stockSyncJobKey));
                    
                    q.AddTrigger(opts => opts
                        .ForJob(stockSyncJobKey)
                        .WithIdentity("StockSyncJob-trigger")
                        .WithCronSchedule(configuration["Schedules:StockSync"] ?? "0 */30 * * * ?") // Her 30 dakikada
                    );
                });

                // Quartz Hosted Service
                services.AddQuartzHostedService(q => q.WaitForJobsToComplete = true);
            });
}







