<?php

namespace App\Services\WhatsApp;

use App\Models\User;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppUser;
use App\Models\WhatsAppFlow;
use Illuminate\Support\Facades\Log;

class WebhookHandler
{
    protected WhatsAppApiService $api;
    protected FlowService $flowService;

    public function __construct(WhatsAppApiService $api, FlowService $flowService)
    {
        $this->api = $api;
        $this->flowService = $flowService;
    }

    /**
     * Process incoming webhook
     */
    public function processWebhook(array $payload): void
    {
        if (!isset($payload['entry'])) {
            return;
        }

        foreach ($payload['entry'] as $entry) {
            if (!isset($entry['changes'])) {
                continue;
            }

            foreach ($entry['changes'] as $change) {
                $this->processChange($change);
            }
        }
    }

    /**
     * Process a single webhook change
     */
    protected function processChange(array $change): void
    {
        if ($change['field'] !== 'messages') {
            return;
        }

        $value = $change['value'];

        // Process messages
        if (isset($value['messages'])) {
            foreach ($value['messages'] as $message) {
                $this->processIncomingMessage($message);
            }
        }

        // Process message status updates
        if (isset($value['statuses'])) {
            foreach ($value['statuses'] as $status) {
                $this->processMessageStatus($status);
            }
        }
    }

    /**
     * Process incoming message
     */
    protected function processIncomingMessage(array $message): void
    {
        $from = $message['from'];
        $messageId = $message['id'];
        $timestamp = $message['timestamp'];
        $type = $message['type'];

        // Log incoming message
        $this->logMessage([
            'whatsapp_message_id' => $messageId,
            'from_number' => $from,
            'to_number' => config('whatsapp.phone_number_id'),
            'direction' => 'inbound',
            'type' => $type,
            'content' => $this->extractMessageContent($message),
            'metadata' => json_encode($message),
            'status' => 'delivered',
        ]);

        // Find associated user
        $whatsappUser = WhatsAppUser::where('whatsapp_phone', $from)->first();
        $user = $whatsappUser?->user;

        // Mark message as read
        try {
            $this->api->markAsRead($messageId);
        } catch (\Exception $e) {
            Log::error('Failed to mark message as read', ['error' => $e->getMessage()]);
        }

        // Process based on message type
        match ($type) {
            'text' => $this->handleTextMessage($message, $user),
            'interactive' => $this->handleInteractiveMessage($message, $user),
            'image', 'document', 'video', 'audio' => $this->handleMediaMessage($message, $user),
            'button' => $this->handleButtonMessage($message, $user),
            default => $this->handleUnsupportedMessage($message, $user),
        };
    }

    /**
     * Handle text message
     */
    protected function handleTextMessage(array $message, ?User $user): void
    {
        $text = $message['text']['body'];
        $from = $message['from'];

        // Check if it's a command
        if (str_starts_with($text, '/')) {
            $this->handleCommand($text, $from, $user);
            return;
        }

        // If user not registered, prompt registration
        if (!$user) {
            $this->promptRegistration($from);
            return;
        }

        // Handle conversational messages based on session state
        $this->handleConversation($text, $from, $user);
    }

    /**
     * Handle command messages
     */
    protected function handleCommand(string $command, string $from, ?User $user): void
    {
        $parts = explode(' ', $command);
        $cmd = strtolower($parts[0]);

        match ($cmd) {
            '/start', '/menu' => $this->sendMainMenu($from, $user),
            '/register' => $this->handleRegistration($parts, $from),
            '/status' => $this->sendUserStatus($from, $user),
            '/help' => $this->sendHelpMessage($from),
            '/stats' => $this->sendUserStatistics($from, $user),
            '/activities' => $this->sendRecentActivities($from, $user, isset($parts[1]) ? (int)$parts[1] : 1),
            '/detail' => $this->sendActivityDetail($from, $user, $parts[1] ?? null),
            '/submit' => $this->initiateActivitySubmission($from, $user),
            default => $this->sendUnknownCommand($from),
        };
    }

