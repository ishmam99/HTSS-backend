<?php

namespace App\Http\Controllers;

use App\Models\Solution;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
    public function index(Request $request)
    {
        // Get all software with related skill
        $query = Solution::advancedQuery($request);
        $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();

    return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => Solution::count()
        ]);

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'software_ids' => 'nullable|array',
            'software_ids.*' => 'exists:software,id',
        ]);

        $solution = Solution::create($validated);

        return response()->json($solution->load('users'), 201);
    }

    public function show(Request $request,Solution $solution)
    {
          if($request->has('softwares'))
        {
           $solution->load('softwares');
        }
        if($request->has('industries'))
        {
           $solution->load('industries');
        }
         if($request->has('trainings'))
        {
             $solution->load('trainings.software','trainings.industry');
        }
        return response()->json($solution->load('users'));
    }

    public function update(Request $request, Solution $solution)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'name' => 'sometimes|required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'software_ids' => 'nullable|array',
            'software_ids.*' => 'exists:software,id',
            'status' => 'sometimes|integer',
        ]);

        $solution->update($validated);

        return response()->json($solution->load('users'));
    }

    public function destroy(Solution $solution)
    {
        $solution->delete();

        return response()->json(['message' => 'Solution deleted successfully']);
    }
}
