<?php

namespace App\Http\Controllers\API\Internal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserInternalController extends Controller
{
    /**
     * Get user by various identifiers (id, email, nip)
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        $user = null;

        // Try to find by ID first
        if (is_numeric($identifier)) {
            $user = User::find($identifier);
        }

        // Try by email
        if (!$user && str_contains($identifier, '@')) {
            $user = User::where('email', $identifier)->first();
        }

        // Try by NIP
        if (!$user) {
            $user = User::where('nip', $identifier)->first();
        }

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'nip' => $user->nip,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'position' => $user->position,
                'unit_kerja' => $user->unit_kerja,
                'jenjang_jabatan' => $user->jenjang_jabatan,
                'golongan' => $user->golongan,
                'target_angka_kredit' => $user->target_angka_kredit,
                'current_credit_utama' => $user->current_credit_utama,
                'current_credit_penunjang' => $user->current_credit_penunjang,
                'current_credit_total' => $user->current_credit_total,
                'is_compliant' => $user->is_compliant,
            ],
        ]);
    }

    /**
     * Find user by email and NIP combination
     */
    public function findByCredentials(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'nip' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])
            ->where('nip', $validated['nip'])
            ->first();

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
                'found' => false,
            ], 404);
        }

        return response()->json([
            'found' => true,
            'user' => [
                'id' => $user->id,
                'nip' => $user->nip,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'position' => $user->position,
                'unit_kerja' => $user->unit_kerja,
                'jenjang_jabatan' => $user->jenjang_jabatan,
                'golongan' => $user->golongan,
            ],
        ]);
    }
}