    /**
     * Send main menu
     */
    protected function sendMainMenu(string $to, ?User $user): void
    {
        if (!$user) {
            $this->promptRegistration($to);
            return;
        }

        $interactive = MessageBuilder::replyButtons(
            "Selamat datang di e-Kredit Pranata TI!\n\nSilakan pilih menu:",
            MessageBuilder::mainMenuButtons(),
            "Menu Utama"
        );

        $this->api->sendInteractiveMessage($to, $interactive);
    }

    /**
     * Handle registration
     */
    protected function handleRegistration(array $parts, string $from): void
    {
        if (count($parts) < 3) {
            $message = "❌ Format pendaftaran salah.\n\n" .
                      "*Cara pendaftaran:*\n" .
                      "/register email@example.com NIP123456\n\n" .
                      "Contoh:\n" .
                      "/register john@pranata.id 198501012010011001";
            $this->api->sendTextMessage($from, $message);
            return;
        }

        $email = $parts[1];
        $nip = $parts[2];

        // Find user by email and NIP
        $user = User::where('email', $email)
                   ->where('nip', $nip)
                   ->first();

        if (!$user) {
            $this->api->sendTextMessage($from, "❌ Email atau NIP tidak ditemukan. Pastikan Anda sudah terdaftar di sistem.");
            return;
        }

        // Check if already registered
        if (WhatsAppUser::where('whatsapp_phone', $from)->exists()) {
            $this->api->sendTextMessage($from, "⚠️ Nomor WhatsApp ini sudah terdaftar.");
            return;
        }

        // Create WhatsApp user
        WhatsAppUser::create([
            'user_id' => $user->id,
            'whatsapp_phone' => $from,
            'verified' => true,
            'notifications_enabled' => true,
        ]);

        $welcomeMessage = MessageBuilder::formatWelcomeMessage($user->name);
        $this->api->sendTextMessage($from, $welcomeMessage);

        // Send main menu
        $this->sendMainMenu($from, $user);
    }

    /**
     * Send user status
     */
    protected function sendUserStatus(string $to, ?User $user): void
    {
        if (!$user) {
            $this->promptRegistration($to);
            return;
        }

        $message = "✅ *Status Akun*\n\n" .
                  "*Nama:* {$user->name}\n" .
                  "*NIP:* {$user->nip}\n" .
                  "*Email:* {$user->email}\n" .
                  "*Jenjang:* {$user->jenjang_jabatan}\n\n" .
                  "Akun Anda telah terhubung dengan WhatsApp.";

        $this->api->sendTextMessage($to, $message);
    }

    /**
     * Send help message
     */
    protected function sendHelpMessage(string $to): void
    {
        $message = MessageBuilder::formatHelpMessage();
        $this->api->sendTextMessage($to, $message);
    }

    /**
     * Send user statistics
     */
    protected function sendUserStatistics(string $to, ?User $user): void
    {
        if (!$user) {
            $this->promptRegistration($to);
            return;
        }

        // Calculate statistics
        $stats = $this->calculateUserStatistics($user);

        $message = "📊 *Statistik Anda*\n\n" .
                  "*Total Aktivitas:* {$stats['total_activities']}\n" .
                  "*Menunggu Verifikasi:* {$stats['pending']}\n" .
                  "*Disetujui:* {$stats['approved']}\n" .
                  "*Ditolak:* {$stats['rejected']}\n\n" .
                  "*Total Kredit Disetujui:* {$stats['total_credits']} angka kredit\n" .
                  "*Kredit Unsur Utama:* {$stats['main_credits']} ({$stats['main_percentage']}%)\n" .
                  "*Kredit Penunjang:* {$stats['supporting_credits']} ({$stats['supporting_percentage']}%)\n\n" .
                  ($stats['compliance_status'] === 'compliant'
                    ? "✅ Status: Sesuai Ketentuan"
                    : "⚠️ Status: Belum Memenuhi Ketentuan\n_Unsur Utama harus ≥80%, Penunjang ≤20%_");

        $this->api->sendTextMessage($to, $message);
    }

