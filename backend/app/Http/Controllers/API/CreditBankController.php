<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CreditBank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CreditBankController extends Controller
{
    /**
     * Display a listing of the user's banked credits
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = CreditBank::with(['activity.creditSchema', 'user'])
            ->where('user_id', $user->id);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        // Order by newest first
        $bankedCredits = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($bankedCredits);
    }

    /**
     * Get summary of banked credits
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        $bankingService = app(\App\Services\CreditBankingService::class);

        $summary = $bankingService->getBankedCreditsSummary($user);

        return response()->json($summary);
    }

    /**
     * Display the specified banked credit
     */
    public function show(Request $request, $id)
    {
        $bankedCredit = CreditBank::with(['activity.creditSchema', 'user'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($bankedCredit);
    }

    /**
     * Manually unlock banked credits (Admin only)
     * This is for special cases or corrections
     */
    public function unlock(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized. Only admin can manually unlock credits.',
            ], 403);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $bankedCredit = CreditBank::findOrFail($id);

        // Check if already unlocked
        if ($bankedCredit->status === 'unlocked') {
            return response()->json([
                'message' => 'This credit is already unlocked.',
            ], 400);
        }

        // Update status
        $bankedCredit->status = 'unlocked';
        $bankedCredit->unlocked_at = now();
        $bankedCredit->unlock_reason = 'Manual unlock by admin: ' . $request->reason;
        $bankedCredit->save();

        // Recalculate user credits
        $bankingService = app(\App\Services\CreditBankingService::class);
        $bankingService->updateUserCurrentCredits($bankedCredit->user);

        \Log::info("Admin {$request->user()->id} manually unlocked credit bank {$id} for user {$bankedCredit->user_id}. Reason: {$request->reason}");

        return response()->json([
            'message' => 'Credit unlocked successfully',
            'banked_credit' => $bankedCredit->load('activity.creditSchema', 'user'),
        ]);
    }

    /**
     * Get statistics about banked credits
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_banked' => CreditBank::where('user_id', $user->id)
                ->where('status', 'banked')
                ->sum('credit_amount'),
            'total_unlocked' => CreditBank::where('user_id', $user->id)
                ->where('status', 'unlocked')
                ->sum('credit_amount'),
            'count_banked' => CreditBank::where('user_id', $user->id)
                ->where('status', 'banked')
                ->count(),
            'count_unlocked' => CreditBank::where('user_id', $user->id)
                ->where('status', 'unlocked')
                ->count(),
            'latest_banked' => CreditBank::with(['activity.creditSchema'])
                ->where('user_id', $user->id)
                ->where('status', 'banked')
                ->orderBy('created_at', 'desc')
                ->first(),
            'reasons_breakdown' => CreditBank::where('user_id', $user->id)
                ->where('status', 'banked')
                ->selectRaw('reason, COUNT(*) as count, SUM(credit_amount) as total_credits')
                ->groupBy('reason')
                ->get(),
        ];

        return response()->json($stats);
    }
}
