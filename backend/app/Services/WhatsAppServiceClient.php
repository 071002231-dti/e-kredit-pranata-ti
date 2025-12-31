<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

/**
 * WhatsAppServiceClient
 *
 * HTTP client for communicating with the WhatsApp microservice.
 * Provides methods to send messages and notifications.
 */
class WhatsAppServiceClient
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp_service.url', 'http://whatsapp-service:8080');
        $this->apiKey = config('services.whatsapp_service.key', '');
        $this->timeout = config('services.whatsapp_service.timeout', 10);
    }

    /**
     * Check if WhatsApp service is available
     */
    public function isAvailable(): bool
    {
        if (empty($this->apiKey)) {
            return false;
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/health");

            return $response->successful();
        } catch (ConnectionException $e) {
            Log::warning('WhatsApp service unavailable', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send a text message to a phone number
     */
    public function sendMessage(string $phone, string $message): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/messages/send", [
                    'phone' => $phone,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('error', 'Unknown error'),
            ];
        } catch (ConnectionException $e) {
            Log::error('Failed to send WhatsApp message', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'WhatsApp service unavailable',
            ];
        }
    }

    /**
     * Send activity status notification
     */
    public function sendActivityNotification(array $data): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/messages/send-notification", [
                    'type' => 'activity_status',
                    'user_id' => $data['user_id'],
                    'activity_id' => $data['activity_id'],
                    'activity_title' => $data['activity_title'],
                    'old_status' => $data['old_status'],
                    'new_status' => $data['new_status'],
                    'credit_points' => $data['credit_points'] ?? 0,
                    'verifier_name' => $data['verifier_name'] ?? null,
                    'comments' => $data['comments'] ?? null,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('error', 'Unknown error'),
            ];
        } catch (ConnectionException $e) {
            Log::error('Failed to send activity notification', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'WhatsApp service unavailable',
            ];
        }
    }

    /**
     * Send a template message
     */
    public function sendTemplateMessage(string $phone, string $templateName, array $parameters = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/messages/send-template", [
                    'phone' => $phone,
                    'template' => $templateName,
                    'parameters' => $parameters,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('error', 'Unknown error'),
            ];
        } catch (ConnectionException $e) {
            Log::error('Failed to send template message', [
                'phone' => $phone,
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'WhatsApp service unavailable',
            ];
        }
    }

    /**
     * Get headers for API requests
     */
    private function getHeaders(): array
    {
        return [
            'X-Service-Key' => $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}
