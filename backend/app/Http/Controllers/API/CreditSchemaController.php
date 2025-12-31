<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CreditSchema;
use Illuminate\Http\Request;

class CreditSchemaController extends Controller
{
    /**
     * Display a listing of credit schemas
     * Filtered by user's jenjang jabatan for compliance
     */
    public function index(Request $request)
    {
        $query = CreditSchema::query();

        // Filter by user's jenjang jabatan (compliance requirement)
        // Only show schemas that user is eligible to perform
        $user = auth()->user();
        if ($user && $user->jenjang_jabatan) {
            $query->where(function ($q) use ($user) {
                $q->where('pelaksana', 'Semua Jenjang')
                  ->orWhere('pelaksana', $user->jenjang_jabatan)
                  ->orWhereNull('pelaksana');
            });
        }

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by subcategory if provided
        if ($request->has('subcategory')) {
            $query->where('subcategory', $request->subcategory);
        }

        // Filter by unsur_type if provided
        if ($request->has('unsur_type')) {
            $query->where('unsur_type', $request->unsur_type);
        }

        // Get all or paginated
        if ($request->has('paginate') && $request->paginate == 'false') {
            $schemas = $query->orderBy('category')->orderBy('subcategory')->get();
        } else {
            $schemas = $query->orderBy('category')->orderBy('subcategory')->paginate(20);
        }

        return response()->json($schemas);
    }

    /**
     * Display the specified credit schema
     */
    public function show($id)
    {
        $schema = CreditSchema::findOrFail($id);
        return response()->json($schema);
    }

    /**
     * Get unique categories
     */
    public function categories()
    {
        $categories = CreditSchema::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json($categories);
    }
}
