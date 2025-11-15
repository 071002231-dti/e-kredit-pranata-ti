<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppUser extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_users';

    protected $fillable = [
        'user_id',
        'whatsapp_phone',
        'verified',
        'notifications_enabled',
        'last_interaction_at',
        'preferences',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'notifications_enabled' => 'boolean',
        'last_interaction_at' => 'datetime',
        'preferences' => 'array',
    ];

    /**
     * Get the user that owns this WhatsApp account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for verified users
     */
    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    /**
     * Scope for users with notifications enabled
     */
    public function scopeNotificationsEnabled($query)
    {
        return $query->where('notifications_enabled', true);
    }

    /**
     * Update last interaction timestamp
     */
    public function updateLastInteraction(): void
    {
        $this->update(['last_interaction_at' => now()]);
    }
}
