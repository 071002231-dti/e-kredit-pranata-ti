<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    protected $fillable = [
        'whatsapp_message_id',
        'whatsapp_user_id',
        'from_number',
        'to_number',
        'direction',
        'type',
        'content',
        'metadata',
        'status',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(WhatsAppUser::class, 'whatsapp_user_id');
    }

    public function scopeInbound($query)
    {
        return $query->where('direction', 'inbound');
    }

    public function scopeOutbound($query)
    {
        return $query->where('direction', 'outbound');
    }
}
