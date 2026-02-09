<?php

namespace App\Listeners;

use App\Events\ActivityStatusChanged;
use App\Services\WhatsAppServiceClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * PublishActivityStatusToWhatsApp
 *
 * Sends activity status change notifications to the WhatsApp microservice.
 * The microservice handles checking if the user has WhatsApp enabled and sending the actual message.
 */
class PublishActivityStatusToWhatsApp implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    protected WhatsAppServiceClient $whatsappClient;

    public function __construct(WhatsAppServiceClient $whatsappClient)
    {
        $this->whatsappClient = $whatsappClient;
    }

    /**
     * Handle the event.
     */
    public function handle(ActivityStatusChanged $event): void
    {
        // Check if WhatsApp service is configured
        if (empty(config('services.whatsapp_service.key'))) {
            Log::debug('WhatsApp service not configured, skipping notification');
            return;
        }

        // Check if service is available (graceful degradation)
        if (!$this->whatsappClient->isAvailable()) {
            Log::warning('WhatsApp service unavailable, skipping notification', [
                'activity_id' => $event->activity->id,
            ]);
            return;
        }

        $activity = $event->activity->load(['user', 'creditSchema', 'latestApproval.verifier']);

        try {
            $result = $this->whatsappClient->sendActivityNotification([
                'user_id' => $activity->user->id,
                'activity_id' => $activity->id,
                'activity_title' => $activity->title,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
                'credit_points' => $activity->creditSchema?->credit_points ?? 0,
                'verifier_name' => $activity->latestApproval?->verifier?->name,
                'comments' => $activity->latestApproval?->comments,
            ]);

            if ($result['success']) {
                Log::info('Activity notification sent to WhatsApp service', [
                    'activity_id' => $activity->id,
                    'user_id' => $activity->user->id,
                    'status' => $event->newStatus,
                ]);
            } else {
                Log::warning('WhatsApp service returned error', [
                    'activity_id' => $activity->id,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send activity notification to WhatsApp service', [
                'activity_id' => $activity->id,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ActivityStatusChanged $event, \Throwable $exception): void
    {
        Log::error('Failed to publish activity status to WhatsApp after all retries', [
            'activity_id' => $event->activity->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
