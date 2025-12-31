<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppUser;
use App\Services\WhatsApp\WhatsAppApiService;
use App\Jobs\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function __construct(
        private WhatsAppApiService $whatsAppApi
    ) {}

    /**
     * Send a text message (called by main app)
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $result = $this->whatsAppApi->sendTextMessage(
                $validated['phone'],
                $validated['message']
            );

            return response()->json([
                'success' => true,
                'message_id' => $result['messages'][0]['id'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send message', [
                'phone' => $validated['phone'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send activity notification (called by main app)
     */
    public function sendNotification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:activity_status',
            'user_id' => 'required|integer',
            'activity_id' => 'required|integer',
            'activity_title' => 'required|string',
            'old_status' => 'required|string',
            'new_status' => 'required|string',
            'credit_points' => 'nullable|numeric',
            'verifier_name' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

        // Find WhatsApp user
        $whatsappUser = WhatsAppUser::where('main_app_user_id', $validated['user_id'])
            ->where('notifications_enabled', true)
            ->first();

        if (!$whatsappUser) {
            return response()->json([
                'success' => false,
                'error' => 'User not registered for WhatsApp notifications',
            ], 404);
        }

        // Queue the notification
        if (config('whatsapp.queue.enabled')) {
            SendNotification::dispatch($whatsappUser, $validated)
                ->onQueue(config('whatsapp.queue.notifications', 'notifications'));

            return response()->json([
                'success' => true,
                'queued' => true,
            ]);
        }

        // Send synchronously
        try {
            $message = $this->buildNotificationMessage($validated);
            $result = $this->whatsAppApi->sendTextMessage($whatsappUser->whatsapp_phone, $message);

            return response()->json([
                'success' => true,
                'message_id' => $result['messages'][0]['id'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'user_id' => $validated['user_id'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Build notification message
     */
    private function buildNotificationMessage(array $data): string
    {
        $status = $data['new_status'];
        $title = $data['activity_title'];
        $credits = $data['credit_points'] ?? 0;
        $verifier = $data['verifier_name'] ?? 'Verifikator';
        $comments = $data['comments'] ?? null;

        return match ($status) {
            'approved' => $this->buildApprovedMessage($title, $credits, $verifier, $comments),
            'rejected' => $this->buildRejectedMessage($title, $verifier, $comments),
            default => "ℹ️ Status aktivitas '{$title}' telah diperbarui menjadi: {$status}",
        };
    }

    private function buildApprovedMessage(string $title, float $credits, string $verifier, ?string $comments): string
    {
        $message = "🎉 *Aktivitas Disetujui*\n\n";
        $message .= "*Aktivitas:* {$title}\n";
        $message .= "*Angka Kredit:* {$credits}\n";
        $message .= "*Disetujui oleh:* {$verifier}\n";
        $message .= "*Tanggal:* " . now()->format('d/m/Y H:i') . "\n\n";

        if ($comments) {
            $message .= "*Catatan:*\n{$comments}\n\n";
        }

        $message .= "Selamat! Angka kredit Anda telah bertambah.\n\n";
        $message .= "Ketik /stats untuk melihat statistik terbaru.";

        return $message;
    }

    private function buildRejectedMessage(string $title, string $verifier, ?string $comments): string
    {
        $reason = $comments ?? 'Tidak ada keterangan';

        $message = "❌ *Aktivitas Ditolak*\n\n";
        $message .= "*Aktivitas:* {$title}\n";
        $message .= "*Ditolak oleh:* {$verifier}\n";
        $message .= "*Tanggal:* " . now()->format('d/m/Y H:i') . "\n\n";
        $message .= "*Alasan Penolakan:*\n{$reason}\n\n";
        $message .= "Anda dapat mengajukan kembali aktivitas dengan perbaikan yang diperlukan.";

        return $message;
    }
}
