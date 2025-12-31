<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppApiService
{
    protected string $baseUrl;
    protected string $phoneNumberId;
    protected string $apiToken;
    protected int $timeout;

    public function __construct()
    {
        $version = config('whatsapp.api.version');
        $this->baseUrl = config('whatsapp.api.base_url') . '/' . $version;
        $this->phoneNumberId = config('whatsapp.api.phone_number_id');
        $this->apiToken = config('whatsapp.api.token');
        $this->timeout = config('whatsapp.api.timeout', 20);
    }

    /**
     * Send a text message
     */
    public function sendTextMessage(string $to, string $message, bool $previewUrl = false): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'text',
            'text' => [
                'preview_url' => $previewUrl,
                'body' => $message,
            ],
        ];

        return $this->sendRequest('messages', $payload);
    }

    /**
     * Send a template message
     */
    public function sendTemplateMessage(string $to, string $templateName, array $components = [], string $languageCode = null): array
    {
        $languageCode = $languageCode ?? config('whatsapp.language_code', 'id');

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode,
                ],
            ],
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        return $this->sendRequest('messages', $payload);
    }

    /**
     * Send a media message (image, document, video, audio)
     */
    public function sendMediaMessage(string $to, string $mediaType, string $mediaId, ?string $caption = null): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => $mediaType,
            $mediaType => [
                'id' => $mediaId,
            ],
        ];

        if ($caption && in_array($mediaType, ['image', 'document', 'video'])) {
            $payload[$mediaType]['caption'] = $caption;
        }

        return $this->sendRequest('messages', $payload);
    }

    /**
     * Send an interactive message (buttons, list)
     */
    public function sendInteractiveMessage(string $to, array $interactive): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'interactive',
            'interactive' => $interactive,
        ];

        return $this->sendRequest('messages', $payload);
    }

    /**
     * Upload media to WhatsApp
     */
    public function uploadMedia(string $filePath, string $mimeType): array
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->timeout($this->timeout)
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/{$this->phoneNumberId}/media", [
                    'messaging_product' => 'whatsapp',
                    'type' => $mimeType,
                ]);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            $this->logError('Upload media failed', $e);
            throw $e;
        }
    }

    /**
     * Download media from WhatsApp
     */
    public function downloadMedia(string $mediaId): array
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->timeout($this->timeout)
                ->get("{$this->baseUrl}/{$mediaId}");

            $result = $this->handleResponse($response);

            if (isset($result['url'])) {
                $fileResponse = Http::withToken($this->apiToken)
                    ->timeout($this->timeout)
                    ->get($result['url']);

                return [
                    'content' => $fileResponse->body(),
                    'mime_type' => $result['mime_type'] ?? null,
                    'sha256' => $result['sha256'] ?? null,
                    'file_size' => $result['file_size'] ?? null,
                ];
            }

            throw new Exception('Media URL not found in response');
        } catch (Exception $e) {
            $this->logError('Download media failed', $e);
            throw $e;
        }
    }

    /**
     * Mark message as read
     */
    public function markAsRead(string $messageId): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'status' => 'read',
            'message_id' => $messageId,
        ];

        return $this->sendRequest('messages', $payload);
    }

    /**
     * Send a Flow message
     */
    public function sendFlowMessage(string $to, string $flowId, array $flowData = []): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'interactive',
            'interactive' => [
                'type' => 'flow',
                'body' => [
                    'text' => 'Silakan isi form berikut untuk mengajukan aktivitas',
                ],
                'action' => [
                    'name' => 'flow',
                    'parameters' => [
                        'flow_id' => $flowId,
                        'flow_cta' => 'Isi Form',
                        'flow_action' => 'navigate',
                        'flow_action_payload' => [
                            'screen' => 'CATEGORY_SCREEN',
                            'data' => $flowData,
                        ],
                    ],
                ],
            ],
        ];

        return $this->sendRequest('messages', $payload);
    }

    /**
     * Send API request
     */
    protected function sendRequest(string $endpoint, array $payload): array
    {
        try {
            $url = "{$this->baseUrl}/{$this->phoneNumberId}/{$endpoint}";

            $response = Http::withToken($this->apiToken)
                ->timeout($this->timeout)
                ->post($url, $payload);

            $result = $this->handleResponse($response);

            Log::info('WhatsApp API Request', [
                'endpoint' => $endpoint,
                'to' => $payload['to'] ?? null,
                'type' => $payload['type'] ?? null,
            ]);

            return $result;
        } catch (Exception $e) {
            $this->logError('API request failed', $e, ['endpoint' => $endpoint]);
            throw $e;
        }
    }

    /**
     * Handle API response
     */
    protected function handleResponse($response): array
    {
        if ($response->successful()) {
            return $response->json();
        }

        $error = $response->json();
        throw new Exception(
            $error['error']['message'] ?? 'WhatsApp API request failed',
            $error['error']['code'] ?? $response->status()
        );
    }

    /**
     * Format phone number (ensure it has country code, no + or spaces)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (!str_starts_with($phone, '62')) {
            $phone = ltrim($phone, '0');
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Log errors
     */
    protected function logError(string $message, Exception $e, array $context = []): void
    {
        Log::error($message, [
            'exception' => $e->getMessage(),
            'context' => $context,
        ]);
    }
}
