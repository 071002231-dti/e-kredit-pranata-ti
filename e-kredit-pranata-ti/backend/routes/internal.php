<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Internal\UserInternalController;
use App\Http\Controllers\API\Internal\ActivityInternalController;
use App\Http\Controllers\API\Internal\SchemaInternalController;

/*
|--------------------------------------------------------------------------
| Internal API Routes
|--------------------------------------------------------------------------
|
| These routes are used for internal service-to-service communication.
| They are protected by the internal service key middleware.
|
*/

Route::middleware('internal.service')->prefix('internal')->group(function () {
    // User endpoints
    Route::get('/users/{identifier}', [UserInternalController::class, 'show']);
    Route::post('/users/find', [UserInternalController::class, 'findByCredentials']);

    // Activity endpoints
    Route::get('/users/{userId}/activities', [ActivityInternalController::class, 'index']);
    Route::get('/users/{userId}/stats', [ActivityInternalController::class, 'stats']);
    Route::post('/activities', [ActivityInternalController::class, 'store']);

    // Credit schema endpoints
    Route::get('/credit-schemas', [SchemaInternalController::class, 'index']);
    Route::get('/credit-schemas/categories', [SchemaInternalController::class, 'categories']);
    Route::get('/credit-schemas/{id}', [SchemaInternalController::class, 'show']);

    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => 'e-kredit-main',
            'timestamp' => now()->toISOString(),
        ]);
    });
});
