<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Approval;
use App\Events\ActivityStatusChanged;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * Get list of all activities for approval (for verifier/admin)
     */
    public function index(Request $request)
    {
        // Check if user is verifier or admin
        if (!in_array($request->user()->role, ['verifier', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Only verifier or admin can access this.',
            ], 403);
        }

        $query = Activity::with(['user', 'creditSchema']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $activities = $query->orderBy('created_at', 'desc')->get();

        // Transform to match frontend expected format
        $transformed = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'title' => $activity->title,
                'description' => $activity->description,
                'status' => $activity->status,
                'credit_points' => $activity->creditSchema->credit_points ?? 0,
                'user' => $activity->user ? [
                    'id' => $activity->user->id,
                    'name' => $activity->user->name,
                    'email' => $activity->user->email,
                    'jenjang_jabatan' => $activity->user->jenjang_jabatan,
                ] : null,
                'schema' => $activity->creditSchema ? [
                    'id' => $activity->creditSchema->id,
                    'category' => $activity->creditSchema->category,
                    'subcategory' => $activity->creditSchema->subcategory,
                    'activity_name' => $activity->creditSchema->activity_name,
                    'credit_points' => $activity->creditSchema->credit_points,
                    'unsur_type' => $activity->creditSchema->unsur_type,
                ] : null,
                'created_at' => $activity->created_at,
                'proof_file_url' => $activity->proof_file ? asset('storage/' . $activity->proof_file) : null,
            ];
        });

        return response()->json($transformed);
    }

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

        $oldStatus = $activity->status;

        // Update activity status
        $activity->update(['status' => 'approved']);

        // Create approval record
        $approval = Approval::create([
            'activity_id' => $activity->id,
            'verifier_id' => $request->user()->id,
            'status' => 'approved',
            'comments' => $validated['comments'] ?? null,
        ]);

        // Dispatch event for WhatsApp notification
        ActivityStatusChanged::dispatch($activity->fresh(), $oldStatus, 'approved');

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

        $oldStatus = $activity->status;

        // Update activity status
        $activity->update(['status' => 'rejected']);

        // Create approval record
        $approval = Approval::create([
            'activity_id' => $activity->id,
            'verifier_id' => $request->user()->id,
            'status' => 'rejected',
            'comments' => $validated['comments'],
        ]);

        // Dispatch event for WhatsApp notification
        ActivityStatusChanged::dispatch($activity->fresh(), $oldStatus, 'rejected');

        return response()->json([
            'message' => 'Activity rejected',
            'activity' => $activity->load('creditSchema', 'user'),
            'approval' => $approval->load('verifier'),
        ]);
    }

    /**
     * Request revision for an activity
     */
    public function revision(Request $request, $id)
    {
        // Check if user is verifier or admin
        if (!in_array($request->user()->role, ['verifier', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Only verifier or admin can request revision.',
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

        $oldStatus = $activity->status;

        // Update activity status to revision
        $activity->update(['status' => 'revision']);

        // Create approval record with revision status
        $approval = Approval::create([
            'activity_id' => $activity->id,
            'verifier_id' => $request->user()->id,
            'status' => 'revision',
            'comments' => $validated['comments'],
        ]);

        // Dispatch event for WhatsApp notification
        ActivityStatusChanged::dispatch($activity->fresh(), $oldStatus, 'revision');

        return response()->json([
            'message' => 'Revision requested for activity',
            'activity' => $activity->load('creditSchema', 'user'),
            'approval' => $approval->load('verifier'),
        ]);
    }
}