    /**
     * Calculate user statistics
     */
    protected function calculateUserStatistics(User $user): array
    {
        $activities = $user->activities()->with(['creditSchema', 'latestApproval'])->get();

        $total = $activities->count();
        $pending = $activities->where('status', 'pending')->count();
        $approved = $activities->where('status', 'approved')->count();
        $rejected = $activities->where('status', 'rejected')->count();

        // Calculate credits for approved activities only
        $approvedActivities = $activities->where('status', 'approved');
        $mainCredits = $approvedActivities
            ->filter(fn($a) => $a->creditSchema?->unsur_type === 'utama')
            ->sum(fn($a) => $a->creditSchema?->credit_points ?? 0);

        $supportingCredits = $approvedActivities
            ->filter(fn($a) => $a->creditSchema?->unsur_type === 'penunjang')
            ->sum(fn($a) => $a->creditSchema?->credit_points ?? 0);

        $totalCredits = $mainCredits + $supportingCredits;

        // Calculate percentages
        $mainPercentage = $totalCredits > 0
            ? round(($mainCredits / $totalCredits) * 100, 1)
            : 0;
        $supportingPercentage = $totalCredits > 0
            ? round(($supportingCredits / $totalCredits) * 100, 1)
            : 0;

        // Check compliance (Utama ≥80%, Penunjang ≤20%)
        $complianceStatus = ($mainPercentage >= 80 && $supportingPercentage <= 20)
            ? 'compliant'
            : 'non_compliant';

        return [
            'total_activities' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'total_credits' => number_format($totalCredits, 3),
            'main_credits' => number_format($mainCredits, 3),
            'supporting_credits' => number_format($supportingCredits, 3),
            'main_percentage' => $mainPercentage,
            'supporting_percentage' => $supportingPercentage,
            'compliance_status' => $complianceStatus,
        ];
    }

    /**
     * Send recent activities
     */
    protected function sendRecentActivities(string $to, ?User $user, int $page = 1): void
    {
        if (!$user) {
            $this->promptRegistration($to);
            return;
        }

        $perPage = 5;
        $activities = $user->activities()
            ->with(['creditSchema', 'latestApproval'])
            ->orderBy('created_at', 'desc')
            ->get();

        $total = $activities->count();

        if ($total === 0) {
            $this->api->sendTextMessage($to, "📋 Anda belum memiliki aktivitas yang diajukan.");
            return;
        }

        // Paginate manually
        $offset = ($page - 1) * $perPage;
        $paginatedActivities = $activities->slice($offset, $perPage);
        $totalPages = ceil($total / $perPage);

        $message = "📋 *Aktivitas Terkini* (Halaman {$page}/{$totalPages})\n\n";

        foreach ($paginatedActivities as $index => $activity) {
            $number = $offset + $index + 1;
            $statusIcon = match($activity->status) {
                'approved' => '✅',
                'rejected' => '❌',
                'pending' => '⏳',
                default => '📝',
            };

            $statusText = match($activity->status) {
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'pending' => 'Menunggu',
                default => 'Pending',
            };

            $credits = $activity->creditSchema?->credit_points ?? 0;
            $date = $activity->created_at->format('d/m/Y');

            $message .= "*{$number}. {$activity->title}*\n";
            $message .= "   {$statusIcon} Status: {$statusText}\n";
            $message .= "   💰 Kredit: {$credits} angka kredit\n";
            $message .= "   📅 Tanggal: {$date}\n";
            $message .= "   _Ketik /detail {$activity->id} untuk info lengkap_\n\n";
        }

        // Add navigation buttons if needed
        if ($totalPages > 1) {
            $message .= "---\n";
            if ($page < $totalPages) {
                $message .= "Ketik /activities " . ($page + 1) . " untuk halaman berikutnya";
            }
            if ($page > 1 && $page < $totalPages) {
                $message .= " atau ";
            }
            if ($page > 1) {
                $message .= "Ketik /activities " . ($page - 1) . " untuk halaman sebelumnya";
            }
        }

        $this->api->sendTextMessage($to, $message);
    }

