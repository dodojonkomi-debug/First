<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Рӯйхати корбарон (фақат суперадмин)
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['region', 'district', 'school']);

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->has('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        return response()->json(['data' => $query->paginate(20)]);
    }

    // Илова кардани корбар (директор/маъмур)
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin_region,admin_district,admin_school',
            'region_id' => 'required_if:role,admin_region|nullable|exists:regions,id',
            'district_id' => 'required_if:role,admin_district|nullable|exists:districts,id',
            'school_id' => 'required_if:role,admin_school|nullable|exists:schools,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'region_id' => $request->region_id,
            'district_id' => $request->district_id,
            'school_id' => $request->school_id,
        ]);

        return response()->json(['data' => $user->load(['region', 'district', 'school'])], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => $user->load(['region', 'district', 'school']),
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'password' => 'sometimes|string|min:6',
            'is_active' => 'sometimes|boolean',
            'role' => 'sometimes|in:admin_region,admin_district,admin_school',
            'region_id' => 'sometimes|nullable|exists:regions,id',
            'district_id' => 'sometimes|nullable|exists:districts,id',
            'school_id' => 'sometimes|nullable|exists:schools,id',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'is_active', 'role', 'region_id', 'district_id', 'school_id']);

        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['data' => $user->load(['region', 'district', 'school'])]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->role === 'superadmin') {
            return response()->json(['message' => 'Суперадминро нест кардан мумкин нест.'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Корбар нест карда шуд.']);
    }
}
