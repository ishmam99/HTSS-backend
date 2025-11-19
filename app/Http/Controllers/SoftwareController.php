<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\IndustrySoftware;
use App\Models\IndustrySolution;
use App\Models\Software;
use App\Models\SoftwareSkill;
use App\Models\SoftwareSolution;
use App\Models\Solution;
use Illuminate\Http\Request;

class SoftwareController extends Controller
{
   public function index(Request $request)
    {
        // Get all software with related skill
        $query = Software::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        return response()->json([
            'success' => true,
            'data' => $lists,
            'total' => Software::count()
        ]);
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

    public function show(Request $request,Software $software)
    {

        if($request->has('solutions'))
        {
           $software->load('solutions');
        }
        if($request->has('industries'))
        {
           $software->load('industries');
        }
        if($request->has('trainings'))
        {
            $software->load('trainings.industry','trainings.solution');
        }
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
    public function industrySolution(Request $request)  {
        $request->validate([
            'industry_id' => 'required|exists:industries,id',
            'solution_id' => 'required|exists:solutions,id',
        ]);
        IndustrySolution::firstOrcreate([
            'industry_id' => $request->industry_id,
            'solution_id' => $request->solution_id,
        ]);
        return response()->json('Data added successfully');
    }
    public function industrySoftware(Request $request)  {
        $request->validate([
            'industry_id' => 'required|exists:industries,id',
            'software_id' => 'required|exists:softwares,id',
        ]);
        IndustrySoftware::firstOrcreate([
            'industry_id' => $request->industry_id,
            'software_id' => $request->software_id,
        ]);
        return response()->json('Data added successfully');
    }
    public function softwareSolution(Request $request)  {
        $request->validate([
            'solution_id' => 'required|exists:solutions,id',
            'software_id' => 'required|exists:softwares,id',
        ]);
        SoftwareSolution::firstOrcreate([
            'solution_id' => $request->solution_id,
            'software_id' => $request->software_id,
        ]);
        return response()->json('Data added successfully');
    }
    public function stats(){
        $softwares = Software::count();
        $solutions = Solution::count();
        $industries = Industry::count();
        $skills = SoftwareSkill::count();
        return response()->json([
            'softwares' =>$softwares,
            'solutions' => $solutions,
            'industries' => $industries,
            'skills' => $skills
        ]);
    }
}
