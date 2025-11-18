<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\ApprovalController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\CreditSchemaController;
use App\Http\Controllers\API\CreditBankController;
use App\Http\Controllers\API\SkpController;
use App\Http\Controllers\API\WhatsAppWebhookController;
use App\Http\Controllers\API\FlowDataController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// WhatsApp Webhook routes (no authentication - verified by signature)
Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify']);
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle']);

// WhatsApp Flow routes (no authentication - verified by signature)
Route::post('/whatsapp/flow/data-exchange', [FlowDataController::class, 'dataExchange']);
Route::post('/whatsapp/flow/response', [FlowDataController::class, 'handleFlowResponse']);

// Public credit schema routes (for reference before login)
Route::get('/credit-schema', [CreditSchemaController::class, 'index']);
Route::get('/credit-schema/categories', [CreditSchemaController::class, 'categories']);
Route::get('/credit-schema/{id}', [CreditSchemaController::class, 'show']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Activity routes (for all authenticated users)
    Route::apiResource('activities', ActivityController::class);

    // Dashboard routes
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::post('/dashboard/validate-compliance', [DashboardController::class, 'validateCompliance']);

    // Credit Bank routes
    Route::prefix('credit-banks')->group(function () {
        Route::get('/', [CreditBankController::class, 'index']); // List banked credits
        Route::get('/summary', [CreditBankController::class, 'summary']); // Get summary
        Route::get('/stats', [CreditBankController::class, 'stats']); // Get statistics
        Route::get('/{id}', [CreditBankController::class, 'show']); // Show detail
        Route::post('/{id}/unlock', [CreditBankController::class, 'unlock']); // Manual unlock (admin only)
    });

    // SKP (Sasaran Kerja Pegawai) routes
    Route::prefix('skp')->group(function () {
        Route::get('/', [SkpController::class, 'index']); // List user's SKPs
        Route::post('/', [SkpController::class, 'store']); // Create new SKP
        Route::get('/stats', [SkpController::class, 'stats']); // Get SKP stats by year
        Route::get('/{id}', [SkpController::class, 'show']); // Show SKP detail
        Route::put('/{id}', [SkpController::class, 'update']); // Update SKP
        Route::delete('/{id}', [SkpController::class, 'destroy']); // Delete SKP
        Route::post('/{id}/submit', [SkpController::class, 'submit']); // Submit for approval

        // SKP Items management
        Route::post('/{id}/items', [SkpController::class, 'addItem']); // Add item
        Route::put('/{skpId}/items/{itemId}', [SkpController::class, 'updateItem']); // Update item
        Route::delete('/{skpId}/items/{itemId}', [SkpController::class, 'removeItem']); // Remove item
    });

    // Approval routes (for verifier and admin only)
    Route::prefix('approvals')->group(function () {
        Route::get('/pending', [ApprovalController::class, 'pending']);
        Route::post('/{id}/approve', [ApprovalController::class, 'approve']);
        Route::post('/{id}/reject', [ApprovalController::class, 'reject']);
    });
});
