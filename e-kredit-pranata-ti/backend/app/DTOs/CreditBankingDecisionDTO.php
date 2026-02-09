<?php

namespace App\DTOs;

use App\Enums\CreditBankStatus;

/**
 * CreditBankingDecisionDTO
 * 
 * Data Transfer Object for credit banking decision results.
 * Replaces array returns from CreditBankingService::shouldBankCredits()
 */
readonly class CreditBankingDecisionDTO
{
    public function __construct(
        public bool $shouldBank,
        public string $reason,
        public ?string $requiredJenjang = null,
        public ?string $requiredGolongan = null,
        public ?ComplianceResultDTO $compliance = null,
    ) {}

    /**
     * Create from array (for backward compatibility)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            shouldBank: $data['should_bank'],
            reason: $data['reason'],
            requiredJenjang: $data['required_jenjang'] ?? null,
            requiredGolongan: $data['required_golongan'] ?? null,
            compliance: isset($data['compliance']) ? ComplianceResultDTO::fromArray($data['compliance']) : null,
        );
    }

    /**
     * Convert to array (for API responses)
     */
    public function toArray(): array
    {
        $result = [
            'should_bank' => $this->shouldBank,
            'reason' => $this->reason,
        ];

        if ($this->requiredJenjang !== null) {
            $result['required_jenjang'] = $this->requiredJenjang;
        }

        if ($this->requiredGolongan !== null) {
            $result['required_golongan'] = $this->requiredGolongan;
        }

        if ($this->compliance !== null) {
            $result['compliance'] = $this->compliance->toArray();
        }

        return $result;
    }
}
