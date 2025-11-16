<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'activity_id',
        'verifier_id',
        'status',
        'comments',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the activity being approved
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the verifier who approved/rejected
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }
}