    /**
     * Send activity detail
     */
    protected function sendActivityDetail(string $to, ?User $user, ?string $activityId): void
    {
        if (!$user) {
            $this->promptRegistration($to);
            return;
        }

        if (!$activityId) {
            $this->api->sendTextMessage($to, "❌ Format salah. Gunakan: /detail <ID_AKTIVITAS>\n\nContoh: /detail 123");
            return;
        }

        $activity = $user->activities()
            ->with(['creditSchema', 'latestApproval.verifier'])
            ->find($activityId);

        if (!$activity) {
            $this->api->sendTextMessage($to, "❌ Aktivitas dengan ID {$activityId} tidak ditemukan.");
            return;
        }

        $statusIcon = match($activity->status) {
            'approved' => '✅',
            'rejected' => '❌',
            'pending' => '⏳',
            default => '📝',
        };

        $statusText = match($activity->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'pending' => 'Menunggu Verifikasi',
            default => 'Pending',
        };

        $message = "📄 *Detail Aktivitas #" . $activity->id . "*\n\n";
        $message .= "*Judul:* {$activity->title}\n";
        $message .= "*Status:* {$statusIcon} {$statusText}\n\n";

        if ($activity->creditSchema) {
            $message .= "*Informasi Kredit:*\n";
            $message .= "• Kategori: {$activity->creditSchema->category}\n";
            $message .= "• Jenis: {$activity->creditSchema->activity_name}\n";
            $message .= "• Angka Kredit: {$activity->creditSchema->credit_points}\n";
            $message .= "• Unsur: " . ucfirst($activity->creditSchema->unsur_type) . "\n\n";
        }

        if ($activity->description) {
            $message .= "*Deskripsi:*\n{$activity->description}\n\n";
        }

        $message .= "*Tanggal Pengajuan:*\n{$activity->created_at->format('d/m/Y H:i')}\n\n";

        if ($activity->latestApproval) {
            $approval = $activity->latestApproval;
            $message .= "*Informasi Verifikasi:*\n";
            $message .= "• Verifikator: {$approval->verifier->name}\n";
            $message .= "• Tanggal: " . $approval->approved_at?->format('d/m/Y H:i') . "\n";

            if ($approval->comments) {
                $message .= "• Komentar: {$approval->comments}\n";
            }
        }

        $this->api->sendTextMessage($to, $message);
    }

