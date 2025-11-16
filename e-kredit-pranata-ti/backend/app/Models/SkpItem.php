<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkpItem extends Model
{
    protected $fillable = [
        'skp_id',
        'schema_id',
        'target_quantity',
        'target_points',
        'realisasi_quantity',
        'realisasi_points',
        'notes',
    ];

    protected $casts = [
        'target_quantity' => 'integer',
        'target_points' => 'decimal:3',
        'realisasi_quantity' => 'integer',
        'realisasi_points' => 'decimal:3',
    ];

    /**
     * Get the SKP that owns this item
     */
    public function skp(): BelongsTo
    {
        return $this->belongsTo(Skp::class);
    }

    /**
     * Get the credit schema for this item
     */
    public function creditSchema(): BelongsTo
    {
        return $this->belongsTo(CreditSchema::class, 'schema_id');
    }

    /**
     * Calculate achievement percentage for this item
     */
    public function getAchievementPercentageAttribute(): float
    {
        if ($this->target_points == 0) return 0;
        return ($this->realisasi_points / $this->target_points) * 100;
    }
}
