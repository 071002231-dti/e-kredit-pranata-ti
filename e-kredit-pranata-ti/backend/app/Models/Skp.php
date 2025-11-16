<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skp extends Model
{
    protected $table = 'skp';

    protected $fillable = [
        'user_id',
        'tahun',
        'target_angka_kredit',
        'tugas_tambahan',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'target_angka_kredit' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the SKP
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the approver (user who approved this SKP)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the SKP items
     */
    public function items(): HasMany
    {
        return $this->hasMany(SkpItem::class);
    }

    /**
     * Calculate total target points from all items
     */
    public function getTotalTargetPointsAttribute(): float
    {
        return $this->items->sum('target_points');
    }

    /**
     * Calculate total realisasi points from all items
     */
    public function getTotalRealisasiPointsAttribute(): float
    {
        return $this->items->sum('realisasi_points');
    }

    /**
     * Calculate achievement percentage
     */
    public function getAchievementPercentageAttribute(): float
    {
        $target = $this->getTotalTargetPointsAttribute();
        if ($target == 0) return 0;

        $realisasi = $this->getTotalRealisasiPointsAttribute();
        return ($realisasi / $target) * 100;
    }
}
