<?php

namespace App\Services\WhatsApp;

class MessageBuilder
{
    /**
     * Build reply buttons interactive message
     */
    public static function replyButtons(string $bodyText, array $buttons, ?string $headerText = null, ?string $footerText = null): array
    {
        $interactive = [
            'type' => 'button',
            'body' => [
                'text' => $bodyText,
            ],
            'action' => [
                'buttons' => array_map(function ($button, $index) {
                    return [
                        'type' => 'reply',
                        'reply' => [
                            'id' => $button['id'] ?? 'btn_' . $index,
                            'title' => $button['title'],
                        ],
                    ];
                }, $buttons, array_keys($buttons)),
            ],
        ];

        if ($headerText) {
            $interactive['header'] = [
                'type' => 'text',
                'text' => $headerText,
            ];
        }

        if ($footerText) {
            $interactive['footer'] = [
                'text' => $footerText,
            ];
        }

        return $interactive;
    }

    /**
     * Build list interactive message
     */
    public static function listMessage(string $bodyText, string $buttonText, array $sections, ?string $headerText = null, ?string $footerText = null): array
    {
        $interactive = [
            'type' => 'list',
            'body' => [
                'text' => $bodyText,
            ],
            'action' => [
                'button' => $buttonText,
                'sections' => $sections,
            ],
        ];

        if ($headerText) {
            $interactive['header'] = [
                'type' => 'text',
                'text' => $headerText,
            ];
        }

        if ($footerText) {
            $interactive['footer'] = [
                'text' => $footerText,
            ];
        }

        return $interactive;
    }

    /**
     * Build menu sections for activity categories
     */
    public static function activityCategoriesMenu(): array
    {
        return [
            [
                'title' => 'Kategori Aktivitas',
                'rows' => [
                    [
                        'id' => 'cat_unsur_utama',
                        'title' => 'Unsur Utama',
                        'description' => 'Aktivitas utama sesuai jabatan',
                    ],
                    [
                        'id' => 'cat_penunjang',
                        'title' => 'Unsur Penunjang',
                        'description' => 'Aktivitas penunjang',
                    ],
                ],
            ],
        ];
    }

    /**
     * Build main menu buttons
     */
    public static function mainMenuButtons(): array
    {
        return [
            [
                'id' => 'submit_activity',
                'title' => '📝 Submit Aktivitas',
            ],
            [
                'id' => 'my_stats',
                'title' => '📊 Statistik Saya',
            ],
            [
                'id' => 'recent_activities',
                'title' => '📋 Aktivitas Terkini',
            ],
        ];
    }

