<?php

namespace App\Http\Controllers;

use App\Models\SoftwareSkill;
use Illuminate\Http\Request;

class SoftwareSkillController extends Controller
{
     public function index(Request $request)
    {
        // Get all software with related skill
        $query = SoftwareSkill::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => SoftwareSkill::count()
        ]);
    }

    public function store(Request $request)
    {
        $skill = SoftwareSkill::create($request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|integer',
        ]));

        return response()->json($skill, 201);
    }

    public function show(SoftwareSkill $softwareSkill)
    {
        return response()->json($softwareSkill);
    }

    public function update(Request $request, SoftwareSkill $softwareSkill)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|integer',
        ]);

        $softwareSkill->update($validated);

        return response()->json([
            'message' => 'Software skill updated successfully',
            'data' => $softwareSkill,
        ]);
    }

    public function destroy(SoftwareSkill $softwareSkill)
    {
        $softwareSkill->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
