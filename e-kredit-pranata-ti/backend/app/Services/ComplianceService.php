<?php

namespace App\Services;

use App\Models\User;
use App\Models\Activity;
use App\Models\CreditSchema;

/**
 * ComplianceService
 *
 * Handles compliance validation based on PR No. 3 Tahun 2025:
 * - Unsur Utama: Minimal 80% dari total angka kredit
 * - Unsur Penunjang: Maksimal 20% dari total angka kredit
 * - Position-based recommendations
 */
class ComplianceService
{
    // Jenjang jabatan order (from lowest to highest)
    private const JENJANG_ORDER = [
        'II/a' => ['name' => 'PK Pelaksana Pemula', 'golongan' => 'II/a', 'min_credit' => 25],
        'II/b' => ['name' => 'PK Pelaksana', 'golongan' => 'II/b', 'min_credit' => 40],
        'II/c' => ['name' => 'PK Pelaksana', 'golongan' => 'II/c', 'min_credit' => 60],
        'II/d' => ['name' => 'PK Pelaksana', 'golongan' => 'II/d', 'min_credit' => 80],
        'III/a' => ['name' => 'PK Pelaksana Lanjutan', 'golongan' => 'III/a', 'min_credit' => 100],
        'III/b' => ['name' => 'PK Pelaksana Lanjutan', 'golongan' => 'III/b', 'min_credit' => 150],
        'III/c' => ['name' => 'PK Pelaksana Lanjutan', 'golongan' => 'III/c', 'min_credit' => 200],
        'III/d' => ['name' => 'PK Penyelia', 'golongan' => 'III/d', 'min_credit' => 300],
        'IV/a' => ['name' => 'PK Pertama', 'golongan' => 'IV/a', 'min_credit' => 400],
        'IV/b' => ['name' => 'PK Madya', 'golongan' => 'IV/b', 'min_credit' => 550],
        'IV/c' => ['name' => 'PK Madya', 'golongan' => 'IV/c', 'min_credit' => 700],
        'IV/d' => ['name' => 'Pranata Komputer', 'golongan' => 'IV/d', 'min_credit' => 850],
        'IV/e' => ['name' => 'Pranata Komputer Utama', 'golongan' => 'IV/e', 'min_credit' => 1050],
    ];

    /**
     * Validate compliance for given credits
     */
    public function validateCompliance(float $creditUtama, float $creditPenunjang): array
    {
        $totalCredit = $creditUtama + $creditPenunjang;

        if ($totalCredit == 0) {
            return [
                'is_compliant' => true,
                'percentage_utama' => 0,
                'percentage_penunjang' => 0,
                'message' => 'Belum ada kredit',
            ];
        }

        $percentageUtama = ($creditUtama / $totalCredit) * 100;
        $percentagePenunjang = ($creditPenunjang / $totalCredit) * 100;

        $isCompliant = $percentageUtama >= 80 && $percentagePenunjang <= 20;

        $message = $isCompliant
            ? 'Memenuhi aturan 80/20'
            : sprintf(
                'Tidak memenuhi aturan! Unsur Utama: %.1f%% (min 80%%), Unsur Penunjang: %.1f%% (max 20%%)',
                $percentageUtama,
                $percentagePenunjang
            );

        return [
            'is_compliant' => $isCompliant,
            'percentage_utama' => round($percentageUtama, 2),
            'percentage_penunjang' => round($percentagePenunjang, 2),
            'total_credit' => $totalCredit,
            'credit_utama' => $creditUtama,
            'credit_penunjang' => $creditPenunjang,
            'message' => $message,
        ];
    }

    /**
     * Calculate user's compliance from their activities
     */
    public function calculateUserCompliance(User $user): array
    {
        $activities = Activity::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with('schema')
            ->get();

        $creditUtama = 0;
        $creditPenunjang = 0;

        foreach ($activities as $activity) {
            if ($activity->schema) {
                if ($activity->schema->unsur_type === 'utama') {
                    $creditUtama += $activity->earned_points;
                } else {
                    $creditPenunjang += $activity->earned_points;
                }
            }
        }

        return $this->validateCompliance($creditUtama, $creditPenunjang);
    }

    /**
     * Get target credits for a jenjang/golongan
     */
    public function getTargetCredits(string $golongan): array
    {
        if (!isset(self::JENJANG_ORDER[$golongan])) {
            return [
                'golongan' => $golongan,
                'jenjang' => 'Unknown',
                'min_credit' => 0,
                'min_credit_utama' => 0,
                'max_credit_penunjang' => 0,
            ];
        }

        $data = self::JENJANG_ORDER[$golongan];
        $minCredit = $data['min_credit'];

        return [
            'golongan' => $golongan,
            'jenjang' => $data['name'],
            'min_credit' => $minCredit,
            'min_credit_utama' => $minCredit * 0.8,  // 80%
            'max_credit_penunjang' => $minCredit * 0.2,  // 20%
        ];
    }

