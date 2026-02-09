<?php

namespace App\Enums;

/**
 * CreditBankStatus Enum
 * 
 * Represents the status of banked credits in the credit banking system.
 */
enum CreditBankStatus: string
{
    case BANKED = 'banked';
    case UNLOCKED = 'unlocked';
    case EXPIRED = 'expired';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::BANKED => 'Tersimpan',
            self::UNLOCKED => 'Terbuka',
            self::EXPIRED => 'Kadaluarsa',
        };
    }

    /**
     * Get color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::BANKED => 'yellow',
            self::UNLOCKED => 'green',
            self::EXPIRED => 'red',
        };
    }

    /**
     * Check if credits count toward total
     */
    public function countsTowardTotal(): bool
    {
        return $this === self::UNLOCKED;
    }
}
