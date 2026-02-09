<?php

namespace App\Enums;

/**
 * RecommendationType Enum
 * 
 * Represents the type of recommendation given to users.
 */
enum RecommendationType: string
{
    case WARNING = 'warning';
    case INFO = 'info';
    case SUCCESS = 'success';
    case ERROR = 'error';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::WARNING => 'Peringatan',
            self::INFO => 'Informasi',
            self::SUCCESS => 'Sukses',
            self::ERROR => 'Error',
        };
    }

    /**
     * Get color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::WARNING => 'yellow',
            self::INFO => 'blue',
            self::SUCCESS => 'green',
            self::ERROR => 'red',
        };
    }

    /**
     * Get icon for UI display
     */
    public function icon(): string
    {
        return match($this) {
            self::WARNING => 'exclamation-triangle',
            self::INFO => 'information-circle',
            self::SUCCESS => 'check-circle',
            self::ERROR => 'x-circle',
        };
    }
}
