<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
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
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        $query = User::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by jenjang
        if ($request->has('jenjang_jabatan')) {
            $query->where('jenjang_jabatan', $request->jenjang_jabatan);
        }

        $users = $query->orderBy('name')->get();

        return response()->json($users);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'nip' => 'nullable|string|max:50',
            'role' => 'required|in:user,verifier,admin',
            'jenjang_jabatan' => 'nullable|string',
            'golongan' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Set target angka kredit based on jenjang
        $validated['target_angka_kredit'] = $this->getTargetKredit($validated['jenjang_jabatan'] ?? null);
        $validated['angka_kredit_minimal'] = $validated['target_angka_kredit'] * 0.8;

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $this->checkAdmin();

        $user = User::findOrFail($id);

        return response()->json($user);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $this->checkAdmin();

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'nip' => 'nullable|string|max:50',
            'role' => 'required|in:user,verifier,admin',
            'jenjang_jabatan' => 'nullable|string',
            'golongan' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Update target if jenjang changed
        if (isset($validated['jenjang_jabatan']) && $validated['jenjang_jabatan'] !== $user->jenjang_jabatan) {
            $validated['target_angka_kredit'] = $this->getTargetKredit($validated['jenjang_jabatan']);
            $validated['angka_kredit_minimal'] = $validated['target_angka_kredit'] * 0.8;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh()
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $this->checkAdmin();

        $user = User::findOrFail($id);

        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Cannot delete your own account'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Get target angka kredit based on jenjang jabatan
     */
    private function getTargetKredit(?string $jenjang): float
    {
        $targets = [
            'Pranata TI Pertama' => 100,
            'Pranata TI Muda' => 200,
            'Pranata TI Madya' => 400,
            'Pranata TI Utama' => 700,
        ];

        return $targets[$jenjang] ?? 100;
    }
}
