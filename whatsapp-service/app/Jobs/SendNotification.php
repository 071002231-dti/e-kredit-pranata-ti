<?php

namespace App\Jobs;

use App\Models\WhatsAppUser;
use App\Services\WhatsApp\WhatsAppApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        private WhatsAppUser $whatsappUser,
        private array $notificationData
    ) {}

    public function handle(WhatsAppApiService $whatsAppApi): void
    {
        try {
            $message = $this->buildMessage();
            $whatsAppApi->sendTextMessage($this->whatsappUser->whatsapp_phone, $message);

            Log::info('Notification sent', [
                'user_id' => $this->whatsappUser->main_app_user_id,
                'type' => $this->notificationData['type'] ?? 'unknown',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'user_id' => $this->whatsappUser->main_app_user_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function buildMessage(): string
    {
        $data = $this->notificationData;
        $status = $data['new_status'] ?? 'unknown';
        $title = $data['activity_title'] ?? 'Aktivitas';
        $credits = $data['credit_points'] ?? 0;
        $verifier = $data['verifier_name'] ?? 'Verifikator';
        $comments = $data['comments'] ?? null;

        return match ($status) {
            'approved' => $this->buildApprovedMessage($title, $credits, $verifier, $comments),
            'rejected' => $this->buildRejectedMessage($title, $verifier, $comments),
            default => "ℹ️ Status aktivitas '{$title}' diperbarui menjadi: {$status}",
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

        $message .= "Selamat! Angka kredit Anda telah bertambah.";

        return $message;
    }

    private function buildRejectedMessage(string $title, string $verifier, ?string $comments): string
    {
        $message = "❌ *Aktivitas Ditolak*\n\n";
        $message .= "*Aktivitas:* {$title}\n";
        $message .= "*Ditolak oleh:* {$verifier}\n";
        $message .= "*Tanggal:* " . now()->format('d/m/Y H:i') . "\n\n";
        $message .= "*Alasan:*\n" . ($comments ?? 'Tidak ada keterangan');

        return $message;
    }
}
