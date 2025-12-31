<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWebhook;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Verify webhook (GET request from Meta)
     */
    public function verify(Request $request): string
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('whatsapp.webhook.verify_token')) {
            Log::info('WhatsApp webhook verified');
            return $challenge;
        }

        Log::warning('WhatsApp webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
        ]);

        abort(403, 'Verification failed');
    }

    /**
     * Handle webhook (POST request from Meta)
     */
    public function handle(Request $request): JsonResponse
    {
        // Verify signature if enabled
        if (config('whatsapp.webhook.verify_signature')) {
            if (!$this->verifySignature($request)) {
                Log::warning('Invalid webhook signature');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $payload = $request->all();

        // Log incoming webhook
        Log::info('WhatsApp webhook received', [
            'object' => $payload['object'] ?? null,
        ]);

        // Dispatch to queue for async processing
        if (config('whatsapp.queue.enabled')) {
            ProcessWebhook::dispatch($payload)
                ->onQueue(config('whatsapp.queue.webhooks', 'webhooks'));
        } else {
            // Process synchronously
            app(\App\Services\WhatsApp\WebhookHandler::class)->processWebhook($payload);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Verify webhook signature
     */
    private function verifySignature(Request $request): bool
    {
        $signature = $request->header('X-Hub-Signature-256');

        if (!$signature) {
            return false;
        }

        $payload = $request->getContent();
        $secret = config('whatsapp.webhook.secret');

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
