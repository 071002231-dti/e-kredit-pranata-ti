<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Approval;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * Get list of pending activities (for verifier/admin)
     */
    public function pending(Request $request)
    {
        // Check if user is verifier or admin
        if (!in_array($request->user()->role, ['verifier', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Only verifier or admin can access this.',
            ], 403);
        }

        $activities = Activity::with(['user', 'creditSchema'])
            ->where('status', 'pending')
            ->orderBy('submitted_at', 'asc')
            ->paginate(10);

        return response()->json($activities);
    }

    /**
     * Approve an activity
     */
    public function approve(Request $request, $id)
    {
        // Check if user is verifier or admin
        if (!in_array($request->user()->role, ['verifier', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Only verifier or admin can approve.',
            ], 403);
        }

        $validated = $request->validate([
            'comments' => 'nullable|string',
        ]);

        $activity = Activity::findOrFail($id);

        if ($activity->status !== 'pending') {
            return response()->json([
                'message' => 'Activity is not pending',
            ], 400);
        }

        // Update activity status
        $activity->update(['status' => 'approved']);

        // Create approval record
        $approval = Approval::create([
            'activity_id' => $activity->id,
            'verifier_id' => $request->user()->id,
            'status' => 'approved',
            'comments' => $validated['comments'] ?? null,
        ]);

        return response()->json([
            'message' => 'Activity approved successfully',
            'activity' => $activity->load('creditSchema', 'user'),
            'approval' => $approval->load('verifier'),
        ]);
    }

    /**
     * Reject an activity
     */
    public function reject(Request $request, $id)
    {
        // Check if user is verifier or admin
        if (!in_array($request->user()->role, ['verifier', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Only verifier or admin can reject.',
            ], 403);
        }

        $validated = $request->validate([
            'comments' => 'required|string',
        ]);

        $activity = Activity::findOrFail($id);

        if ($activity->status !== 'pending') {
            return response()->json([
                'message' => 'Activity is not pending',
            ], 400);
        }

        // Update activity status
        $activity->update(['status' => 'rejected']);

        // Create approval record
        $approval = Approval::create([
            'activity_id' => $activity->id,
            'verifier_id' => $request->user()->id,
            'status' => 'rejected',
            'comments' => $validated['comments'],
        ]);

        return response()->json([
            'message' => 'Activity rejected',
            'activity' => $activity->load('creditSchema', 'user'),
            'approval' => $approval->load('verifier'),
        ]);
    }
}
