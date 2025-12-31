<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppUser extends Model
{
    protected $fillable = [
        'main_app_user_id',
        'whatsapp_phone',
        'verified',
        'notifications_enabled',
        'last_interaction_at',
        'preferences',
        'cached_name',
        'cached_email',
        'cached_nip',
        'cached_jenjang_jabatan',
        'cache_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
            'notifications_enabled' => 'boolean',
            'last_interaction_at' => 'datetime',
            'preferences' => 'array',
            'cache_updated_at' => 'datetime',
        ];
    }

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function sessions()
    {
        return $this->hasMany(WhatsAppSession::class);
    }
}
