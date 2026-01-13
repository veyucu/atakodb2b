<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$userTypes): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized action.');
        }

        // Birden fazla kullanıcı tipi destekleniyor (plasiyer,admin gibi)
        $allowedTypes = count($userTypes) > 0 ? $userTypes : [];
        
        if (!empty($allowedTypes) && !in_array($request->user()->user_type, $allowedTypes)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}












