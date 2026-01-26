<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * ERP API Key doğrulama middleware'i
 * .NET AtakoErpService'den gelen istekleri doğrular
 */
class VerifyErpApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $expectedKey = config('services.erp.api_key');

        // API key kontrolü
        if (empty($expectedKey) || $apiKey !== $expectedKey) {
            Log::warning('ERP API: Geçersiz veya eksik API Key', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Invalid or missing API key'
            ], 401);
        }

        return $next($request);
    }
}
