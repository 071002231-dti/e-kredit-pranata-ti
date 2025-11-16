<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    /**
     * Display a listing of the user's activities
     */
    public function index(Request $request)
    {
        $activities = Activity::with(['creditSchema', 'latestApproval.verifier'])
            ->where('user_id', $request->user()->id)
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        return response()->json($activities);
    }

    /**
     * Store a newly created activity
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'schema_id' => 'required|exists:credit_schema,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
        ]);

        $activity = new Activity([
            'user_id' => $request->user()->id,
            'schema_id' => $validated['schema_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        // Handle file upload
        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('proofs', 'public');
            $activity->proof_file = $path;
        }

        $activity->save();

        return response()->json([
            'message' => 'Activity submitted successfully',
            'activity' => $activity->load('creditSchema'),
        ], 201);
    }

    /**
     * Display the specified activity
     */
    public function show(Request $request, $id)
    {
        $activity = Activity::with(['creditSchema', 'approvals.verifier'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($activity);
    }

    /**
     * Update the specified activity (only if pending)
     */
    public function update(Request $request, $id)
    {
        $activity = Activity::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($activity->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot update activity that is not pending',
            ], 403);
        }

        $validated = $request->validate([
            'schema_id' => 'sometimes|exists:credit_schema,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Handle file upload
        if ($request->hasFile('proof_file')) {
            // Delete old file if exists
            if ($activity->proof_file) {
                Storage::disk('public')->delete($activity->proof_file);
            }

            $path = $request->file('proof_file')->store('proofs', 'public');
            $validated['proof_file'] = $path;
        }

        $activity->update($validated);

        return response()->json([
            'message' => 'Activity updated successfully',
            'activity' => $activity->load('creditSchema'),
        ]);
    }

    /**
     * Remove the specified activity (only if pending)
     */
    public function destroy(Request $request, $id)
    {
        $activity = Activity::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($activity->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot delete activity that is not pending',
            ], 403);
        }

        // Delete file if exists
        if ($activity->proof_file) {
            Storage::disk('public')->delete($activity->proof_file);
        }

        $activity->delete();

        return response()->json([
            'message' => 'Activity deleted successfully',
        ]);
    }
}
