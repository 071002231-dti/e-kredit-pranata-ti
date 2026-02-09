<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CreditSchema;
use Illuminate\Http\Request;

class AdminSchemaController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    /**
     * Display a listing of schemas
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        $query = CreditSchema::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('activity_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('subcategory', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by unsur_type
        if ($request->has('unsur_type')) {
            $query->where('unsur_type', $request->unsur_type);
        }

        $schemas = $query->orderBy('category')->orderBy('subcategory')->orderBy('activity_name')->get();

        return response()->json($schemas);
    }

    /**
     * Store a newly created schema
     */
    public function store(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'subcategory' => 'required|string|max:255',
            'activity_name' => 'required|string|max:500',
            'credit_points' => 'required|numeric|min:0',
            'satuan_hasil' => 'required|string|max:100',
            'batasan_penilaian' => 'nullable|string|max:255',
            'pelaksana' => 'nullable|string|max:100',
            'bukti_fisik' => 'nullable|string|max:255',
            'unsur_type' => 'required|in:utama,penunjang',
            'description' => 'nullable|string',
        ]);

        $schema = CreditSchema::create($validated);

        return response()->json([
            'message' => 'Schema created successfully',
            'schema' => $schema
        ], 201);
    }

    /**
     * Display the specified schema
     */
    public function show($id)
    {
        $this->checkAdmin();

        $schema = CreditSchema::findOrFail($id);

        return response()->json($schema);
    }

    /**
     * Update the specified schema
     */
    public function update(Request $request, $id)
    {
        $this->checkAdmin();

        $schema = CreditSchema::findOrFail($id);

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'subcategory' => 'required|string|max:255',
            'activity_name' => 'required|string|max:500',
            'credit_points' => 'required|numeric|min:0',
            'satuan_hasil' => 'required|string|max:100',
            'batasan_penilaian' => 'nullable|string|max:255',
            'pelaksana' => 'nullable|string|max:100',
            'bukti_fisik' => 'nullable|string|max:255',
            'unsur_type' => 'required|in:utama,penunjang',
            'description' => 'nullable|string',
        ]);

        $schema->update($validated);

        return response()->json([
            'message' => 'Schema updated successfully',
            'schema' => $schema->fresh()
        ]);
    }

    /**
     * Remove the specified schema
     */
    public function destroy($id)
    {
        $this->checkAdmin();

        $schema = CreditSchema::findOrFail($id);

        // Check if schema is being used by activities
        if ($schema->activities()->exists()) {
            return response()->json([
                'message' => 'Cannot delete schema that is being used by activities'
            ], 400);
        }

        $schema->delete();

        return response()->json([
            'message' => 'Schema deleted successfully'
        ]);
    }
}
