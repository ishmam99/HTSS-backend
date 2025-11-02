<?php

namespace App\Http\Controllers;

use App\Models\Software;
use App\Models\SoftwareSkill;
use Illuminate\Http\Request;

class SoftwareController extends Controller
{
   public function index()
    {
        // Get all software with related skill
        if(request()->has('per_page')){
            return response()->json(
            Software::with('softwareSkill' , 'users')->paginate(request()->per_page)
             );
        }
        return response()->json(
            Software::with('softwareSkill' , 'users')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'software_skill_id' => 'nullable|exists:software_skills,id',
        ]);

        $software = Software::create($validated);
        return response()->json($software->load('softwareSkill', 'users'), 201);
    }

    public function show(Software $software)
    {
        return response()->json($software->load('softwareSkill', 'users'));
    }

    public function update(Request $request, Software $software)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'name' => 'sometimes|required|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'software_skill_id' => 'nullable|exists:software_skills,id',
            'status' => 'sometimes|integer',
        ]);

        $software->update($validated);
        return response()->json($software->load('softwareSkill', 'users'));
    }

    public function destroy(Software $software)
    {
        $software->delete();
        return response()->json(['message' => 'Software deleted successfully']);
    }
}
