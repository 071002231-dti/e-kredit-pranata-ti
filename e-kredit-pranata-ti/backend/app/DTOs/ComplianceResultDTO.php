<?php

namespace App\DTOs;

use App\Enums\UnsurType;

/**
 * ComplianceResultDTO
 * 
 * Data Transfer Object for compliance validation results.
 * Replaces array returns from ComplianceService::validateCompliance()
 */
readonly class ComplianceResultDTO
{
    public function __construct(
        public bool $isCompliant,
        public float $percentageUtama,
        public float $percentagePenunjang,
        public float $totalCredit,
        public float $creditUtama,
        public float $creditPenunjang,
        public string $message,
    ) {}

    /**
     * Create from array (for backward compatibility)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isCompliant: $data['is_compliant'],
            percentageUtama: $data['percentage_utama'],
            percentagePenunjang: $data['percentage_penunjang'],
            totalCredit: $data['total_credit'],
            creditUtama: $data['credit_utama'],
            creditPenunjang: $data['credit_penunjang'],
            message: $data['message'],
        );
    }

    /**
     * Convert to array (for API responses)
     */
    public function toArray(): array
    {
        return [
            'is_compliant' => $this->isCompliant,
            'percentage_utama' => $this->percentageUtama,
            'percentage_penunjang' => $this->percentagePenunjang,
            'total_credit' => $this->totalCredit,
            'credit_utama' => $this->creditUtama,
            'credit_penunjang' => $this->creditPenunjang,
            'message' => $this->message,
        ];
    }

    /**
     * Check if Unsur Utama meets minimum requirement
     */
    public function meetsUtamaRequirement(): bool
    {
        return $this->percentageUtama >= UnsurType::UTAMA->minPercentage();
    }

    /**
     * Check if Unsur Penunjang is within limit
     */
    public function meetsPenunjangRequirement(): bool
    {
        return $this->percentagePenunjang <= UnsurType::PENUNJANG->maxPercentage();
    }
}
