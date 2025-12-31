<?php

namespace App\Jobs;

use App\Services\WhatsApp\WebhookHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        private array $payload
    ) {}

    public function handle(WebhookHandler $handler): void
    {
        try {
            $handler->processWebhook($this->payload);
        } catch (\Exception $e) {
            Log::error('Failed to process webhook', [
                'error' => $e->getMessage(),
                'payload' => $this->payload,
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Webhook processing failed after all retries', [
            'error' => $exception->getMessage(),
        ]);
    }
}
