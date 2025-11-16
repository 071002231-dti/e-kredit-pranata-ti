<?php

namespace App\Jobs;

use App\Services\WhatsApp\WebhookHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessWhatsAppWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $payload
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WebhookHandler $handler): void
    {
        try {
            $handler->processWebhook($this->payload);
        } catch (Exception $e) {
            Log::error('Failed to process WhatsApp webhook', [
                'error' => $e->getMessage(),
                'payload' => $this->payload,
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('WhatsApp webhook job failed after all retries', [
            'error' => $exception->getMessage(),
            'payload' => $this->payload,
        ]);
    }
}
