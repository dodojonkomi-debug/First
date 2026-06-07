<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = School::with('district.region');

        if ($request->has('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->has('region_id')) {
            $query->whereHas('district', function ($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }

        $schools = $query->withCount('applications')->get();

        return response()->json(['data' => $schools]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:schools,code',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'capacity_class_0' => 'required|integer|min:0',
            'capacity_class_1' => 'required|integer|min:0',
        ]);

        $school = School::create($request->only([
            'district_id', 'name', 'code', 'address', 'phone',
            'capacity_class_0', 'capacity_class_1'
        ]));

        return response()->json(['data' => $school->load('district.region')], 201);
    }

    public function show(School $school): JsonResponse
    {
        return response()->json([
            'data' => $school->load(['district.region', 'users']),
        ]);
    }

    public function update(Request $request, School $school): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:schools,code,' . $school->id,
            'district_id' => 'sometimes|exists:districts,id',
            'address' => 'sometimes|nullable|string|max:500',
            'phone' => 'sometimes|nullable|string|max:20',
            'capacity_class_0' => 'sometimes|integer|min:0',
            'capacity_class_1' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $school->update($request->only([
            'name', 'code', 'district_id', 'address', 'phone',
            'capacity_class_0', 'capacity_class_1', 'is_active'
        ]));

        return response()->json(['data' => $school->load('district.region')]);
    }

    public function destroy(School $school): JsonResponse
    {
        $school->delete();
        return response()->json(['message' => 'Мактаб нест карда шуд.']);
    }
}
