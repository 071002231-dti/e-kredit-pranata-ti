<?php

namespace App\Http\Controllers\API\Internal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Activity;
use App\Models\CreditSchema;
use App\Services\ComplianceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityInternalController extends Controller
{
    public function __construct(
        private ComplianceService $complianceService
    ) {}

    /**
     * Get paginated activities for a user
     */
    public function index(Request $request, int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        $status = $request->query('status'); // pending, approved, rejected
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $query = Activity::with('creditSchema')
            ->where('user_id', $userId)
            ->orderBy('submitted_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $activities = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'activities' => $activities->items(),
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ]);
    }

    /**
     * Get user statistics for WhatsApp /stats command
     */
    public function stats(int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        $compliance = $this->complianceService->calculateUserCompliance($user);

        // Get activity counts
        $activityCounts = Activity::where('user_id', $userId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get this year stats
        $thisYear = date('Y');
        $thisYearCredits = Activity::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereYear('submitted_at', $thisYear)
            ->with('creditSchema')
            ->get()
            ->sum(fn($a) => $a->creditSchema?->credit_points ?? 0);

        return response()->json([
            'user' => [
                'name' => $user->name,
                'nip' => $user->nip,
                'jenjang_jabatan' => $user->jenjang_jabatan,
                'golongan' => $user->golongan,
            ],
            'credits' => [
                'total' => $user->current_credit_total ?? 0,
                'utama' => $user->current_credit_utama ?? 0,
                'penunjang' => $user->current_credit_penunjang ?? 0,
                'target' => $user->target_angka_kredit ?? 0,
                'this_year' => $thisYearCredits,
            ],
            'compliance' => [
                'is_compliant' => $compliance['is_compliant'],
                'percentage_utama' => $compliance['percentage_utama'],
                'percentage_penunjang' => $compliance['percentage_penunjang'],
                'message' => $compliance['message'],
            ],
            'activities' => [
                'pending' => $activityCounts['pending'] ?? 0,
                'approved' => $activityCounts['approved'] ?? 0,
                'rejected' => $activityCounts['rejected'] ?? 0,
                'total' => array_sum($activityCounts),
            ],
        ]);
    }

    /**
     * Create activity from WhatsApp submission
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schema_id' => 'required|exists:credit_schema,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'submitted_via' => 'nullable|string',
            'whatsapp_message_id' => 'nullable|string',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $schema = CreditSchema::findOrFail($validated['schema_id']);

        // Validate jenjang jabatan
        if (!$schema->canBePerformedBy($user->jenjang_jabatan)) {
            return response()->json([
                'error' => 'User tidak memiliki jenjang jabatan yang sesuai untuk aktivitas ini',
                'required_pelaksana' => $schema->pelaksana,
                'user_jenjang' => $user->jenjang_jabatan,
            ], 403);
        }

        // Check batasan penilaian
        $batasanNumeric = $schema->getBatasanNumericAttribute();
        if ($batasanNumeric && str_contains($schema->batasan_penilaian ?? '', '/tahun')) {
            $countThisYear = Activity::where('user_id', $user->id)
                ->where('schema_id', $schema->id)
                ->where('status', 'approved')
                ->whereYear('submitted_at', date('Y'))
                ->count();

            if ($countThisYear >= $batasanNumeric) {
                return response()->json([
                    'error' => 'Batasan penilaian tahunan telah tercapai',
                    'limit' => $batasanNumeric,
                    'current' => $countThisYear,
                ], 422);
            }
        }

        $activity = Activity::create([
            'user_id' => $user->id,
            'schema_id' => $validated['schema_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'pending',
            'submitted_via' => $validated['submitted_via'] ?? 'whatsapp',
            'whatsapp_message_id' => $validated['whatsapp_message_id'] ?? null,
        ]);

        $activity->load('creditSchema');

        return response()->json([
            'success' => true,
            'activity' => [
                'id' => $activity->id,
                'title' => $activity->title,
                'status' => $activity->status,
                'credit_points' => $activity->creditSchema->credit_points,
                'unsur_type' => $activity->creditSchema->unsur_type,
            ],
        ], 201);
    }
}
