<?php

namespace App\Services;

use App\Models\User;
use App\Models\Activity;
use App\Models\CreditBank;
use App\Models\CreditSchema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CreditBankingService
 *
 * Handles credit banking operations:
 * - Banking credits that user cannot use yet
 * - Unlocking banked credits when user gets promoted
 * - Tracking banked vs active credits
 */
class CreditBankingService
{
    protected ComplianceService $complianceService;

    public function __construct(ComplianceService $complianceService)
    {
        $this->complianceService = $complianceService;
    }

    /**
     * Determine if activity credits should be banked
     *
     * Credits are banked if they would cause non-compliance
     * or if user hasn't reached minimum for current position
     */
    public function shouldBankCredits(User $user, Activity $activity): array
    {
        if (!$activity->schema) {
            return [
                'should_bank' => false,
                'reason' => 'No credit schema attached',
            ];
        }

        // Get user's current credits
        $currentUtama = $user->current_credit_utama ?? 0;
        $currentPenunjang = $user->current_credit_penunjang ?? 0;

        // Add this activity's credits
        $newUtama = $currentUtama + ($activity->schema->unsur_type === 'utama' ? $activity->earned_points : 0);
        $newPenunjang = $currentPenunjang + ($activity->schema->unsur_type === 'penunjang' ? $activity->earned_points : 0);

        // Check if this would break compliance
        $compliance = $this->complianceService->validateCompliance($newUtama, $newPenunjang);

        if (!$compliance['is_compliant']) {
            return [
                'should_bank' => true,
                'reason' => sprintf(
                    'Menambahkan kredit ini akan melanggar aturan 80/20. Unsur %s akan menjadi %.1f%%',
                    $activity->schema->unsur_type === 'utama' ? 'Utama' : 'Penunjang',
                    $activity->schema->unsur_type === 'utama' ? $compliance['percentage_utama'] : $compliance['percentage_penunjang']
                ),
                'compliance' => $compliance,
            ];
        }

        // Check if user has golongan set
        if (!$user->golongan) {
            return [
                'should_bank' => false,
                'reason' => 'User belum memiliki golongan, kredit langsung masuk',
            ];
        }

        // Check if exceeds current position's maximum
        $target = $this->complianceService->getTargetCredits($user->golongan);
        $nextJenjang = $this->complianceService->getNextJenjang($user->golongan);

        if ($nextJenjang && $compliance['total_credit'] > $target['min_credit']) {
            return [
                'should_bank' => true,
                'reason' => sprintf(
                    'Kredit melebihi target untuk %s (%s). Akan disimpan untuk jenjang berikutnya: %s',
                    $target['jenjang'],
                    $target['golongan'],
                    $nextJenjang['jenjang']
                ),
                'required_jenjang' => $nextJenjang['jenjang'],
                'required_golongan' => $nextJenjang['golongan'],
            ];
        }

        return [
            'should_bank' => false,
            'reason' => 'Kredit sesuai dengan posisi saat ini',
        ];
    }

    /**
     * Bank credits from an activity
     */
    public function bankCredits(User $user, Activity $activity, string $reason, ?string $requiredJenjang = null, ?string $requiredGolongan = null): CreditBank
    {
        // Determine required jenjang if not provided
        if (!$requiredJenjang && !$requiredGolongan) {
            $nextJenjang = $this->complianceService->getNextJenjang($user->golongan ?? 'II/a');
            if ($nextJenjang) {
                $requiredJenjang = $nextJenjang['jenjang'];
                $requiredGolongan = $nextJenjang['golongan'];
            }
        }

        $creditBank = CreditBank::create([
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'credit_schema_id' => $activity->credit_schema_id,
            'credit_points' => $activity->earned_points,
            'unsur_type' => $activity->schema->unsur_type ?? 'utama',
            'activity_category' => $activity->schema->category ?? 'Unknown',
            'status' => 'banked',
            'required_jenjang' => $requiredJenjang ?? 'PK Pelaksana',
            'required_golongan' => $requiredGolongan,
            'current_jenjang_when_banked' => $user->jenjang_jabatan ?? 'Unknown',
            'banked_at' => now(),
            'reason' => $reason,
        ]);

        // Update user's banked credits
        $this->updateUserBankedCredits($user);

        Log::info("Credits banked for user {$user->id}", [
            'credit_bank_id' => $creditBank->id,
            'points' => $activity->earned_points,
            'reason' => $reason,
        ]);

        return $creditBank;
    }

