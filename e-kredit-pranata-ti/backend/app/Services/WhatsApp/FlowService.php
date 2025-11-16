<?php

namespace App\Services\WhatsApp;

use App\Models\CreditSchema;
use App\Models\WhatsAppFlow;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlowService
{
    protected WhatsAppApiService $api;
    protected string $flowId;

    public function __construct(WhatsAppApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Get the activity submission Flow JSON
     */
    public function getActivitySubmissionFlowJson(): array
    {
        return [
            'version' => '5.0',
            'data_api_version' => '3.0',
            'routing_model' => [
                'SUBMIT_ACTIVITY' => ['SUBMIT_ACTIVITY'],
            ],
            'screens' => [
                // Screen 1: Category Selection
                [
                    'id' => 'SUBMIT_ACTIVITY',
                    'title' => 'Ajukan Aktivitas',
                    'terminal' => true,
                    'data' => [
                        'categories' => [
                            'type' => 'array',
                            '__example__' => [],
                        ],
                        'schema_id' => [
                            'type' => 'string',
                            '__example__' => '1',
                        ],
                    ],
                    'layout' => [
                        'type' => 'SingleColumnLayout',
                        'children' => [
                            [
                                'type' => 'Form',
                                'name' => 'activity_form',
                                'children' => [
                                    // Category Dropdown
                                    [
                                        'type' => 'Dropdown',
                                        'label' => 'Pilih Skema Kredit',
                                        'required' => true,
                                        'name' => 'schema_id',
                                        'data-source' => '${data.categories}',
                                        'on-select-action' => [
                                            'name' => 'data_exchange',
                                            'payload' => [
                                                'schema_id' => '${form.schema_id}',
                                            ],
                                        ],
                                    ],
                                    // Activity Title
                                    [
                                        'type' => 'TextInput',
                                        'label' => 'Judul Aktivitas',
                                        'required' => true,
                                        'name' => 'title',
                                        'input-type' => 'text',
                                        'helper-text' => 'Berikan judul deskriptif untuk aktivitas',
                                    ],
                                    // Description
                                    [
                                        'type' => 'TextArea',
                                        'label' => 'Deskripsi',
                                        'required' => true,
                                        'name' => 'description',
                                        'helper-text' => 'Jelaskan detail aktivitas yang dilakukan',
                                    ],
                                    // Quantity
                                    [
                                        'type' => 'TextInput',
                                        'label' => 'Jumlah/Volume',
                                        'required' => false,
                                        'name' => 'quantity',
                                        'input-type' => 'number',
                                        'helper-text' => 'Opsional: jumlah output yang dihasilkan',
                                    ],
                                    // Submit Footer
                                    [
                                        'type' => 'Footer',
                                        'label' => 'Kirim',
                                        'on-click-action' => [
                                            'name' => 'complete',
                                            'payload' => [
                                                'schema_id' => '${form.schema_id}',
                                                'title' => '${form.title}',
                                                'description' => '${form.description}',
                                                'quantity' => '${form.quantity}',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Create Flow via WhatsApp Cloud API
     */
    public function createFlow(string $name, string $categoryTag = 'ACTIVITY_SUBMISSION'): ?array
    {
        try {
            $response = Http::withToken(config('whatsapp.api_token'))
                ->post(config('whatsapp.api_base_url') . '/' . config('whatsapp.api_version') . '/' . config('whatsapp.business_account_id') . '/flows', [
                    'name' => $name,
                    'categories' => [$categoryTag],
                ]);

            if ($response->successful()) {
                $flowData = $response->json();

                // Store Flow in database
                WhatsAppFlow::create([
                    'flow_id' => $flowData['id'],
                    'name' => $name,
                    'status' => 'draft',
                    'category' => $categoryTag,
                ]);

                Log::info('Flow created successfully', ['flow_id' => $flowData['id']]);

                return $flowData;
            }

            Log::error('Failed to create Flow', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception creating Flow', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Update Flow JSON
     */
    public function updateFlowJson(string $flowId, array $flowJson): bool
    {
        try {
            $response = Http::withToken(config('whatsapp.api_token'))
                ->post(config('whatsapp.api_base_url') . '/' . config('whatsapp.api_version') . '/' . $flowId . '/assets', [
                    'name' => 'flow.json',
                    'asset_type' => 'FLOW_JSON',
                    'body' => json_encode($flowJson),
                ]);

            if ($response->successful()) {
                Log::info('Flow JSON updated', ['flow_id' => $flowId]);
                return true;
            }

            Log::error('Failed to update Flow JSON', [
                'flow_id' => $flowId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception updating Flow JSON', [
                'flow_id' => $flowId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Publish Flow
     */
    public function publishFlow(string $flowId): bool
    {
        try {
            $response = Http::withToken(config('whatsapp.api_token'))
                ->post(config('whatsapp.api_base_url') . '/' . config('whatsapp.api_version') . '/' . $flowId . '/publish');

            if ($response->successful()) {
                // Update status in database
                WhatsAppFlow::where('flow_id', $flowId)->update(['status' => 'published']);

                Log::info('Flow published', ['flow_id' => $flowId]);
                return true;
            }

            Log::error('Failed to publish Flow', [
                'flow_id' => $flowId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception publishing Flow', [
                'flow_id' => $flowId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get categories data for Flow dropdown
     */
    public function getCategoriesData(string $jenjangJabatan): array
    {
        $schemas = CreditSchema::query()
            ->select('id', 'category', 'activity_name', 'credit_points', 'unsur_type', 'pelaksana')
            ->get()
            ->filter(function ($schema) use ($jenjangJabatan) {
                return $schema->canBePerformedBy($jenjangJabatan);
            });

        $categories = [];
        foreach ($schemas as $schema) {
            $categories[] = [
                'id' => (string) $schema->id,
                'title' => $schema->activity_name,
                'description' => $schema->category . ' - ' . $schema->credit_points . ' AK',
            ];
        }

        return $categories;
    }

    /**
     * Send Flow message to user
     */
    public function sendFlowMessage(string $to, string $flowId, string $flowToken, array $flowData = []): bool
    {
        $message = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'flow',
                'header' => [
                    'type' => 'text',
                    'text' => '📝 Ajukan Aktivitas',
                ],
                'body' => [
                    'text' => 'Silakan isi form berikut untuk mengajukan aktivitas Anda.',
                ],
                'footer' => [
                    'text' => 'e-Kredit Pranata TI',
                ],
                'action' => [
                    'name' => 'flow',
                    'parameters' => [
                        'flow_message_version' => '3',
                        'flow_token' => $flowToken,
                        'flow_id' => $flowId,
                        'flow_cta' => 'Mulai',
                        'flow_action' => 'navigate',
                        'flow_action_payload' => [
                            'screen' => 'SUBMIT_ACTIVITY',
                            'data' => $flowData,
                        ],
                    ],
                ],
            ],
        ];

        try {
            $this->api->sendMessage($to, $message);
            Log::info('Flow message sent', ['to' => $to, 'flow_id' => $flowId]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send Flow message', [
                'to' => $to,
                'flow_id' => $flowId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Generate Flow token for user
     */
    public function generateFlowToken(int $userId): string
    {
        return hash('sha256', $userId . time() . config('app.key'));
    }
}
