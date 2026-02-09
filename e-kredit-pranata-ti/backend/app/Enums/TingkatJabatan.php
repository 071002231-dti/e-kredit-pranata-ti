<?php

namespace App\Enums;

/**
 * TingkatJabatan Enum
 * 
 * Represents the level of functional position (Tingkat Jabatan Fungsional).
 * Based on PR No. 3 Tahun 2025.
 */
enum TingkatJabatan: string
{
    case TERAMPIL = 'terampil';
    case AHLI = 'ahli';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::TERAMPIL => 'Tingkat Terampil',
            self::AHLI => 'Tingkat Ahli',
        };
    }

    /**
     * Get golongan range for this tingkat
     */
    public function golonganRange(): array
    {
        return match($this) {
            self::TERAMPIL => ['II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d'],
            self::AHLI => ['III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'],
        };
    }

    /**
     * Get description
     */
    public function description(): string
    {
        return match($this) {
            self::TERAMPIL => 'Jabatan Fungsional Tingkat Terampil (II/a - III/d)',
            self::AHLI => 'Jabatan Fungsional Tingkat Ahli (III/a - IV/e)',
        };
    }
}
