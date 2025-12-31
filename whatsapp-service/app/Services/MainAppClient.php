<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

/**
 * MainAppClient
 *
 * HTTP client for communicating with the main e-kredit application.
 * Fetches user data, activities, credit schemas, and creates activities.
 */
class MainAppClient
{
    private string $baseUrl;
    private string $internalKey;
    private int $timeout;
    private bool $cacheEnabled;
    private int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = config('main_app.url', 'http://app');
        $this->internalKey = config('main_app.internal_key', '');
        $this->timeout = config('main_app.timeout', 10);
        $this->cacheEnabled = config('main_app.cache.enabled', true);
        $this->cacheTtl = config('main_app.cache.ttl', 300);
    }

    /**
     * Get user by ID
     */
    public function getUser(int $userId): ?array
    {
        $cacheKey = "main_app:user:{$userId}";

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->request('GET', "/internal/users/{$userId}");

            if ($response && isset($response['user'])) {
                if ($this->cacheEnabled) {
                    Cache::put($cacheKey, $response['user'], $this->cacheTtl);
                }
                return $response['user'];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get user from main app', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Find user by email and NIP
     */
    public function getUserByCredentials(string $email, string $nip): ?array
    {
        try {
            $response = $this->request('POST', '/internal/users/find', [
                'email' => $email,
                'nip' => $nip,
            ]);

            if ($response && isset($response['user'])) {
                return $response['user'];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to find user by credentials', [
                'email' => $email,
                'nip' => $nip,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get user activities
     */
    public function getUserActivities(int $userId, int $page = 1, ?string $status = null): array
    {
        try {
            $query = ['page' => $page];
            if ($status) {
                $query['status'] = $status;
            }

            $response = $this->request('GET', "/internal/users/{$userId}/activities", $query);

            return $response ?? ['activities' => [], 'pagination' => null];
        } catch (\Exception $e) {
            Log::error('Failed to get user activities', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return ['activities' => [], 'pagination' => null];
        }
    }

    /**
     * Get user statistics
     */
    public function getUserStats(int $userId): ?array
    {
        try {
            $response = $this->request('GET', "/internal/users/{$userId}/stats");
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to get user stats', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get credit schemas
     */
    public function getCreditSchemas(?string $jenjang = null, ?string $unsurType = null): array
    {
        $cacheKey = "main_app:schemas:" . md5($jenjang . $unsurType);

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $query = [];
            if ($jenjang) $query['jenjang'] = $jenjang;
            if ($unsurType) $query['unsur_type'] = $unsurType;

            $response = $this->request('GET', '/internal/credit-schemas', $query);

            if ($response && isset($response['schemas'])) {
                if ($this->cacheEnabled) {
                    Cache::put($cacheKey, $response['schemas'], $this->cacheTtl * 2);
                }
                return $response['schemas'];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get credit schemas', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get schema categories
     */
    public function getSchemaCategories(): array
    {
        $cacheKey = 'main_app:schema_categories';

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->request('GET', '/internal/credit-schemas/categories');

            if ($response && isset($response['categories'])) {
                if ($this->cacheEnabled) {
                    Cache::put($cacheKey, $response['categories'], $this->cacheTtl * 2);
                }
                return $response['categories'];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get schema categories', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Create activity
     */
    public function createActivity(array $data): array
    {
        try {
            $response = $this->request('POST', '/internal/activities', $data);

            if ($response && isset($response['success']) && $response['success']) {
                // Invalidate user cache
                if (isset($data['user_id'])) {
                    Cache::forget("main_app:user:{$data['user_id']}");
                }

                return [
                    'success' => true,
                    'activity' => $response['activity'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $response['error'] ?? 'Failed to create activity',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to create activity', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if main app is available
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/internal/health");

            return $response->successful();
        } catch (ConnectionException $e) {
            return false;
        }
    }

    /**
     * Make HTTP request to main app
     */
    private function request(string $method, string $endpoint, array $data = []): ?array
    {
        $url = "{$this->baseUrl}/api{$endpoint}";

        $http = Http::timeout($this->timeout)
            ->withHeaders($this->getHeaders());

        $response = match (strtoupper($method)) {
            'GET' => $http->get($url, $data),
            'POST' => $http->post($url, $data),
            'PUT' => $http->put($url, $data),
            'DELETE' => $http->delete($url, $data),
            default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
        };

        if ($response->successful()) {
            return $response->json();
        }

        if ($response->status() === 404) {
            return null;
        }

        throw new \Exception(
            $response->json('error', 'Request failed'),
            $response->status()
        );
    }

    /**
     * Get headers for API requests
     */
    private function getHeaders(): array
    {
        return [
            'X-Internal-Key' => $this->internalKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Clear user cache
     */
    public function clearUserCache(int $userId): void
    {
        Cache::forget("main_app:user:{$userId}");
    }
}