    /**
     * Get next jenjang/golongan
     */
    public function getNextJenjang(string $currentGolongan): ?array
    {
        $golonganKeys = array_keys(self::JENJANG_ORDER);
        $currentIndex = array_search($currentGolongan, $golonganKeys);

        if ($currentIndex === false || $currentIndex >= count($golonganKeys) - 1) {
            return null; // Already at highest
        }

        $nextGolongan = $golonganKeys[$currentIndex + 1];
        return $this->getTargetCredits($nextGolongan);
    }

    /**
     * Check if user can be promoted to next jenjang
     */
    public function canPromote(User $user): array
    {
        if (!$user->golongan) {
            return [
                'can_promote' => false,
                'reason' => 'Golongan belum diset',
            ];
        }

        $compliance = $this->calculateUserCompliance($user);
        $currentTarget = $this->getTargetCredits($user->golongan);
        $nextJenjang = $this->getNextJenjang($user->golongan);

        if (!$nextJenjang) {
            return [
                'can_promote' => false,
                'reason' => 'Sudah di jenjang tertinggi',
                'current_jenjang' => $currentTarget,
            ];
        }

        // Check if has enough credits for current position
        $hasEnoughCredits = $compliance['total_credit'] >= $currentTarget['min_credit'];

        // Check 80/20 compliance
        $isCompliant = $compliance['is_compliant'];

        $canPromote = $hasEnoughCredits && $isCompliant;

        return [
            'can_promote' => $canPromote,
            'current_credit' => $compliance['total_credit'],
            'required_credit' => $currentTarget['min_credit'],
            'next_jenjang' => $nextJenjang,
            'is_compliant' => $isCompliant,
            'compliance' => $compliance,
            'reason' => !$hasEnoughCredits
                ? sprintf('Perlu %.2f kredit lagi', $currentTarget['min_credit'] - $compliance['total_credit'])
                : (!$isCompliant ? 'Tidak memenuhi aturan 80/20' : 'Bisa naik jabatan'),
        ];
    }

    /**
     * Get recommendations for user based on their position
     */
    public function getRecommendations(User $user): array
    {
        $compliance = $this->calculateUserCompliance($user);
        $recommendations = [];

        // Recommendation 1: Compliance
        if (!$compliance['is_compliant'] && $compliance['total_credit'] > 0) {
            if ($compliance['percentage_utama'] < 80) {
                $recommendations[] = [
                    'type' => 'warning',
                    'category' => 'compliance',
                    'message' => sprintf(
                        'Unsur Utama Anda hanya %.1f%%. Tambahkan lebih banyak aktivitas Unsur Utama untuk mencapai minimal 80%%.',
                        $compliance['percentage_utama']
                    ),
                    'action' => 'Fokus pada aktivitas: Pendidikan, Operasi TI, Implementasi, Analisis Sistem',
                ];
            }

            if ($compliance['percentage_penunjang'] > 20) {
                $recommendations[] = [
                    'type' => 'warning',
                    'category' => 'compliance',
                    'message' => sprintf(
                        'Unsur Penunjang Anda %.1f%% (melebihi batas 20%%). Kurangi aktivitas Unsur Penunjang.',
                        $compliance['percentage_penunjang']
                    ),
                    'action' => 'Hindari terlalu banyak aktivitas Penunjang',
                ];
            }
        }

        // Recommendation 2: Progress toward target
        if ($user->golongan) {
            $target = $this->getTargetCredits($user->golongan);
            $progress = ($compliance['total_credit'] / $target['min_credit']) * 100;

            if ($progress < 100) {
                $remaining = $target['min_credit'] - $compliance['total_credit'];
                $recommendations[] = [
                    'type' => 'info',
                    'category' => 'progress',
                    'message' => sprintf(
                        'Progress Anda %.1f%%. Perlu %.2f kredit lagi untuk memenuhi target %s.',
                        $progress,
                        $remaining,
                        $target['jenjang']
                    ),
                    'action' => 'Lanjutkan submit aktivitas',
                ];
            }
        }

        // Recommendation 3: Suggested activities
        $topActivities = CreditSchema::where('unsur_type', 'utama')
            ->orderBy('credit_points', 'desc')
            ->take(5)
            ->get(['activity_name', 'category', 'credit_points']);

        if ($topActivities->isNotEmpty()) {
            $recommendations[] = [
                'type' => 'success',
                'category' => 'suggestions',
                'message' => 'Aktivitas dengan kredit tertinggi yang bisa Anda ambil:',
                'activities' => $topActivities->map(function($schema) {
                    return [
                        'name' => $schema->activity_name,
                        'category' => $schema->category,
                        'points' => $schema->credit_points,
                    ];
                })->toArray(),
            ];
        }

        return $recommendations;
    }

    /**
     * Get all jenjang levels
     */
    public function getAllJenjang(): array
    {
        return array_map(function($golongan, $data) {
            return array_merge(['golongan' => $golongan], $data);
        }, array_keys(self::JENJANG_ORDER), self::JENJANG_ORDER);
    }
}