    /**
     * Unlock banked credits for user (called when promoted)
     */
    public function unlockCreditsForPromotion(User $user, string $newJenjang, string $newGolongan): array
    {
        $bankedCredits = CreditBank::forUser($user->id)
            ->banked()
            ->get();

        $unlocked = [];
        $remainingBanked = [];

        foreach ($bankedCredits as $creditBank) {
            if ($creditBank->canUnlockFor($newJenjang, $newGolongan)) {
                $creditBank->unlock("Auto-unlocked on promotion to {$newJenjang} ({$newGolongan})");
                $unlocked[] = $creditBank;
            } else {
                $remainingBanked[] = $creditBank;
            }
        }

        // Update user credits
        $this->updateUserBankedCredits($user);
        $this->updateUserCurrentCredits($user);

        Log::info("Credits unlocked for user {$user->id} on promotion", [
            'new_jenjang' => $newJenjang,
            'unlocked_count' => count($unlocked),
            'remaining_banked' => count($remainingBanked),
        ]);

        return [
            'unlocked' => $unlocked,
            'unlocked_count' => count($unlocked),
            'unlocked_points' => array_sum(array_map(fn($c) => $c->credit_points, $unlocked)),
            'remaining_banked' => $remainingBanked,
        ];
    }

    /**
     * Update user's banked credits totals
     */
    public function updateUserBankedCredits(User $user): void
    {
        $bankedCredits = CreditBank::forUser($user->id)
            ->banked()
            ->get();

        $bankedUtama = $bankedCredits->where('unsur_type', 'utama')->sum('credit_points');
        $bankedPenunjang = $bankedCredits->where('unsur_type', 'penunjang')->sum('credit_points');

        $user->update([
            'banked_credit_utama' => $bankedUtama,
            'banked_credit_penunjang' => $bankedPenunjang,
            'banked_credit_total' => $bankedUtama + $bankedPenunjang,
        ]);
    }

    /**
     * Update user's current (active) credits from approved activities
     */
    public function updateUserCurrentCredits(User $user): void
    {
        // Get all approved activities that are NOT banked
        $activities = Activity::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with('schema')
            ->get();

        // Get IDs of activities that are banked
        $bankedActivityIds = CreditBank::forUser($user->id)
            ->whereIn('status', ['banked', 'unlocked'])
            ->pluck('activity_id')
            ->toArray();

        $creditUtama = 0;
        $creditPenunjang = 0;

        foreach ($activities as $activity) {
            // Skip if this activity is banked and not unlocked
            if (in_array($activity->id, $bankedActivityIds)) {
                $creditBank = CreditBank::where('activity_id', $activity->id)->first();
                if ($creditBank && $creditBank->status === 'banked') {
                    continue; // Don't count banked credits
                }
            }

            if ($activity->schema) {
                if ($activity->schema->unsur_type === 'utama') {
                    $creditUtama += $activity->earned_points;
                } else {
                    $creditPenunjang += $activity->earned_points;
                }
            }
        }

        $totalCredit = $creditUtama + $creditPenunjang;

        // Calculate compliance
        $compliance = $this->complianceService->validateCompliance($creditUtama, $creditPenunjang);

        // Calculate progress
        $progressPercentage = 0;
        if ($user->target_angka_kredit && $user->target_angka_kredit > 0) {
            $progressPercentage = min(100, ($totalCredit / $user->target_angka_kredit) * 100);
        }

        $user->update([
            'current_credit_utama' => $creditUtama,
            'current_credit_penunjang' => $creditPenunjang,
            'current_credit_total' => $totalCredit,
            'compliance_percentage_utama' => $compliance['percentage_utama'],
            'compliance_percentage_penunjang' => $compliance['percentage_penunjang'],
            'progress_percentage' => round($progressPercentage, 2),
            'is_compliant' => $compliance['is_compliant'],
            'last_credit_updated_at' => now(),
        ]);
    }

    /**
     * Get banked credits summary for user
     */
    public function getBankedCreditsSummary(User $user): array
    {
        $bankedCredits = CreditBank::forUser($user->id)
            ->banked()
            ->with(['activity', 'creditSchema'])
            ->orderBy('banked_at', 'desc')
            ->get();

        $unlockedCredits = CreditBank::forUser($user->id)
            ->unlocked()
            ->with(['activity', 'creditSchema'])
            ->orderBy('unlocked_at', 'desc')
            ->get();

        return [
            'banked' => [
                'count' => $bankedCredits->count(),
                'total_points' => $bankedCredits->sum('credit_points'),
                'utama' => $bankedCredits->where('unsur_type', 'utama')->sum('credit_points'),
                'penunjang' => $bankedCredits->where('unsur_type', 'penunjang')->sum('credit_points'),
                'items' => $bankedCredits,
            ],
            'unlocked' => [
                'count' => $unlockedCredits->count(),
                'total_points' => $unlockedCredits->sum('credit_points'),
                'items' => $unlockedCredits,
            ],
        ];
    }
}
