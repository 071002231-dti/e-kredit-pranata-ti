<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppUser;
use App\Models\WhatsAppMessage;
use App\Services\MainAppClient;
use Illuminate\Support\Facades\Log;

class WebhookHandler
{
    public function __construct(
        private WhatsAppApiService $whatsAppApi,
        private MainAppClient $mainAppClient
    ) {}

    /**
     * Process incoming webhook from Meta
     */
    public function processWebhook(array $payload): void
    {
        if (!isset($payload['entry'])) {
            Log::warning('Invalid webhook payload - no entry');
            return;
        }

        foreach ($payload['entry'] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if ($change['field'] !== 'messages') {
                    continue;
                }

                $value = $change['value'];

                // Process status updates
                if (isset($value['statuses'])) {
                    $this->processStatusUpdates($value['statuses']);
                }

                // Process incoming messages
                if (isset($value['messages'])) {
                    $this->processIncomingMessages($value['messages'], $value['contacts'] ?? []);
                }
            }
        }
    }

    /**
     * Process status updates
     */
    private function processStatusUpdates(array $statuses): void
    {
        foreach ($statuses as $status) {
            WhatsAppMessage::where('whatsapp_message_id', $status['id'])
                ->update([
                    'status' => $status['status'],
                ]);
        }
    }

    /**
     * Process incoming messages
     */
    private function processIncomingMessages(array $messages, array $contacts): void
    {
        foreach ($messages as $message) {
            $from = $message['from'];
            $messageId = $message['id'];
            $type = $message['type'];

            // Log message
            $waMessage = WhatsAppMessage::create([
                'whatsapp_message_id' => $messageId,
                'from_number' => $from,
                'direction' => 'inbound',
                'type' => $type,
                'content' => $this->extractMessageContent($message),
                'metadata' => $message,
                'status' => 'delivered',
            ]);

            // Find or create WhatsApp user
            $whatsappUser = WhatsAppUser::firstOrCreate(
                ['whatsapp_phone' => $from],
                ['notifications_enabled' => true]
            );

            $waMessage->update(['whatsapp_user_id' => $whatsappUser->id]);

            // Update last interaction
            $whatsappUser->update(['last_interaction_at' => now()]);

            // Mark as read
            try {
                $this->whatsAppApi->markAsRead($messageId);
            } catch (\Exception $e) {
                Log::warning('Failed to mark message as read', ['error' => $e->getMessage()]);
            }

            // Handle message based on type
            $this->handleMessage($whatsappUser, $message, $type);
        }
    }

    /**
     * Handle message based on type
     */
    private function handleMessage(WhatsAppUser $user, array $message, string $type): void
    {
        $from = $message['from'];

        try {
            if ($type === 'text') {
                $text = $message['text']['body'] ?? '';
                $this->handleTextMessage($user, $from, $text);
            } elseif ($type === 'interactive') {
                $this->handleInteractiveMessage($user, $from, $message['interactive']);
            } else {
                $this->sendDefaultResponse($from);
            }
        } catch (\Exception $e) {
            Log::error('Error handling message', [
                'from' => $from,
                'error' => $e->getMessage(),
            ]);
            $this->whatsAppApi->sendTextMessage($from, 'Maaf, terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Handle text message
     */
    private function handleTextMessage(WhatsAppUser $user, string $from, string $text): void
    {
        $text = trim($text);

        // Check if it's a command
        if (str_starts_with($text, '/')) {
            $this->handleCommand($user, $from, $text);
            return;
        }

        // Default response
        $this->sendWelcomeMessage($from, $user);
    }

    /**
     * Handle command
     */
    private function handleCommand(WhatsAppUser $user, string $from, string $command): void
    {
        $parts = explode(' ', $command, 2);
        $cmd = strtolower($parts[0]);
        $args = $parts[1] ?? '';

        match ($cmd) {
            '/start', '/menu' => $this->sendWelcomeMessage($from, $user),
            '/register' => $this->handleRegister($user, $from, $args),
            '/stats' => $this->handleStats($user, $from),
            '/activities' => $this->handleActivities($user, $from, $args),
            '/help' => $this->sendHelpMessage($from),
            default => $this->sendUnknownCommandMessage($from),
        };
    }

    /**
     * Handle /register command
     */
    private function handleRegister(WhatsAppUser $user, string $from, string $args): void
    {
        // Parse email and NIP
        $parts = array_filter(array_map('trim', explode(' ', $args)));

        if (count($parts) < 2) {
            $this->whatsAppApi->sendTextMessage($from,
                "Format salah. Gunakan:\n/register email@domain.com 123456789012345678\n\n" .
                "Contoh:\n/register budi@bps.go.id 199001012020011001"
            );
            return;
        }

        $email = $parts[0];
        $nip = $parts[1];

        // Find user in main app
        $mainAppUser = $this->mainAppClient->getUserByCredentials($email, $nip);

        if (!$mainAppUser) {
            $this->whatsAppApi->sendTextMessage($from,
                "❌ Data tidak ditemukan.\n\n" .
                "Pastikan email dan NIP yang Anda masukkan sesuai dengan data di sistem e-Kredit."
            );
            return;
        }

        // Update WhatsApp user
        $user->update([
            'main_app_user_id' => $mainAppUser['id'],
            'verified' => true,
            'cached_name' => $mainAppUser['name'],
            'cached_email' => $mainAppUser['email'],
            'cached_nip' => $mainAppUser['nip'],
            'cached_jenjang_jabatan' => $mainAppUser['jenjang_jabatan'],
            'cache_updated_at' => now(),
        ]);

        $this->whatsAppApi->sendTextMessage($from,
            "✅ *Registrasi Berhasil!*\n\n" .
            "Halo {$mainAppUser['name']}!\n" .
            "Akun Anda berhasil terhubung dengan e-Kredit Pranata TI.\n\n" .
            "Ketik /stats untuk melihat statistik kredit Anda."
        );
    }

    /**
     * Handle /stats command
     */
    private function handleStats(WhatsAppUser $user, string $from): void
    {
        if (!$user->main_app_user_id) {
            $this->whatsAppApi->sendTextMessage($from,
                "❌ Anda belum terdaftar.\n\nKetik /register email nip untuk mendaftar."
            );
            return;
        }

        $stats = $this->mainAppClient->getUserStats($user->main_app_user_id);

        if (!$stats) {
            $this->whatsAppApi->sendTextMessage($from, "Gagal mengambil statistik. Silakan coba lagi.");
            return;
        }

        $message = MessageBuilder::formatUserStatistics([
            'total_kredit' => $stats['credits']['total'] ?? 0,
            'unsur_utama' => $stats['credits']['utama'] ?? 0,
            'unsur_penunjang' => $stats['credits']['penunjang'] ?? 0,
            'persentase_utama' => $stats['compliance']['percentage_utama'] ?? 0,
            'persentase_penunjang' => $stats['compliance']['percentage_penunjang'] ?? 0,
            'is_compliant' => $stats['compliance']['is_compliant'] ?? false,
        ]);

        $this->whatsAppApi->sendTextMessage($from, $message);
    }

    /**
     * Handle /activities command
     */
    private function handleActivities(WhatsAppUser $user, string $from, string $args): void
    {
        if (!$user->main_app_user_id) {
            $this->whatsAppApi->sendTextMessage($from,
                "❌ Anda belum terdaftar.\n\nKetik /register email nip untuk mendaftar."
            );
            return;
        }

        $page = is_numeric(trim($args)) ? (int)$args : 1;
        $result = $this->mainAppClient->getUserActivities($user->main_app_user_id, $page);

        $activities = array_map(function ($a) {
            return [
                'nama_kegiatan' => $a['title'] ?? 'N/A',
                'status' => $a['status'] ?? 'pending',
                'total_kredit' => $a['credit_points'] ?? 0,
                'tanggal' => isset($a['created_at']) ? date('d/m/Y', strtotime($a['created_at'])) : '-',
            ];
        }, $result['activities'] ?? []);

        $message = MessageBuilder::formatRecentActivities($activities);
        $this->whatsAppApi->sendTextMessage($from, $message);
    }

    /**
     * Handle interactive message
     */
    private function handleInteractiveMessage(WhatsAppUser $user, string $from, array $interactive): void
    {
        $type = $interactive['type'] ?? '';

        if ($type === 'button_reply') {
            $buttonId = $interactive['button_reply']['id'] ?? '';
            // Handle button response
            Log::info('Button clicked', ['button_id' => $buttonId]);
        } elseif ($type === 'list_reply') {
            $listId = $interactive['list_reply']['id'] ?? '';
            // Handle list selection
            Log::info('List item selected', ['list_id' => $listId]);
        }

        $this->sendWelcomeMessage($from, $user);
    }

    /**
     * Send welcome message
     */
    private function sendWelcomeMessage(string $to, WhatsAppUser $user): void
    {
        $name = $user->cached_name ?? 'Pengguna';

        if (!$user->verified) {
            $message = "👋 Selamat datang di *e-Kredit Pranata TI*!\n\n" .
                "Untuk mulai menggunakan layanan, silakan daftar dengan mengetik:\n" .
                "/register email@domain.com NIP\n\n" .
                "Contoh:\n/register budi@bps.go.id 199001012020011001\n\n" .
                "Ketik /help untuk bantuan.";
        } else {
            $message = MessageBuilder::formatWelcomeMessage($name) . "\n\n" .
                "*Perintah yang tersedia:*\n" .
                "📊 /stats - Lihat statistik kredit\n" .
                "📋 /activities - Lihat aktivitas\n" .
                "❓ /help - Bantuan";
        }

        $this->whatsAppApi->sendTextMessage($to, $message);
    }

    /**
     * Send help message
     */
    private function sendHelpMessage(string $to): void
    {
        $message = MessageBuilder::formatHelpMessage();
        $this->whatsAppApi->sendTextMessage($to, $message);
    }

    /**
     * Send unknown command message
     */
    private function sendUnknownCommandMessage(string $to): void
    {
        $this->whatsAppApi->sendTextMessage($to,
            "❓ Perintah tidak dikenali.\n\nKetik /help untuk melihat daftar perintah."
        );
    }

    /**
     * Send default response
     */
    private function sendDefaultResponse(string $to): void
    {
        $this->whatsAppApi->sendTextMessage($to,
            "Maaf, saya hanya bisa memproses pesan teks.\n\nKetik /help untuk bantuan."
        );
    }

    /**
     * Extract message content
     */
    private function extractMessageContent(array $message): ?string
    {
        $type = $message['type'];

        return match ($type) {
            'text' => $message['text']['body'] ?? null,
            'image' => $message['image']['caption'] ?? '[Image]',
            'document' => $message['document']['caption'] ?? '[Document]',
            'audio' => '[Audio]',
            'video' => $message['video']['caption'] ?? '[Video]',
            'interactive' => json_encode($message['interactive'] ?? []),
            default => null,
        };
    }
}
