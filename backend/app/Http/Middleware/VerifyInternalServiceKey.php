<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyInternalServiceKey
{
    /**
     * Handle an incoming request from internal services.
     *
     * Validates that the request comes from an authorized internal service
     * by checking the X-Internal-Key header against the configured key.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $internalKey = config('services.internal_api.key');

        if (empty($internalKey)) {
            return response()->json([
                'error' => 'Internal API not configured',
            ], 500);
        }

        $providedKey = $request->header('X-Internal-Key');

        if (empty($providedKey) || !hash_equals($internalKey, $providedKey)) {
            return response()->json([
                'error' => 'Unauthorized - Invalid internal service key',
            ], 401);
        }

        return $next($request);
    }
}
