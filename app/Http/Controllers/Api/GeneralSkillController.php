<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralSkillResource;
use App\Models\GeneralSkill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeneralSkillController extends Controller
{

    public function getGeneralSkillByUser(Request $request)
    {

        $skills = GeneralSkill::where('user_id', auth()->id())
            ->get();

        return GeneralSkillResource::collection($skills);
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
            'competencies.*'    => 'string',
        ]);

        $skill = GeneralSkill::create([
            'user_id'           => auth()->id(),
            'name'              => $validated['name'],
            'icon'              => $validated['icon'] ?? null,
            'proficiency_level' => $validated['proficiency_level'] ?? null,
            'competencies'      => $validated['competencies'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Skill created successfully.',
            'data'    => $skill,
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
            'competencies.*'    => 'string',
        ]);

        $generalSkill->update([
            'name'              => $validated['name'],
            'icon'              => $validated['icon'] ?? null,
            'proficiency_level' => $validated['proficiency_level'] ?? null,
            'competencies'      => $validated['competencies'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Skill updated successfully.',
            'data'    => $generalSkill,
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
