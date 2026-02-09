<?php

namespace App\DTOs;

use App\Enums\RecommendationType;

/**
 * RecommendationDTO
 * 
 * Data Transfer Object for user recommendations.
 * Replaces array items from ComplianceService::getRecommendations()
 */
readonly class RecommendationDTO
{
    public function __construct(
        public RecommendationType $type,
        public string $category,
        public string $message,
        public ?string $action = null,
        public ?array $activities = null,
    ) {}

    /**
     * Create from array (for backward compatibility)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: RecommendationType::from($data['type']),
            category: $data['category'],
            message: $data['message'],
            action: $data['action'] ?? null,
            activities: $data['activities'] ?? null,
        );
    }

    /**
     * Convert to array (for API responses)
     */
    public function toArray(): array
    {
        $result = [
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'type_color' => $this->type->color(),
            'type_icon' => $this->type->icon(),
            'category' => $this->category,
            'message' => $this->message,
        ];

        if ($this->action !== null) {
            $result['action'] = $this->action;
        }

        if ($this->activities !== null) {
            $result['activities'] = $this->activities;
        }

        return $result;
    }

    /**
     * Check if this is a warning recommendation
     */
    public function isWarning(): bool
    {
        return $this->type === RecommendationType::WARNING;
    }

    /**
     * Check if this is a critical recommendation
     */
    public function isCritical(): bool
    {
        return $this->type === RecommendationType::ERROR;
    }
}
