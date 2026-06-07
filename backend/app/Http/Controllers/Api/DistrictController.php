<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = District::with('region')->withCount('schools');

        if ($request->has('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:districts,code',
        ]);

        $district = District::create($request->only(['region_id', 'name', 'code']));

        return response()->json(['data' => $district->load('region')], 201);
    }

    public function show(District $district): JsonResponse
    {
        return response()->json([
            'data' => $district->load(['region', 'schools']),
        ]);
    }

    public function update(Request $request, District $district): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:districts,code,' . $district->id,
            'region_id' => 'sometimes|exists:regions,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $district->update($request->only(['name', 'code', 'region_id', 'is_active']));

        return response()->json(['data' => $district->load('region')]);
    }

    public function destroy(District $district): JsonResponse
    {
        $district->delete();
        return response()->json(['message' => 'Ноҳия нест карда шуд.']);
    }
}
