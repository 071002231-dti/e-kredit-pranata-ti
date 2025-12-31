<?php

namespace App\Events;

use App\Models\Activity;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Activity $activity;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Activity $activity, string $oldStatus, string $newStatus)
    {
        $this->activity = $activity;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
