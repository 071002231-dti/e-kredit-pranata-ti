<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyServiceApiKey
{
    /**
     * Handle an incoming request from main app or other services.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $serviceKey = config('main_app.service_api_key');

        if (empty($serviceKey)) {
            return response()->json([
                'error' => 'Service API not configured',
            ], 500);
        }

        $providedKey = $request->header('X-Service-Key');

        if (empty($providedKey) || !hash_equals($serviceKey, $providedKey)) {
            return response()->json([
                'error' => 'Unauthorized - Invalid service key',
            ], 401);
        }

        return $next($request);
    }
}
