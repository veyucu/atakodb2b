namespace AtakoDB2B.WindowsService.Models;

public class NetsisConfig
{
    public string ConnectionString { get; set; } = string.Empty;
    public int Timeout { get; set; } = 30;
    public bool EnableRetry { get; set; } = true;
    public int MaxRetryCount { get; set; } = 3;
}

public class ApiConfig
{
    public string BaseUrl { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
    public string Password { get; set; } = string.Empty;
    public string DeviceName { get; set; } = "Netsis Windows Service";
    public int Timeout { get; set; } = 60;
    public int MaxRetryCount { get; set; } = 3;
}







