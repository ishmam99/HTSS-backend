<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompetencyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 10), 100);

        $competencies = Competency::with('generalSkills')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Competencies fetched successfully.',
            'data'    => $competencies,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:competencies,name',
        ]);

        $competency = Competency::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Competency created successfully.',
            'data'    => $competency,
        ], 201);
    }

    public function show(Competency $competency): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Competency fetched successfully.',
            'data'    => $competency->load('generalSkills'),
        ], 200);
    }

    public function update(Request $request, Competency $competency): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:competencies,name,' . $competency->id,
        ]);

        $competency->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Competency updated successfully.',
            'data'    => $competency->load('generalSkills'),
        ], 200);
    }

    public function destroy(Competency $competency): JsonResponse
    {
        $competency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Competency deleted successfully.',
            'data'    => null,
        ], 200);
    }
}
