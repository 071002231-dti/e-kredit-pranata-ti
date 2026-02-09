<?php

namespace App\DTOs;

/**
 * PromotionEligibilityDTO
 * 
 * Data Transfer Object for promotion eligibility check results.
 * Replaces array returns from ComplianceService::canPromote()
 */
readonly class PromotionEligibilityDTO
{
    public function __construct(
        public bool $canPromote,
        public string $reason,
        public float $currentCredit,
        public float $requiredCredit,
        public ?JenjangTargetDTO $nextJenjang,
        public bool $isCompliant,
        public ?ComplianceResultDTO $compliance = null,
        public ?JenjangTargetDTO $currentJenjang = null,
    ) {}

    /**
     * Create from array (for backward compatibility)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            canPromote: $data['can_promote'],
            reason: $data['reason'],
            currentCredit: $data['current_credit'] ?? 0,
            requiredCredit: $data['required_credit'] ?? 0,
            nextJenjang: isset($data['next_jenjang']) ? JenjangTargetDTO::fromArray($data['next_jenjang']) : null,
            isCompliant: $data['is_compliant'] ?? false,
            compliance: isset($data['compliance']) ? ComplianceResultDTO::fromArray($data['compliance']) : null,
            currentJenjang: isset($data['current_jenjang']) ? JenjangTargetDTO::fromArray($data['current_jenjang']) : null,
        );
    }

    /**
     * Convert to array (for API responses)
     */
    public function toArray(): array
    {
        $result = [
            'can_promote' => $this->canPromote,
            'reason' => $this->reason,
            'current_credit' => $this->currentCredit,
            'required_credit' => $this->requiredCredit,
            'is_compliant' => $this->isCompliant,
        ];

        if ($this->nextJenjang !== null) {
            $result['next_jenjang'] = $this->nextJenjang->toArray();
        }

        if ($this->compliance !== null) {
            $result['compliance'] = $this->compliance->toArray();
        }

        if ($this->currentJenjang !== null) {
            $result['current_jenjang'] = $this->currentJenjang->toArray();
        }

        return $result;
    }

    /**
     * Get remaining credits needed for promotion
     */
    public function getRemainingCredits(): float
    {
        return max(0, $this->requiredCredit - $this->currentCredit);
    }

    /**
     * Get progress percentage toward promotion
     */
    public function getProgressPercentage(): float
    {
        if ($this->requiredCredit <= 0) {
            return 0;
        }

        return min(100, ($this->currentCredit / $this->requiredCredit) * 100);
    }
}
