<?php

namespace App\Enums;

/**
 * ActivityStatus Enum
 * 
 * Represents the status of an activity submission in the credit system.
 * Based on the approval workflow in the e-Kredit Pranata TI system.
 */
enum ActivityStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case REVISION_REQUESTED = 'revision_requested';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Diajukan',
            self::UNDER_REVIEW => 'Sedang Ditinjau',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::REVISION_REQUESTED => 'Perlu Revisi',
        };
    }

    /**
     * Get color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'blue',
            self::UNDER_REVIEW => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::REVISION_REQUESTED => 'orange',
        };
    }

    /**
     * Check if status is final (cannot be changed)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED]);
    }

    /**
     * Check if status allows editing
     */
    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::REVISION_REQUESTED]);
    }

    /**
     * Get all statuses that count toward credit
     */
    public static function creditCounting(): array
    {
        return [self::APPROVED];
    }
}