    /**
     * Initiate activity submission
     */
    protected function initiateActivitySubmission(string $to, ?User $user): void
    {
        if (!$user) {
            $this->promptRegistration($to);
            return;
        }

        // Get published Flow for activity submission
        $flow = WhatsAppFlow::where('status', 'published')
            ->where('category', 'ACTIVITY_SUBMISSION')
            ->first();

        if (!$flow) {
            $this->api->sendTextMessage($to, "📝 Fitur pengajuan aktivitas via Flow sedang dalam proses setup. Silakan coba lagi nanti atau hubungi administrator.");
            Log::warning('No published Flow found for activity submission');
            return;
        }

        try {
            // Generate flow token for user
            $flowToken = $user->id . '_' . time();

            // Get categories data for user's jenjang
            $categories = $this->flowService->getCategoriesData($user->jenjang_jabatan);

            if (empty($categories)) {
                $this->api->sendTextMessage($to, "❌ Tidak ada skema kredit yang tersedia untuk jenjang jabatan Anda saat ini.");
                return;
            }

            // Send Flow message
            $success = $this->flowService->sendFlowMessage(
                $to,
                $flow->flow_id,
                $flowToken,
                ['categories' => $categories]
            );

            if (!$success) {
                $this->api->sendTextMessage($to, "❌ Gagal mengirim form. Silakan coba lagi atau hubungi administrator.");
            }
        } catch (\Exception $e) {
            Log::error('Failed to initiate Flow submission', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            $this->api->sendTextMessage($to, "❌ Terjadi kesalahan. Silakan coba lagi nanti.");
        }
    }

    /**
     * Prompt registration
     */
    protected function promptRegistration(string $to): void
    {
        $message = "👋 Selamat datang!\n\n" .
                  "Untuk menggunakan layanan ini, silakan daftarkan nomor WhatsApp Anda terlebih dahulu.\n\n" .
                  "*Cara pendaftaran:*\n" .
                  "/register email@example.com NIP123456\n\n" .
                  "Pastikan email dan NIP sesuai dengan yang terdaftar di sistem.";

        $this->api->sendTextMessage($to, $message);
    }

    /**
     * Send unknown command response
     */
    protected function sendUnknownCommand(string $to): void
    {
        $message = "❌ Perintah tidak dikenali.\n\n" .
                  "Ketik /help untuk melihat daftar perintah yang tersedia.";
        $this->api->sendTextMessage($to, $message);
    }

    /**
     * Handle interactive message (button clicks, list selections)
     */
    protected function handleInteractiveMessage(array $message, ?User $user): void
    {
        $from = $message['from'];
        $interactive = $message['interactive'];
        $type = $interactive['type'];

        if ($type === 'button_reply') {
            $buttonId = $interactive['button_reply']['id'];
            $this->handleButtonClick($buttonId, $from, $user);
        } elseif ($type === 'list_reply') {
            $listId = $interactive['list_reply']['id'];
            $this->handleListSelection($listId, $from, $user);
        }
    }

    /**
     * Handle button click
     */
    protected function handleButtonClick(string $buttonId, string $from, ?User $user): void
    {
        match ($buttonId) {
            'submit_activity' => $this->initiateActivitySubmission($from, $user),
            'my_stats' => $this->sendUserStatistics($from, $user),
            'recent_activities' => $this->sendRecentActivities($from, $user),
            default => $this->sendMainMenu($from, $user),
        };
    }

    /**
     * Handle list selection
     */
    protected function handleListSelection(string $listId, string $from, ?User $user): void
    {
        // TODO: Implement list handling
        // This will be used for category selection in Phase 3
    }

    /**
     * Handle media message
     */
    protected function handleMediaMessage(array $message, ?User $user): void
    {
        // TODO: Implement media handling
        // This will be used for proof file uploads in Phase 3
    }

    /**
     * Handle button message
     */
    protected function handleButtonMessage(array $message, ?User $user): void
    {
        // Similar to interactive message handling
        $this->handleInteractiveMessage($message, $user);
    }

    /**
     * Handle unsupported message type
     */
    protected function handleUnsupportedMessage(array $message, ?User $user): void
    {
        $from = $message['from'];
        $this->api->sendTextMessage($from, "⚠️ Tipe pesan ini tidak didukung.");
    }

    /**
     * Handle conversation (context-based)
     */
    protected function handleConversation(string $text, string $from, User $user): void
    {
        // TODO: Implement session-based conversation handling
        // This will be implemented in Phase 5
        $this->sendMainMenu($from, $user);
    }

    /**
     * Process message status update
     */
    protected function processMessageStatus(array $status): void
    {
        $messageId = $status['id'];
        $statusValue = $status['status']; // sent, delivered, read, failed

        WhatsAppMessage::where('whatsapp_message_id', $messageId)->update([
            'status' => $statusValue,
        ]);
    }

    /**
     * Extract message content based on type
     */
    protected function extractMessageContent(array $message): ?string
    {
        $type = $message['type'];

        return match ($type) {
            'text' => $message['text']['body'] ?? null,
            'interactive' => json_encode($message['interactive']),
            'image', 'document', 'video', 'audio' => $message[$type]['id'] ?? null,
            default => null,
        };
    }

    /**
     * Log message to database
     */
    protected function logMessage(array $data): void
    {
        try {
            WhatsAppMessage::create($data);
        } catch (\Exception $e) {
            Log::error('Failed to log WhatsApp message', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }
}
