<?php

namespace App\DTOs;

/**
 * JenjangTargetDTO
 * 
 * Data Transfer Object for jenjang/golongan target credits.
 * Replaces array returns from ComplianceService::getTargetCredits()
 */
readonly class JenjangTargetDTO
{
    public function __construct(
        public string $golongan,
        public string $jenjang,
        public ?string $jabatanTerampil,
        public ?string $jabatanAhli,
        public float $minCredit,
        public float $minCreditUtama,
        public float $maxCreditPenunjang,
    ) {}

    /**
     * Create from array (for backward compatibility)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            golongan: $data['golongan'],
            jenjang: $data['jenjang'],
            jabatanTerampil: $data['jabatan_terampil'] ?? null,
            jabatanAhli: $data['jabatan_ahli'] ?? null,
            minCredit: $data['min_credit'],
            minCreditUtama: $data['min_credit_utama'] ?? $data['min_utama'],
            maxCreditPenunjang: $data['max_credit_penunjang'] ?? $data['max_penunjang'],
        );
    }

    /**
     * Convert to array (for API responses)
     */
    public function toArray(): array
    {
        return [
            'golongan' => $this->golongan,
            'jenjang' => $this->jenjang,
            'jabatan_terampil' => $this->jabatanTerampil,
            'jabatan_ahli' => $this->jabatanAhli,
            'min_credit' => $this->minCredit,
            'min_credit_utama' => $this->minCreditUtama,
            'max_credit_penunjang' => $this->maxCreditPenunjang,
        ];
    }

    /**
     * Check if both Terampil and Ahli positions are available
     */
    public function hasBothTingkat(): bool
    {
        return $this->jabatanTerampil !== null && $this->jabatanAhli !== null;
    }

    /**
     * Check if only Terampil position is available
     */
    public function isOnlyTerampil(): bool
    {
        return $this->jabatanTerampil !== null && $this->jabatanAhli === null;
    }

    /**
     * Check if only Ahli position is available
     */
    public function isOnlyAhli(): bool
    {
        return $this->jabatanAhli !== null && $this->jabatanTerampil === null;
    }
}
