<?php

namespace App\Listeners;

use App\Events\ActivityStatusChanged;
use App\Models\WhatsAppUser;
use App\Services\WhatsApp\WhatsAppApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected WhatsAppApiService $whatsappApi;

    /**
     * Create the event listener.
     */
    public function __construct(WhatsAppApiService $whatsappApi)
    {
        $this->whatsappApi = $whatsappApi;
    }

    /**
     * Handle the event.
     */
    public function handle(ActivityStatusChanged $event): void
    {
        $activity = $event->activity->load(['user', 'creditSchema', 'latestApproval.verifier']);
        $user = $activity->user;

        // Check if user has WhatsApp registered
        $whatsappUser = WhatsAppUser::where('user_id', $user->id)
            ->where('notifications_enabled', true)
            ->first();

        if (!$whatsappUser) {
            Log::info('User does not have WhatsApp notifications enabled', [
                'user_id' => $user->id,
                'activity_id' => $activity->id,
            ]);
            return;
        }

        // Send notification based on new status
        try {
            $message = $this->buildNotificationMessage($activity, $event->newStatus);
            $this->whatsappApi->sendTextMessage($whatsappUser->whatsapp_phone, $message);

            Log::info('WhatsApp notification sent', [
                'user_id' => $user->id,
                'activity_id' => $activity->id,
                'status' => $event->newStatus,
                'phone' => $whatsappUser->whatsapp_phone,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp notification', [
                'user_id' => $user->id,
                'activity_id' => $activity->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build notification message based on status
     */
    protected function buildNotificationMessage($activity, string $status): string
    {
        $activityName = $activity->title;
        $credits = $activity->creditSchema?->credit_points ?? 0;

        return match ($status) {
            'approved' => $this->buildApprovedMessage($activity, $activityName, $credits),
            'rejected' => $this->buildRejectedMessage($activity, $activityName),
            'pending' => $this->buildPendingMessage($activity, $activityName, $credits),
            default => "ℹ️ Status aktivitas '{$activityName}' telah diperbarui menjadi: {$status}",
        };
    }

    /**
     * Build approved notification message
     */
    protected function buildApprovedMessage($activity, string $activityName, float $credits): string
    {
        $verifier = $activity->latestApproval?->verifier->name ?? 'Verifikator';

        $message = "🎉 *Aktivitas Disetujui*\n\n";
        $message .= "*Aktivitas:* {$activityName}\n";
        $message .= "*Angka Kredit:* {$credits}\n";
        $message .= "*Disetujui oleh:* {$verifier}\n";
        $message .= "*Tanggal:* " . now()->format('d/m/Y H:i') . "\n\n";

        if ($activity->latestApproval?->comments) {
            $message .= "*Catatan:*\n{$activity->latestApproval->comments}\n\n";
        }

        $message .= "Selamat! Angka kredit Anda telah bertambah.\n\n";
        $message .= "Ketik /stats untuk melihat statistik terbaru.";

        return $message;
    }

    /**
     * Build rejected notification message
     */
    protected function buildRejectedMessage($activity, string $activityName): string
    {
        $verifier = $activity->latestApproval?->verifier->name ?? 'Verifikator';
        $reason = $activity->latestApproval?->comments ?? 'Tidak ada keterangan';

        $message = "❌ *Aktivitas Ditolak*\n\n";
        $message .= "*Aktivitas:* {$activityName}\n";
        $message .= "*Ditolak oleh:* {$verifier}\n";
        $message .= "*Tanggal:* " . now()->format('d/m/Y H:i') . "\n\n";
        $message .= "*Alasan Penolakan:*\n{$reason}\n\n";
        $message .= "Anda dapat mengajukan kembali aktivitas dengan perbaikan yang diperlukan.\n\n";
        $message .= "Ketik /menu untuk kembali ke menu utama.";

        return $message;
    }

    /**
     * Build pending notification message
     */
    protected function buildPendingMessage($activity, string $activityName, float $credits): string
    {
        $message = "✅ *Aktivitas Berhasil Diajukan*\n\n";
        $message .= "*Aktivitas:* {$activityName}\n";
        $message .= "*Angka Kredit:* {$credits}\n";
        $message .= "*Tanggal:* " . now()->format('d/m/Y H:i') . "\n\n";
        $message .= "Aktivitas Anda sedang menunggu verifikasi.\n\n";
        $message .= "Anda akan menerima notifikasi setelah aktivitas diverifikasi.\n\n";
        $message .= "Ketik /activities untuk melihat status aktivitas Anda.";

        return $message;
    }
}
