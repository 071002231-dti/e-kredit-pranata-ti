<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppWebhook;
use App\Services\WhatsApp\WebhookHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verify webhook (GET request from Meta)
     * This is called when you first set up the webhook in Meta Business Manager
     */
    public function verify(Request $request): string
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('whatsapp.webhook_verify_token');

        // Check if mode and token are correct
        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('Webhook verified successfully');
            return $challenge;
        }

        Log::warning('Webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
        ]);

        abort(403, 'Forbidden');
    }

    /**
     * Handle webhook (POST request from Meta)
     * This is called when messages are sent/received
     */
    public function handle(Request $request): JsonResponse
    {
        // Verify webhook signature if enabled
        if (config('whatsapp.verify_webhook_signature')) {
            if (!$this->verifySignature($request)) {
                Log::warning('Webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        $payload = $request->all();

        // Log incoming webhook
        if (config('whatsapp.logging.enabled')) {
            Log::channel(config('whatsapp.logging.channel'))->info('Webhook received', [
                'payload' => $payload,
            ]);
        }

        // Process webhook asynchronously if queue is enabled
        if (config('whatsapp.queue.enabled')) {
            ProcessWhatsAppWebhook::dispatch($payload)
                ->onQueue(config('whatsapp.queue.name'))
                ->onConnection(config('whatsapp.queue.connection'));
        } else {
            // Process synchronously
            $handler = app(WebhookHandler::class);
            $handler->processWebhook($payload);
        }

        // Always return 200 OK quickly to Meta
        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Verify webhook signature
     * Meta signs all webhook requests with SHA256
     */
    protected function verifySignature(Request $request): bool
    {
        $signature = $request->header('X-Hub-Signature-256');

        if (!$signature) {
            return false;
        }

        $secret = config('whatsapp.webhook_secret');
        $payload = $request->getContent();

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