    /**
     * Build template message components
     */
    public static function templateComponents(array $parameters): array
    {
        $components = [];

        if (isset($parameters['header'])) {
            $components[] = [
                'type' => 'header',
                'parameters' => array_map(fn($param) => ['type' => 'text', 'text' => $param], $parameters['header']),
            ];
        }

        if (isset($parameters['body'])) {
            $components[] = [
                'type' => 'body',
                'parameters' => array_map(fn($param) => ['type' => 'text', 'text' => $param], $parameters['body']),
            ];
        }

        if (isset($parameters['button'])) {
            foreach ($parameters['button'] as $index => $param) {
                $components[] = [
                    'type' => 'button',
                    'sub_type' => 'url',
                    'index' => $index,
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => $param,
                        ],
                    ],
                ];
            }
        }

        return $components;
    }

    /**
     * Format activity submission confirmation message
     */
    public static function formatActivitySubmitted(array $activity): string
    {
        return "✅ *Aktivitas Berhasil Diajukan*\n\n" .
               "*Jenis Aktivitas:* {$activity['nama_kegiatan']}\n" .
               "*Jumlah:* {$activity['jumlah']} {$activity['satuan_hasil']}\n" .
               "*Angka Kredit:* {$activity['total_kredit']}\n" .
               "*Kategori:* {$activity['kategori']}\n\n" .
               "Aktivitas Anda sedang menunggu verifikasi dari atasan.";
    }

    /**
     * Format activity approved message
     */
    public static function formatActivityApproved(array $activity): string
    {
        return "🎉 *Aktivitas Disetujui*\n\n" .
               "*Jenis Aktivitas:* {$activity['nama_kegiatan']}\n" .
               "*Angka Kredit:* {$activity['total_kredit']}\n\n" .
               "Selamat! Aktivitas Anda telah disetujui dan angka kredit telah ditambahkan.";
    }

    /**
     * Format activity rejected message
     */
    public static function formatActivityRejected(array $activity, ?string $reason = null): string
    {
        $message = "❌ *Aktivitas Ditolak*\n\n" .
                  "*Jenis Aktivitas:* {$activity['nama_kegiatan']}\n";

        if ($reason) {
            $message .= "\n*Alasan Penolakan:*\n{$reason}\n";
        }

        $message .= "\nAnda dapat mengajukan kembali aktivitas ini dengan perbaikan yang diperlukan.";

        return $message;
    }

    /**
     * Format user statistics
     */
    public static function formatUserStatistics(array $stats): string
    {
        $message = "📊 *Statistik Angka Kredit Anda*\n\n";
        $message .= "*Total Angka Kredit:* {$stats['total_kredit']}\n";
        $message .= "*Unsur Utama:* {$stats['unsur_utama']} ({$stats['persentase_utama']}%)\n";
        $message .= "*Unsur Penunjang:* {$stats['unsur_penunjang']} ({$stats['persentase_penunjang']}%)\n\n";

        // Progress bar untuk Unsur Utama
        $progressUtama = self::generateProgressBar($stats['persentase_utama'], 80);
        $message .= "*Target Unsur Utama (min 80%):*\n{$progressUtama}\n\n";

        // Compliance status
        if ($stats['is_compliant']) {
            $message .= "✅ Status: *Memenuhi Persyaratan*";
        } else {
            $message .= "⚠️ Status: *Belum Memenuhi Persyaratan*\n";
            $message .= "Unsur Utama harus minimal 80%";
        }

        return $message;
    }

    /**
     * Generate text-based progress bar
     */
    protected static function generateProgressBar(float $percentage, float $target = 100): string
    {
        $filled = floor($percentage / 10);
        $empty = 10 - $filled;

        $bar = str_repeat('█', $filled) . str_repeat('░', $empty);
        $status = $percentage >= $target ? '✅' : '⚠️';

        return "{$bar} {$percentage}% {$status}";
    }

    /**
     * Format recent activities list
     */
    public static function formatRecentActivities(array $activities): string
    {
        if (empty($activities)) {
            return "📋 *Aktivitas Terkini*\n\nAnda belum memiliki aktivitas yang diajukan.";
        }

        $message = "📋 *Aktivitas Terkini*\n\n";

        foreach ($activities as $index => $activity) {
            $statusIcon = match ($activity['status']) {
                'approved' => '✅',
                'rejected' => '❌',
                'pending' => '⏳',
                default => '•',
            };

            $message .= "{$statusIcon} *{$activity['nama_kegiatan']}*\n";
            $message .= "   Kredit: {$activity['total_kredit']} | Status: " . ucfirst($activity['status']) . "\n";
            $message .= "   Tanggal: {$activity['tanggal']}\n\n";
        }

        return $message;
    }

    /**
     * Format help message
     */
    public static function formatHelpMessage(): string
    {
        return "ℹ️ *Bantuan e-Kredit Pranata TI*\n\n" .
               "*Perintah yang tersedia:*\n\n" .
               "🏠 */menu* atau */start*\n" .
               "   Tampilkan menu utama\n\n" .
               "👤 */status*\n" .
               "   Lihat status akun Anda\n\n" .
               "📊 */stats*\n" .
               "   Lihat statistik lengkap angka kredit:\n" .
               "   • Total aktivitas & status\n" .
               "   • Kredit Unsur Utama & Penunjang\n" .
               "   • Status compliance (min 80% Utama)\n\n" .
               "📋 */activities* [halaman]\n" .
               "   Lihat daftar aktivitas terkini\n" .
               "   Contoh: /activities 2\n\n" .
               "📄 */detail* <ID>\n" .
               "   Lihat detail aktivitas tertentu\n" .
               "   Contoh: /detail 123\n\n" .
               "📝 */submit*\n" .
               "   Ajukan aktivitas baru (akan tersedia via WhatsApp Flow)\n\n" .
               "❓ */help*\n" .
               "   Tampilkan pesan bantuan ini\n\n" .
               "*📋 Ketentuan Angka Kredit:*\n" .
               "• Unsur Utama: minimal 80%\n" .
               "• Unsur Penunjang: maksimal 20%\n" .
               "• Sesuai PR No. 3 Tahun 2025\n\n" .
               "Untuk pertanyaan lebih lanjut, silakan hubungi administrator.";
    }

    /**
     * Format welcome message for new users
     */
    public static function formatWelcomeMessage(string $name): string
    {
        return "👋 *Selamat datang di e-Kredit Pranata TI!*\n\n" .
               "Halo {$name},\n\n" .
               "Anda sekarang dapat mengajukan aktivitas dan memantau angka kredit Anda langsung melalui WhatsApp.\n\n" .
               "Gunakan menu di bawah untuk memulai.";
    }
}
