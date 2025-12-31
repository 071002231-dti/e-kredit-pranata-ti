<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\HealthController;

/*
|--------------------------------------------------------------------------
| API Routes - WhatsApp Service
|--------------------------------------------------------------------------
*/

// Meta WhatsApp Webhook routes (public - verified by signature)
Route::get('/webhook', [WebhookController::class, 'verify']);
Route::post('/webhook', [WebhookController::class, 'handle']);

// API routes for main app (protected by service key)
Route::middleware('service.api')->group(function () {
    Route::post('/messages/send', [MessageController::class, 'send']);
    Route::post('/messages/send-notification', [MessageController::class, 'sendNotification']);
    Route::get('/health', [HealthController::class, 'check']);
});

// Public health check (basic)
Route::get('/ping', function () {
    return response()->json(['pong' => true, 'timestamp' => now()->toISOString()]);
});
