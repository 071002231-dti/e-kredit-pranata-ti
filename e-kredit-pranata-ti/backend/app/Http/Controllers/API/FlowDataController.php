<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CreditSchema;
use App\Models\User;
use App\Services\WhatsApp\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlowDataController extends Controller
{
    protected FlowService $flowService;

    public function __construct(FlowService $flowService)
    {
        $this->flowService = $flowService;
    }

    /**
     * Handle Flow data exchange requests
     * This endpoint is called by WhatsApp when user interacts with Flow
     */
    public function dataExchange(Request $request)
    {
        Log::info('Flow data exchange request', $request->all());

        // Validate request signature (important for security)
        if (!$this->validateFlowSignature($request)) {
            return response()->json([
                'error' => 'Invalid signature',
            ], 401);
        }

        $version = $request->input('version');
        $action = $request->input('action');
        $flowToken = $request->input('flow_token');
        $screen = $request->input('screen');
        $data = $request->input('data', []);

        // Handle different actions
        return match ($action) {
            'INIT' => $this->handleInit($flowToken, $screen, $data),
            'data_exchange' => $this->handleDataExchange($flowToken, $screen, $data),
            default => response()->json(['error' => 'Unknown action']),
        };
    }

    /**
     * Handle INIT action - first load of Flow
     */
    protected function handleInit(string $flowToken, string $screen, array $data)
    {
        // Decode flow token to get user info
        $userId = $this->decodeFlowToken($flowToken);

        if (!$userId) {
            return response()->json([
                'error' => 'Invalid flow token',
            ], 401);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        // Get credit schemas available for user's jenjang
        $categories = $this->flowService->getCategoriesData($user->jenjang_jabatan);

        return response()->json([
            'version' => '3.0',
            'screen' => $screen,
            'data' => [
                'categories' => $categories,
            ],
        ]);
    }

    /**
     * Handle data_exchange action - when user selects something
     */
    protected function handleDataExchange(string $flowToken, string $screen, array $data)
    {
        $schemaId = $data['schema_id'] ?? null;

        if ($schemaId) {
            $schema = CreditSchema::find($schemaId);

            if ($schema) {
                return response()->json([
                    'version' => '3.0',
                    'screen' => $screen,
                    'data' => [
                        'schema_details' => [
                            'activity_name' => $schema->activity_name,
                            'credit_points' => $schema->credit_points,
                            'satuan_hasil' => $schema->satuan_hasil,
                            'description' => $schema->description,
                        ],
                    ],
                ]);
            }
        }

        return response()->json([
            'version' => '3.0',
            'screen' => $screen,
            'data' => [],
        ]);
    }

    /**
     * Handle Flow completion - when user submits the form
     */
    public function handleFlowResponse(Request $request)
    {
        Log::info('Flow response received', $request->all());

        $flowToken = $request->input('flow_token');
        $responseData = $request->input('response');

        // Validate signature
        if (!$this->validateFlowSignature($request)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $userId = $this->decodeFlowToken($flowToken);

        if (!$userId) {
            return response()->json(['error' => 'Invalid flow token'], 401);
        }

        // Extract form data
        $schemaId = $responseData['schema_id'] ?? null;
        $title = $responseData['title'] ?? null;
        $description = $responseData['description'] ?? null;
        $quantity = $responseData['quantity'] ?? null;

        if (!$schemaId || !$title || !$description) {
            return response()->json([
                'error' => 'Missing required fields',
            ], 400);
        }

        // Create activity
        $user = User::find($userId);
        $schema = CreditSchema::find($schemaId);

        if (!$user || !$schema) {
            return response()->json(['error' => 'User or schema not found'], 404);
        }

        // Store activity
        $activity = $user->activities()->create([
            'schema_id' => $schemaId,
            'title' => $title,
            'description' => $description,
            'status' => 'pending',
            'submitted_at' => now(),
            'submitted_via' => 'whatsapp',
        ]);

        Log::info('Activity created from Flow', [
            'activity_id' => $activity->id,
            'user_id' => $userId,
            'schema_id' => $schemaId,
        ]);

        // Trigger notification event
        event(new \App\Events\ActivityStatusChanged($activity, 'draft', 'pending'));

        return response()->json([
            'success' => true,
            'activity_id' => $activity->id,
            'message' => 'Activity submitted successfully',
        ]);
    }

    /**
     * Validate Flow signature from WhatsApp
     */
    protected function validateFlowSignature(Request $request): bool
    {
        $signature = $request->header('X-Whatsapp-Signature');

        if (!$signature) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, config('whatsapp.webhook_secret'));

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Decode flow token to get user ID
     * In production, use proper JWT or encrypted tokens
     */
    protected function decodeFlowToken(string $token): ?int
    {
        // For now, simple implementation
        // In production, use JWT or encrypted token with expiry
        $parts = explode('_', $token);
        return isset($parts[0]) ? (int) $parts[0] : null;
    }
}
