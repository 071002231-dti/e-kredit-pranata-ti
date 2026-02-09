<?php

namespace App\Enums;

/**
 * UnsurType Enum
 * 
 * Represents the type of credit element (Unsur) according to PR No. 3 Tahun 2025.
 * - Unsur Utama: Main activities (minimum 80% of total credits)
 * - Unsur Penunjang: Supporting activities (maximum 20% of total credits)
 */
enum UnsurType: string
{
    case UTAMA = 'utama';
    case PENUNJANG = 'penunjang';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::UTAMA => 'Unsur Utama',
            self::PENUNJANG => 'Unsur Penunjang',
        };
    }

    /**
     * Get minimum percentage required (for Utama)
     */
    public function minPercentage(): ?float
    {
        return match($this) {
            self::UTAMA => 80.0,
            self::PENUNJANG => null,
        };
    }

    /**
     * Get maximum percentage allowed (for Penunjang)
     */
    public function maxPercentage(): ?float
    {
        return match($this) {
            self::UTAMA => null,
            self::PENUNJANG => 20.0,
        };
    }

    /**
     * Get color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::UTAMA => 'blue',
            self::PENUNJANG => 'green',
        };
    }
}
