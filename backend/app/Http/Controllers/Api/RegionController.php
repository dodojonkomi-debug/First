<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index(): JsonResponse
    {
        $regions = Region::withCount('districts')->get();
        return response()->json(['data' => $regions]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:regions,code',
        ]);

        $region = Region::create($request->only(['name', 'code']));

        return response()->json(['data' => $region], 201);
    }

    public function show(Region $region): JsonResponse
    {
        return response()->json([
            'data' => $region->load('districts.schools'),
        ]);
    }

    public function update(Request $request, Region $region): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:regions,code,' . $region->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $region->update($request->only(['name', 'code', 'is_active']));

        return response()->json(['data' => $region]);
    }

    public function destroy(Region $region): JsonResponse
    {
        $region->delete();
        return response()->json(['message' => 'Вилоят нест карда шуд.']);
    }
}
