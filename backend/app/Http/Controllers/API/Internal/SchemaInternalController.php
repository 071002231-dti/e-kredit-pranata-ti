<?php

namespace App\Http\Controllers\API\Internal;

use App\Http\Controllers\Controller;
use App\Models\CreditSchema;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SchemaInternalController extends Controller
{
    /**
     * Get all credit schemas with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $jenjang = $request->query('jenjang'); // Filter by jenjang jabatan
        $unsurType = $request->query('unsur_type'); // 'utama' or 'penunjang'
        $category = $request->query('category');

        $query = CreditSchema::query();

        if ($unsurType) {
            $query->where('unsur_type', $unsurType);
        }

        if ($category) {
            $query->where('category', $category);
        }

        // If jenjang specified, filter schemas that this jenjang can perform
        if ($jenjang) {
            $query->where(function ($q) use ($jenjang) {
                $q->where('pelaksana', 'like', '%' . $jenjang . '%')
                  ->orWhere('pelaksana', 'Semua Jenjang');
            });
        }

        $schemas = $query->orderBy('category')
            ->orderBy('sub_category')
            ->get([
                'id',
                'butir_kegiatan',
                'activity_name',
                'category',
                'sub_category',
                'unsur_type',
                'credit_points',
                'pelaksana',
                'batasan_penilaian',
                'bukti_fisik',
            ]);

        return response()->json([
            'schemas' => $schemas,
            'count' => $schemas->count(),
        ]);
    }

    /**
     * Get categories list
     */
    public function categories(): JsonResponse
    {
        $categories = CreditSchema::select('category', 'unsur_type')
            ->distinct()
            ->orderBy('unsur_type')
            ->orderBy('category')
            ->get()
            ->groupBy('unsur_type')
            ->map(fn($items) => $items->pluck('category')->unique()->values());

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Get single schema by ID
     */
    public function show(int $id): JsonResponse
    {
        $schema = CreditSchema::find($id);

        if (!$schema) {
            return response()->json([
                'error' => 'Schema not found',
            ], 404);
        }

        return response()->json([
            'schema' => $schema,
        ]);
    }
}
