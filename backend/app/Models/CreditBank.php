<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'credit_schema_id',
        'credit_points',
        'unsur_type',
        'activity_category',
        'status',
        'required_jenjang',
        'required_golongan',
        'current_jenjang_when_banked',
        'banked_at',
        'unlocked_at',
        'used_at',
        'reason',
        'unlock_notes',
    ];

    protected $casts = [
        'credit_points' => 'decimal:2',
        'banked_at' => 'datetime',
        'unlocked_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function creditSchema(): BelongsTo
    {
        return $this->belongsTo(CreditSchema::class);
    }

    /**
     * Scopes
     */
    public function scopeBanked($query)
    {
        return $query->where('status', 'banked');
    }

    public function scopeUnlocked($query)
    {
        return $query->where('status', 'unlocked');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if credit can be unlocked for given jenjang
     */
    public function canUnlockFor(string $jenjang, ?string $golongan = null): bool
    {
        if ($this->status !== 'banked') {
            return false;
        }

        // Check jenjang requirement
        $jenjangOrder = [
            'PK Pelaksana Pemula',
            'PK Pelaksana',
            'PK Pelaksana Lanjutan',
            'PK Penyelia',
            'PK Pertama',
            'PK Muda',
            'PK Madya',
            'Pranata Komputer Utama',
        ];

        $currentIndex = array_search($jenjang, $jenjangOrder);
        $requiredIndex = array_search($this->required_jenjang, $jenjangOrder);

        return $currentIndex !== false && $requiredIndex !== false && $currentIndex >= $requiredIndex;
    }

    /**
     * Unlock this banked credit
     */
    public function unlock(?string $notes = null): bool
    {
        if ($this->status !== 'banked') {
            return false;
        }

        $this->status = 'unlocked';
        $this->unlocked_at = now();
        $this->unlock_notes = $notes;

        return $this->save();
    }

    /**
     * Mark credit as used
     */
    public function markAsUsed(): bool
    {
        if ($this->status !== 'unlocked') {
            return false;
        }

        $this->status = 'used';
        $this->used_at = now();

        return $this->save();
    }
}
