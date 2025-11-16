<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\CreditSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get user statistics
     */
    public function stats(Request $request)
    {
        $userId = $request->user()->id;
        $user = $request->user();

        // Calculate total points and unsur distribution
        $totalPoints = Activity::where('user_id', $userId)
            ->where('status', 'approved')
            ->join('credit_schema', 'activities.schema_id', '=', 'credit_schema.id')
            ->sum('credit_schema.credit_points');

        $utamaPoints = Activity::where('user_id', $userId)
            ->where('status', 'approved')
            ->join('credit_schema', 'activities.schema_id', '=', 'credit_schema.id')
            ->where('credit_schema.unsur_type', 'utama')
            ->sum('credit_schema.credit_points');

        $penunjangPoints = Activity::where('user_id', $userId)
            ->where('status', 'approved')
            ->join('credit_schema', 'activities.schema_id', '=', 'credit_schema.id')
            ->where('credit_schema.unsur_type', 'penunjang')
            ->sum('credit_schema.credit_points');

        // Calculate percentages
        $utamaPercentage = $totalPoints > 0 ? ($utamaPoints / $totalPoints) * 100 : 0;
        $penunjangPercentage = $totalPoints > 0 ? ($penunjangPoints / $totalPoints) * 100 : 0;

        // Check compliance with Pasal 3 (Utama ≥ 80%, Penunjang ≤ 20%)
        $isCompliant = $utamaPercentage >= 80 && $penunjangPercentage <= 20;

        $stats = [
            'total_activities' => Activity::where('user_id', $userId)->count(),
            'pending' => Activity::where('user_id', $userId)->where('status', 'pending')->count(),
            'approved' => Activity::where('user_id', $userId)->where('status', 'approved')->count(),
            'rejected' => Activity::where('user_id', $userId)->where('status', 'rejected')->count(),
            'total_points' => $totalPoints,
            'utama_points' => $utamaPoints,
            'penunjang_points' => $penunjangPoints,
            'utama_percentage' => round($utamaPercentage, 2),
            'penunjang_percentage' => round($penunjangPercentage, 2),
            'is_compliant' => $isCompliant,
            'target_angka_kredit' => $user->target_angka_kredit,
            'angka_kredit_minimal' => $user->angka_kredit_minimal,
            'progress_percentage' => $user->target_angka_kredit > 0
                ? round(($totalPoints / $user->target_angka_kredit) * 100, 2)
                : 0,
        ];

        return response()->json($stats);
    }

    /**
     * Get summary by category
     */
    public function summary(Request $request)
    {
        $userId = $request->user()->id;

        $summary = Activity::where('activities.user_id', $userId)
            ->join('credit_schema', 'activities.schema_id', '=', 'credit_schema.id')
            ->select(
                'credit_schema.category',
                DB::raw('COUNT(*) as total_activities'),
                DB::raw('SUM(CASE WHEN activities.status = "approved" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN activities.status = "approved" THEN credit_schema.credit_points ELSE 0 END) as earned_points')
            )
            ->groupBy('credit_schema.category')
            ->get();

        return response()->json($summary);
    }

    /**
     * Validate if adding a new activity would maintain compliance
     * with Pasal 3 (Utama ≥ 80%, Penunjang ≤ 20%)
     */
    public function validateCompliance(Request $request)
    {
        $request->validate([
            'schema_id' => 'required|exists:credit_schema,id',
        ]);

        $userId = $request->user()->id;
        $schemaId = $request->schema_id;

        // Get the schema to be added
        $newSchema = CreditSchema::findOrFail($schemaId);

        // Calculate current totals
        $currentTotal = Activity::where('user_id', $userId)
            ->where('status', 'approved')
            ->join('credit_schema', 'activities.schema_id', '=', 'credit_schema.id')
            ->sum('credit_schema.credit_points');

        $currentUtama = Activity::where('user_id', $userId)
            ->where('status', 'approved')
            ->join('credit_schema', 'activities.schema_id', '=', 'credit_schema.id')
            ->where('credit_schema.unsur_type', 'utama')
            ->sum('credit_schema.credit_points');

        $currentPenunjang = Activity::where('user_id', $userId)
            ->where('status', 'approved')
            ->join('credit_schema', 'activities.schema_id', '=', 'credit_schema.id')
            ->where('credit_schema.unsur_type', 'penunjang')
            ->sum('credit_schema.credit_points');

        // Calculate new totals if this activity is approved
        $newTotal = $currentTotal + $newSchema->credit_points;
        $newUtama = $newSchema->unsur_type === 'utama'
            ? $currentUtama + $newSchema->credit_points
            : $currentUtama;
        $newPenunjang = $newSchema->unsur_type === 'penunjang'
            ? $currentPenunjang + $newSchema->credit_points
            : $currentPenunjang;

        // Calculate new percentages
        $newUtamaPercentage = $newTotal > 0 ? ($newUtama / $newTotal) * 100 : 0;
        $newPenunjangPercentage = $newTotal > 0 ? ($newPenunjang / $newTotal) * 100 : 0;

        // Check if still compliant
        $wouldBeCompliant = $newUtamaPercentage >= 80 && $newPenunjangPercentage <= 20;

        return response()->json([
            'current_total' => $currentTotal,
            'new_total' => $newTotal,
            'current_utama_percentage' => round(($currentUtama / max($currentTotal, 1)) * 100, 2),
            'new_utama_percentage' => round($newUtamaPercentage, 2),
            'current_penunjang_percentage' => round(($currentPenunjang / max($currentTotal, 1)) * 100, 2),
            'new_penunjang_percentage' => round($newPenunjangPercentage, 2),
            'would_be_compliant' => $wouldBeCompliant,
            'warning' => !$wouldBeCompliant
                ? 'Menambahkan kegiatan ini akan membuat distribusi Unsur Utama/Penunjang tidak sesuai dengan Pasal 3 (Utama ≥80%, Penunjang ≤20%)'
                : null,
        ]);
    }
}
