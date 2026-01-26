using Serilog;

namespace AtakoErpService.Middleware;

/// <summary>
/// API Key doğrulama middleware'i
/// Laravel'den gelen istekleri doğrular
/// </summary>
public class ApiKeyMiddleware
{
    private readonly RequestDelegate _next;
    private readonly IConfiguration _configuration;
    private const string API_KEY_HEADER = "X-API-Key";

    // API key kontrolü yapılmayacak path'ler (küçük harfle)
    private static readonly string[] ExcludedPaths = new[]
    {
        "/api/health",
        "/swagger",
        "/api/syncstatus/dashboard"  // Dashboard geliştirme için açık (production'da kapatılabilir)
    };

    public ApiKeyMiddleware(RequestDelegate next, IConfiguration configuration)
    {
        _next = next;
        _configuration = configuration;
    }

    public async Task InvokeAsync(HttpContext context)
    {
        var path = context.Request.Path.Value?.ToLowerInvariant() ?? "";

        // Hariç tutulan path'ler için kontrol yapma
        if (ExcludedPaths.Any(excluded => path.StartsWith(excluded)))
        {
            await _next(context);
            return;
        }

        // API key kontrolü
        if (!context.Request.Headers.TryGetValue(API_KEY_HEADER, out var requestApiKey))
        {
            Log.Warning("API Key eksik. Path: {Path}, IP: {IP}", 
                context.Request.Path, 
                context.Connection.RemoteIpAddress);

            context.Response.StatusCode = 401;
            context.Response.ContentType = "application/json";
            await context.Response.WriteAsync("{\"success\":false,\"message\":\"API Key gerekli\"}");
            return;
        }

        var expectedApiKey = _configuration["Security:ApiKey"];

        // Eğer ayarlarda API key tanımlı değilse, geliştirme modunda izin ver
        if (string.IsNullOrEmpty(expectedApiKey))
        {
            var env = context.RequestServices.GetService<IWebHostEnvironment>();
            if (env?.IsDevelopment() == true)
            {
                Log.Warning("API Key yapılandırılmamış - Development modunda izin veriliyor");
                await _next(context);
                return;
            }

            Log.Error("API Key yapılandırılmamış - Production'da erişim reddedildi");
            context.Response.StatusCode = 500;
            context.Response.ContentType = "application/json";
            await context.Response.WriteAsync("{\"success\":false,\"message\":\"Sunucu yapılandırma hatası\"}");
            return;
        }

        if (!string.Equals(requestApiKey, expectedApiKey, StringComparison.Ordinal))
        {
            Log.Warning("Geçersiz API Key. Path: {Path}, IP: {IP}", 
                context.Request.Path, 
                context.Connection.RemoteIpAddress);

            context.Response.StatusCode = 401;
            context.Response.ContentType = "application/json";
            await context.Response.WriteAsync("{\"success\":false,\"message\":\"Geçersiz API Key\"}");
            return;
        }

        await _next(context);
    }
}
