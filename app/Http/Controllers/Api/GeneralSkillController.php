<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSkill;
use App\Models\Competency;
use Illuminate\Http\JsonResponse;

class GeneralSkillController extends Controller
{

     public function getGeneralSkillByUser(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10); // default 10
        $skills = GeneralSkill::with('competencies')
        ->where('user_id', auth()->id())
        ->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Skills fetched successfully.',
            'data'    => $skills,
        ], 200);
    }

    /**
     * Store a newly created skill.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 10), 100);

        $skills = GeneralSkill::with('competencies')->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Skills fetched successfully.',
            'data'    => $skills,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'icon'              => 'nullable|string|max:255',
            'proficiency_level' => 'nullable|in:Beginner,Intermediate,Advanced,Expert',
            'competencies'      => 'nullable|array',
            'competencies.*'    => 'string|exists:competencies,id',
        ]);

        $skill = GeneralSkill::create([
            'user_id' => auth()->id(),
            'name'              => $validated['name'],
            'icon'              => $validated['icon'] ?? null,
            'proficiency_level' => $validated['proficiency_level'] ?? null,
        ]);

        if (!empty($validated['competencies'])) {
            $skill->competencies()->sync($validated['competencies']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Skill created successfully.',
            'data'    => $skill->load('competencies'),
        ], 201);
    }

    public function show(GeneralSkill $generalSkill): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Skill fetched successfully.',
            'data'    => $generalSkill->load('competencies'),
        ], 200);
    }

    public function update(Request $request, GeneralSkill $generalSkill): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'icon'              => 'nullable|string|max:255',
            'proficiency_level' => 'nullable|in:Beginner,Intermediate,Advanced,Expert',
            'competencies'      => 'nullable|array',
            'competencies.*'    => 'string|exists:competencies,id',
        ]);

        $generalSkill->update([
            'name'              => $validated['name'],
            'icon'              => $validated['icon'] ?? null,
            'proficiency_level' => $validated['proficiency_level'] ?? null,
        ]);

        if (isset($validated['competencies'])) {
            $generalSkill->competencies()->sync($validated['competencies']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Skill updated successfully.',
            'data'    => $generalSkill->load('competencies'),
        ], 200);
    }

    public function destroy(GeneralSkill $generalSkill): JsonResponse
    {
        $generalSkill->delete();

        return response()->json([
            'success' => true,
            'message' => 'Skill deleted successfully.',
            'data'    => null,
        ], 200);
    }
}
