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
     * Alias for creditSchema (for backward compatibility)
     */
    public function schema()
    {
        return $this->creditSchema();
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

    /**
     * Boot the model and add event listeners for credit banking
     */
    protected static function booted()
    {
        static::updated(function ($activity) {
            // Check if status changed to 'approved' or 'rejected'
            if ($activity->wasChanged('status')) {
                $bankingService = app(\App\Services\CreditBankingService::class);

                if ($activity->status === 'approved') {
                    // Load the credit schema and user
                    $activity->load('creditSchema', 'user');

                    if (!$activity->creditSchema) {
                        \Log::warning("Activity {$activity->id} approved but has no credit schema");
                        return;
                    }

                    // Check if credits should be banked
                    $shouldBank = $bankingService->shouldBankCredits(
                        $activity->user,
                        $activity
                    );

                    if ($shouldBank['should_bank']) {
                        // Bank the credits
                        $bankingService->bankCredits(
                            $activity->user,
                            $activity,
                            $shouldBank['reason']
                        );

                        \Log::info("Credits banked for activity {$activity->id}: {$activity->creditSchema->credit_points} points. Reason: {$shouldBank['reason']}");
                    } else {
                        // Credits approved and usable directly
                        \Log::info("Credits approved for activity {$activity->id}: {$activity->creditSchema->credit_points} points (usable)");
                    }

                    // Update user's current credits (both usable and banked)
                    $bankingService->updateUserCurrentCredits($activity->user);
                }

                // Recalculate user credits on any status change
                if ($activity->status === 'rejected') {
                    $bankingService->updateUserCurrentCredits($activity->user);
                }
            }
        });
    }
}
