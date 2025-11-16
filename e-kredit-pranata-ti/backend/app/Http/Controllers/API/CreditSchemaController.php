<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CreditSchema;
use Illuminate\Http\Request;

class CreditSchemaController extends Controller
{
    /**
     * Display a listing of credit schemas
     */
    public function index(Request $request)
    {
        $query = CreditSchema::query();

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by subcategory if provided
        if ($request->has('subcategory')) {
            $query->where('subcategory', $request->subcategory);
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
