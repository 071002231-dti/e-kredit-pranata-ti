<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Skp;
use App\Models\SkpItem;
use App\Models\CreditSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkpController extends Controller
{
    /**
     * Display a listing of the user's SKP
     */
    public function index(Request $request)
    {
        $skps = Skp::with(['items.creditSchema', 'approver'])
            ->where('user_id', $request->user()->id)
            ->orderBy('tahun', 'desc')
            ->paginate(10);

        return response()->json($skps);
    }

    /**
     * Store a newly created SKP
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2024|max:2100',
            'target_angka_kredit' => 'required|numeric|min:0',
            'tugas_tambahan' => 'nullable|string',
        ]);

        $user = $request->user();

        // Check if SKP for this year already exists
        $exists = Skp::where('user_id', $user->id)
            ->where('tahun', $validated['tahun'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'SKP untuk tahun ini sudah ada',
            ], 422);
        }

        $skp = Skp::create([
            'user_id' => $user->id,
            'tahun' => $validated['tahun'],
            'target_angka_kredit' => $validated['target_angka_kredit'],
            'tugas_tambahan' => $validated['tugas_tambahan'],
            'status' => 'draft',
        ]);

        return response()->json([
            'message' => 'SKP created successfully',
            'skp' => $skp->load('items'),
        ], 201);
    }

    /**
     * Display the specified SKP
     */
    public function show(Request $request, $id)
    {
        $skp = Skp::with(['items.creditSchema', 'approver'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        // Calculate totals
        $targetTotal = $skp->items->sum('target_points');
        $realisasiTotal = $skp->items->sum('realisasi_points');
        $progress = $targetTotal > 0 ? ($realisasiTotal / $targetTotal) * 100 : 0;

        return response()->json([
            'skp' => $skp,
            'summary' => [
                'target_total' => $targetTotal,
                'realisasi_total' => $realisasiTotal,
                'progress_percentage' => round($progress, 2),
            ],
        ]);
    }

    /**
     * Update the specified SKP (only if draft)
     */
    public function update(Request $request, $id)
    {
        $skp = Skp::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($skp->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot update SKP that is not in draft status',
            ], 403);
        }

        $validated = $request->validate([
            'target_angka_kredit' => 'sometimes|numeric|min:0',
            'tugas_tambahan' => 'nullable|string',
        ]);

        $skp->update($validated);

        return response()->json([
            'message' => 'SKP updated successfully',
            'skp' => $skp->load('items'),
        ]);
    }

    /**
     * Submit SKP for approval
     */
    public function submit(Request $request, $id)
    {
        $skp = Skp::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($skp->status !== 'draft') {
            return response()->json([
                'message' => 'SKP sudah disubmit',
            ], 422);
        }

        if ($skp->items->count() === 0) {
            return response()->json([
                'message' => 'SKP harus memiliki minimal 1 item',
            ], 422);
        }

        $skp->update(['status' => 'submitted']);

        return response()->json([
            'message' => 'SKP berhasil disubmit untuk persetujuan',
            'skp' => $skp,
        ]);
    }

    /**
     * Remove the specified SKP (only if draft)
     */
    public function destroy(Request $request, $id)
    {
        $skp = Skp::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($skp->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot delete SKP that is not in draft status',
            ], 403);
        }

        $skp->delete();

        return response()->json([
            'message' => 'SKP deleted successfully',
        ]);
    }

    /**
     * Add item to SKP
     */
    public function addItem(Request $request, $id)
    {
        $skp = Skp::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($skp->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot add item to SKP that is not in draft status',
            ], 403);
        }

        $validated = $request->validate([
            'schema_id' => 'required|exists:credit_schema,id',
            'target_quantity' => 'required|integer|min:1',
        ]);

        $schema = CreditSchema::findOrFail($validated['schema_id']);

        // Check if user is eligible for this schema
        if (!$schema->canBePerformedBy($request->user()->jenjang_jabatan)) {
            return response()->json([
                'message' => 'Anda tidak eligible untuk aktivitas ini',
                'error' => "Memerlukan jenjang: {$schema->pelaksana}",
            ], 403);
        }

        // Calculate target points
        $targetPoints = $schema->credit_points * $validated['target_quantity'];

        $item = SkpItem::create([
            'skp_id' => $skp->id,
            'schema_id' => $validated['schema_id'],
            'target_quantity' => $validated['target_quantity'],
            'target_points' => $targetPoints,
            'realisasi_quantity' => 0,
            'realisasi_points' => 0,
        ]);

        return response()->json([
            'message' => 'Item added to SKP successfully',
            'item' => $item->load('creditSchema'),
        ], 201);
    }

    /**
     * Update SKP item
     */
    public function updateItem(Request $request, $skpId, $itemId)
    {
        $skp = Skp::where('user_id', $request->user()->id)
            ->findOrFail($skpId);

        if ($skp->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot update item on SKP that is not in draft status',
            ], 403);
        }

        $item = SkpItem::where('skp_id', $skp->id)
            ->findOrFail($itemId);

        $validated = $request->validate([
            'target_quantity' => 'sometimes|integer|min:1',
            'realisasi_quantity' => 'sometimes|integer|min:0',
        ]);

        // Recalculate points if quantity changed
        if (isset($validated['target_quantity'])) {
            $validated['target_points'] = $item->creditSchema->credit_points * $validated['target_quantity'];
        }

        if (isset($validated['realisasi_quantity'])) {
            $validated['realisasi_points'] = $item->creditSchema->credit_points * $validated['realisasi_quantity'];
        }

        $item->update($validated);

        return response()->json([
            'message' => 'SKP item updated successfully',
            'item' => $item->load('creditSchema'),
        ]);
    }

    /**
     * Remove item from SKP
     */
    public function removeItem(Request $request, $skpId, $itemId)
    {
        $skp = Skp::where('user_id', $request->user()->id)
            ->findOrFail($skpId);

        if ($skp->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot remove item from SKP that is not in draft status',
            ], 403);
        }

        $item = SkpItem::where('skp_id', $skp->id)
            ->findOrFail($itemId);

        $item->delete();

        return response()->json([
            'message' => 'SKP item removed successfully',
        ]);
    }

    /**
     * Get SKP statistics by year
     */
    public function stats(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $user = $request->user();

        $skp = Skp::with('items.creditSchema')
            ->where('user_id', $user->id)
            ->where('tahun', $tahun)
            ->first();

        if (!$skp) {
            return response()->json([
                'message' => 'No SKP found for this year',
                'has_skp' => false,
            ]);
        }

        $targetTotal = $skp->items->sum('target_points');
        $realisasiTotal = $skp->items->sum('realisasi_points');
        $progress = $targetTotal > 0 ? ($realisasiTotal / $targetTotal) * 100 : 0;

        return response()->json([
            'has_skp' => true,
            'skp' => $skp,
            'stats' => [
                'target_total' => $targetTotal,
                'realisasi_total' => $realisasiTotal,
                'progress_percentage' => round($progress, 2),
                'items_count' => $skp->items->count(),
                'status' => $skp->status,
            ],
        ]);
    }
}
