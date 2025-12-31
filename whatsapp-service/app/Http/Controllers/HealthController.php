<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    /**
     * Health check endpoint
     */
    public function check(): JsonResponse
    {
        $status = 'ok';
        $checks = [];

        // Check database connection
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'ok';
        } catch (\Exception $e) {
            $checks['database'] = 'error';
            $status = 'degraded';
        }

        // Check Redis connection
        try {
            Redis::ping();
            $checks['redis'] = 'ok';
        } catch (\Exception $e) {
            $checks['redis'] = 'error';
            $status = 'degraded';
        }

        // Check main app connectivity
        try {
            $mainAppClient = app(\App\Services\MainAppClient::class);
            $checks['main_app'] = $mainAppClient->isAvailable() ? 'ok' : 'error';
            if ($checks['main_app'] === 'error') {
                $status = 'degraded';
            }
        } catch (\Exception $e) {
            $checks['main_app'] = 'error';
            $status = 'degraded';
        }

        return response()->json([
            'status' => $status,
            'service' => 'whatsapp-service',
            'timestamp' => now()->toISOString(),
            'checks' => $checks,
        ], $status === 'ok' ? 200 : 503);
    }
}
