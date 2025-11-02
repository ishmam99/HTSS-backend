<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserSoftwareSkillController extends Controller
{
     // Show all skills for a given user
    public function index($userId)
    {
        $user = User::with('softwareSkills')->findOrFail($userId);
        return response()->json($user->softwareSkills);
    }

    // Attach one or multiple software skills to a user
    public function store(Request $request, $userId)
    {
        // dd($request->all());
        $validated = $request->validate([
            'skills' => 'required|array',
            'skills.*.software_skill_id' => 'required|exists:software_skills,id',
            'skills.*.proficiency_level' => 'nullable|string|max:255',
            'skills.*.experience_years' => 'nullable|integer|min:0',
        ]);

        $user = User::findOrFail($userId);

        $syncData = [];
        foreach ($validated['skills'] as $skill) {
            $syncData[$skill['software_skill_id']] = [
                'proficiency_level' => $skill['proficiency_level'] ?? null,
                'experience_years' => $skill['experience_years'] ?? 0,
            ];
        }

        // attach or sync skills
        $user->softwareSkills()->syncWithoutDetaching($syncData);

        return response()->json([
            'message' => 'Skills added successfully',
            'skills' => $user->softwareSkills()->get()
        ]);
    }

    // Update a single skill record for a user
    public function update(Request $request, $userId, $softwareSkillId)
    {
        $validated = $request->validate([
            'proficiency_level' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
        ]);

        $user = User::findOrFail($userId);

        $user->softwareSkills()->updateExistingPivot($softwareSkillId, [
            'proficiency_level' => $validated['proficiency_level'] ?? null,
            'experience_years' => $validated['experience_years'] ?? 0,
        ]);

        return response()->json([
            'message' => 'User skill updated successfully',
            'skills' => $user->softwareSkills()->get()
        ]);
    }

    // Detach a skill from a user
    public function destroy($userId, $softwareSkillId)
    {
        $user = User::findOrFail($userId);
        $user->softwareSkills()->detach($softwareSkillId);

        return response()->json(['message' => 'Skill removed successfully']);
    }
}
