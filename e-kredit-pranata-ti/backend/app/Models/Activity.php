<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'schema_id',
        'title',
        'description',
        'proof_file',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Get the user who submitted this activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the credit schema for this activity
     */
    public function creditSchema()
    {
        return $this->belongsTo(CreditSchema::class, 'schema_id');
    }

    /**
     * Get approvals for this activity
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    /**
     * Get the latest approval for this activity
     */
    public function latestApproval()
    {
        return $this->hasOne(Approval::class)->latestOfMany();
    }
}
