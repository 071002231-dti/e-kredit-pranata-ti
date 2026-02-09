<?php

namespace App\Services;

use App\DTOs\ComplianceResultDTO;
use App\DTOs\JenjangTargetDTO;
use App\DTOs\PromotionEligibilityDTO;
use App\DTOs\RecommendationDTO;
use App\Enums\ActivityStatus;
use App\Enums\RecommendationType;
use App\Enums\UnsurType;
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
    /**
     * Jenjang jabatan berdasarkan PR No. 3 Tahun 2025 (Lampiran II)
     * Target Angka Kredit Jabatan Fungsional Pranata TI
     *
     * Struktur:
     * - min_credit: Angka Kredit Minimal
     * - min_utama: Unsur Utama ≥80%
     * - max_penunjang: Unsur Penunjang ≤20%
     * - jabatan_terampil: Jabatan Fungsional Tingkat Terampil (II/a - III/d)
     * - jabatan_ahli: Jabatan Fungsional Tingkat Ahli (III/a - IV/e)
     */
    private const JENJANG_ORDER = [
        // Tingkat Terampil Only (II/a - II/d)
        'II/a' => [
            'golongan' => 'II/a',
            'min_credit' => 25,
            'min_utama' => 20,
            'max_penunjang' => 5,
            'jabatan_terampil' => 'Pranata TI Pelaksana Pemula',
            'jabatan_ahli' => null,
        ],
        'II/b' => [
            'golongan' => 'II/b',
            'min_credit' => 40,
            'min_utama' => 32,
            'max_penunjang' => 8,
            'jabatan_terampil' => 'Pranata TI Pelaksana',
            'jabatan_ahli' => null,
        ],
        'II/c' => [
            'golongan' => 'II/c',
            'min_credit' => 60,
            'min_utama' => 48,
            'max_penunjang' => 12,
            'jabatan_terampil' => 'Pranata TI Pelaksana',
            'jabatan_ahli' => null,
        ],
        'II/d' => [
            'golongan' => 'II/d',
            'min_credit' => 80,
            'min_utama' => 64,
            'max_penunjang' => 16,
            'jabatan_terampil' => 'Pranata TI Pelaksana',
            'jabatan_ahli' => null,
        ],
        // Tingkat Terampil & Ahli (III/a - III/d)
        'III/a' => [
            'golongan' => 'III/a',
            'min_credit' => 100,
            'min_utama' => 80,
            'max_penunjang' => 20,
            'jabatan_terampil' => 'Pranata TI Pelaksana Lanjutan',
            'jabatan_ahli' => 'Pranata TI Pertama',
        ],
        'III/b' => [
            'golongan' => 'III/b',
            'min_credit' => 150,
            'min_utama' => 120,
            'max_penunjang' => 30,
            'jabatan_terampil' => 'Pranata TI Pelaksana Lanjutan',
            'jabatan_ahli' => 'Pranata TI Pertama',
        ],
        'III/c' => [
            'golongan' => 'III/c',
            'min_credit' => 200,
            'min_utama' => 160,
            'max_penunjang' => 40,
            'jabatan_terampil' => 'Pranata TI Penyelia',
            'jabatan_ahli' => 'Pranata TI Muda',
        ],
        'III/d' => [
            'golongan' => 'III/d',
            'min_credit' => 300,
            'min_utama' => 240,
            'max_penunjang' => 60,
            'jabatan_terampil' => 'Pranata TI Penyelia',
            'jabatan_ahli' => 'Pranata TI Muda',
        ],
        // Tingkat Ahli Only (IV/a - IV/e)
        'IV/a' => [
            'golongan' => 'IV/a',
            'min_credit' => 400,
            'min_utama' => 320,
            'max_penunjang' => 80,
            'jabatan_terampil' => null,
            'jabatan_ahli' => 'Pranata TI Madya',
        ],
        'IV/b' => [
            'golongan' => 'IV/b',
            'min_credit' => 550,
            'min_utama' => 440,
            'max_penunjang' => 110,
            'jabatan_terampil' => null,
            'jabatan_ahli' => 'Pranata TI Madya',
        ],
        'IV/c' => [
            'golongan' => 'IV/c',
            'min_credit' => 700,
            'min_utama' => 560,
            'max_penunjang' => 140,
            'jabatan_terampil' => null,
            'jabatan_ahli' => 'Pranata TI Madya',
        ],
        'IV/d' => [
            'golongan' => 'IV/d',
            'min_credit' => 850,
            'min_utama' => 680,
            'max_penunjang' => 170,
            'jabatan_terampil' => null,
            'jabatan_ahli' => 'Pranata TI Utama',
        ],
        'IV/e' => [
            'golongan' => 'IV/e',
            'min_credit' => 1050,
            'min_utama' => 840,
            'max_penunjang' => 210,
            'jabatan_terampil' => null,
            'jabatan_ahli' => 'Pranata TI Utama',
        ],
    ];

    /**
     * Validate compliance for given credits
     */
    public function validateCompliance(float $creditUtama, float $creditPenunjang): ComplianceResultDTO
    {
        $totalCredit = $creditUtama + $creditPenunjang;

        if ($totalCredit == 0) {
            return new ComplianceResultDTO(
                isCompliant: true,
                percentageUtama: 0,
                percentagePenunjang: 0,
                totalCredit: 0,
                creditUtama: 0,
                creditPenunjang: 0,
                message: 'Belum ada kredit',
            );
        }

        $percentageUtama = ($creditUtama / $totalCredit) * 100;
        $percentagePenunjang = ($creditPenunjang / $totalCredit) * 100;

        $minUtama = UnsurType::UTAMA->minPercentage();
        $maxPenunjang = UnsurType::PENUNJANG->maxPercentage();
        $isCompliant = $percentageUtama >= $minUtama && $percentagePenunjang <= $maxPenunjang;

        $message = $isCompliant
            ? 'Memenuhi aturan 80/20'
            : sprintf(
                'Tidak memenuhi aturan! Unsur Utama: %.1f%% (min %.0f%%), Unsur Penunjang: %.1f%% (max %.0f%%)',
                $percentageUtama,
                $minUtama,
                $percentagePenunjang,
                $maxPenunjang
            );

        return new ComplianceResultDTO(
            isCompliant: $isCompliant,
            percentageUtama: round($percentageUtama, 2),
            percentagePenunjang: round($percentagePenunjang, 2),
            totalCredit: $totalCredit,
            creditUtama: $creditUtama,
            creditPenunjang: $creditPenunjang,
            message: $message,
        );
    }

    /**
     * Calculate user's compliance from their activities
     */
    public function calculateUserCompliance(User $user): ComplianceResultDTO
    {
        $activities = Activity::where('user_id', $user->id)
            ->where('status', ActivityStatus::APPROVED->value)
            ->with('schema')
            ->get();

        $creditUtama = 0;
        $creditPenunjang = 0;

        foreach ($activities as $activity) {
            if ($activity->creditSchema) {
                $points = $activity->creditSchema->credit_points;
                if ($activity->creditSchema->unsur_type === UnsurType::UTAMA->value) {
                    $creditUtama += $points;
                } else {
                    $creditPenunjang += $points;
                }
            }
        }

        return $this->validateCompliance($creditUtama, $creditPenunjang);
    }

    /**
     * Get target credits for a jenjang/golongan
     * Sesuai PR No. 3 Tahun 2025 Lampiran II
     *
     * @param string $golongan Golongan/ruang (e.g., 'III/a')
     * @param string|null $tingkat 'terampil' atau 'ahli' (optional, auto-detect if null)
     */
    public function getTargetCredits(string $golongan, ?string $tingkat = null): JenjangTargetDTO
    {
        if (!isset(self::JENJANG_ORDER[$golongan])) {
            return new JenjangTargetDTO(
                golongan: $golongan,
                jenjang: 'Unknown',
                jabatanTerampil: null,
                jabatanAhli: null,
                minCredit: 0,
                minCreditUtama: 0,
                maxCreditPenunjang: 0,
            );
        }

        $data = self::JENJANG_ORDER[$golongan];

        // Determine jabatan name based on tingkat
        $jenjang = $this->getJabatanName($data, $tingkat);

        return new JenjangTargetDTO(
            golongan: $golongan,
            jenjang: $jenjang,
            jabatanTerampil: $data['jabatan_terampil'],
            jabatanAhli: $data['jabatan_ahli'],
            minCredit: $data['min_credit'],
            minCreditUtama: $data['min_utama'],
            maxCreditPenunjang: $data['max_penunjang'],
        );
    }

    /**
     * Get jabatan name based on tingkat preference
     */
    private function getJabatanName(array $data, ?string $tingkat = null): string
    {
        // If tingkat specified, return that
        if ($tingkat === 'terampil' && $data['jabatan_terampil']) {
            return $data['jabatan_terampil'];
        }
        if ($tingkat === 'ahli' && $data['jabatan_ahli']) {
            return $data['jabatan_ahli'];
        }

        // Auto-detect: prefer ahli if available, otherwise terampil
        return $data['jabatan_ahli'] ?? $data['jabatan_terampil'] ?? 'Unknown';
    }

    /**
     * Get next jenjang/golongan
     */
    public function getNextJenjang(string $currentGolongan): ?JenjangTargetDTO
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
    public function canPromote(User $user): PromotionEligibilityDTO
    {
        if (!$user->golongan) {
            return new PromotionEligibilityDTO(
                canPromote: false,
                reason: 'Golongan belum diset',
                currentCredit: 0,
                requiredCredit: 0,
                nextJenjang: null,
                isCompliant: false,
            );
        }

        $compliance = $this->calculateUserCompliance($user);
        $currentTarget = $this->getTargetCredits($user->golongan);
        $nextJenjang = $this->getNextJenjang($user->golongan);

        if (!$nextJenjang) {
            return new PromotionEligibilityDTO(
                canPromote: false,
                reason: 'Sudah di jenjang tertinggi',
                currentCredit: $compliance->totalCredit,
                requiredCredit: $currentTarget->minCredit,
                nextJenjang: null,
                isCompliant: $compliance->isCompliant,
                compliance: $compliance,
                currentJenjang: $currentTarget,
            );
        }

        // Check if has enough credits for current position
        $hasEnoughCredits = $compliance->totalCredit >= $currentTarget->minCredit;

        // Check 80/20 compliance
        $isCompliant = $compliance->isCompliant;

        $canPromote = $hasEnoughCredits && $isCompliant;

        $reason = !$hasEnoughCredits
            ? sprintf('Perlu %.2f kredit lagi', $currentTarget->minCredit - $compliance->totalCredit)
            : (!$isCompliant ? 'Tidak memenuhi aturan 80/20' : 'Bisa naik jabatan');

        return new PromotionEligibilityDTO(
            canPromote: $canPromote,
            reason: $reason,
            currentCredit: $compliance->totalCredit,
            requiredCredit: $currentTarget->minCredit,
            nextJenjang: $nextJenjang,
            isCompliant: $isCompliant,
            compliance: $compliance,
            currentJenjang: $currentTarget,
        );
    }

    /**
     * Get recommendations for user based on their position
     * 
     * @return array<RecommendationDTO>
     */
    public function getRecommendations(User $user): array
    {
        $compliance = $this->calculateUserCompliance($user);
        $recommendations = [];

        $minUtama = UnsurType::UTAMA->minPercentage();
        $maxPenunjang = UnsurType::PENUNJANG->maxPercentage();

        // Recommendation 1: Compliance
        if (!$compliance->isCompliant && $compliance->totalCredit > 0) {
            if ($compliance->percentageUtama < $minUtama) {
                $recommendations[] = new RecommendationDTO(
                    type: RecommendationType::WARNING,
                    category: 'compliance',
                    message: sprintf(
                        'Unsur Utama Anda hanya %.1f%%. Tambahkan lebih banyak aktivitas Unsur Utama untuk mencapai minimal %.0f%%.',
                        $compliance->percentageUtama,
                        $minUtama
                    ),
                    action: 'Fokus pada aktivitas: Pendidikan, Operasi TI, Implementasi, Analisis Sistem',
                );
            }

            if ($compliance->percentagePenunjang > $maxPenunjang) {
                $recommendations[] = new RecommendationDTO(
                    type: RecommendationType::WARNING,
                    category: 'compliance',
                    message: sprintf(
                        'Unsur Penunjang Anda %.1f%% (melebihi batas %.0f%%). Kurangi aktivitas Unsur Penunjang.',
                        $compliance->percentagePenunjang,
                        $maxPenunjang
                    ),
                    action: 'Hindari terlalu banyak aktivitas Penunjang',
                );
            }
        }

        // Recommendation 2: Progress toward target
        if ($user->golongan) {
            $target = $this->getTargetCredits($user->golongan);
            $totalCredit = $compliance->totalCredit;
            $progress = $target->minCredit > 0 ? ($totalCredit / $target->minCredit) * 100 : 0;

            if ($progress < 100) {
                $remaining = $target->minCredit - $totalCredit;
                $recommendations[] = new RecommendationDTO(
                    type: RecommendationType::INFO,
                    category: 'progress',
                    message: sprintf(
                        'Progress Anda %.1f%%. Perlu %.2f kredit lagi untuk memenuhi target %s.',
                        $progress,
                        $remaining,
                        $target->jenjang
                    ),
                    action: 'Lanjutkan submit aktivitas',
                );
            }
        }

        // Recommendation 3: Suggested activities
        $topActivities = CreditSchema::where('unsur_type', UnsurType::UTAMA->value)
            ->orderBy('credit_points', 'desc')
            ->take(5)
            ->get(['activity_name', 'category', 'credit_points']);

        if ($topActivities->isNotEmpty()) {
            $recommendations[] = new RecommendationDTO(
                type: RecommendationType::SUCCESS,
                category: 'suggestions',
                message: 'Aktivitas dengan kredit tertinggi yang bisa Anda ambil:',
                activities: $topActivities->map(function($schema) {
                    return [
                        'name' => $schema->activity_name,
                        'category' => $schema->category,
                        'points' => $schema->credit_points,
                    ];
                })->toArray(),
            );
        }

        return $recommendations;
    }

    /**
     * Get all jenjang levels
     * Sesuai PR No. 3 Tahun 2025 Lampiran II
     */
    public function getAllJenjang(): array
    {
        return array_map(function($golongan, $data) {
            return [
                'golongan' => $golongan,
                'min_credit' => $data['min_credit'],
                'min_utama' => $data['min_utama'],
                'max_penunjang' => $data['max_penunjang'],
                'jabatan_terampil' => $data['jabatan_terampil'],
                'jabatan_ahli' => $data['jabatan_ahli'],
                // Backward compatibility - use ahli name if available
                'jenjang' => $data['jabatan_ahli'] ?? $data['jabatan_terampil'],
            ];
        }, array_keys(self::JENJANG_ORDER), self::JENJANG_ORDER);
    }

    /**
     * Get jenjang by tingkat (terampil/ahli)
     */
    public function getJenjangByTingkat(string $tingkat): array
    {
        $result = [];
        foreach (self::JENJANG_ORDER as $golongan => $data) {
            $jabatan = $tingkat === 'terampil' ? $data['jabatan_terampil'] : $data['jabatan_ahli'];
            if ($jabatan) {
                $result[] = [
                    'golongan' => $golongan,
                    'jabatan' => $jabatan,
                    'min_credit' => $data['min_credit'],
                    'min_utama' => $data['min_utama'],
                    'max_penunjang' => $data['max_penunjang'],
                ];
            }
        }
        return $result;
    }
}
