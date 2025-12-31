<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppSession extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_sessions';

    protected $fillable = [
        'whatsapp_phone',
        'user_id',
        'state',
        'context',
        'last_message_at',
        'expires_at',
    ];

    protected $casts = [
        'context' => 'array',
        'last_message_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user associated with this session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active sessions (not expired)
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now())
                    ->orWhereNull('expires_at');
    }

    /**
     * Scope for expired sessions
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Update session state
     */
    public function updateState(string $state, array $context = []): void
    {
        $this->update([
            'state' => $state,
            'context' => array_merge($this->context ?? [], $context),
            'last_message_at' => now(),
        ]);
    }

    /**
     * Extend session expiration
     */
    public function extendExpiration(int $minutes = 30): void
    {
        $this->update([
            'expires_at' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Clear session
     */
    public function clear(): void
    {
        $this->update([
            'state' => 'idle',
            'context' => null,
        ]);
    }
}
