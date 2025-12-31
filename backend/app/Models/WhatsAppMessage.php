<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'whatsapp_message_id',
        'user_id',
        'from_number',
        'to_number',
        'direction',
        'type',
        'content',
        'metadata',
        'status',
        'error_message',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user associated with this message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for inbound messages
     */
    public function scopeInbound($query)
    {
        return $query->where('direction', 'inbound');
    }

    /**
     * Scope for outbound messages
     */
    public function scopeOutbound($query)
    {
        return $query->where('direction', 'outbound');
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific phone number
     */
    public function scopeForPhone($query, string $phone)
    {
        return $query->where(function ($q) use ($phone) {
            $q->where('from_number', $phone)
              ->orWhere('to_number', $phone);
        });
    }
}
