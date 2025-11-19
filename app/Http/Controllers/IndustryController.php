<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use Illuminate\Http\Request;
use App\Http\Resources\IndustryResource;

class IndustryController extends Controller
{
     public function index(Request $request)
    {
        // Get all software with related skill
        $query = Industry::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => Industry::count()
        ]);
    }

    public function show(Request $request,$id)
    {
        $industry = Industry::findOrFail($id);
          if($request->has('softwares'))
        {
           $industry->load('softwares');
        }
        if($request->has('solutions'))
        {
           $industry->load('solutions');
        }
        if($request->has('customers'))
        {
           $industry->load('customers.user','customers.softwares','customers.solutions');
        }
         if($request->has('trainings'))
        {
           $industry->load('trainings.software','trainings.solution');
        }
        return new IndustryResource($industry);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:industries,name',
            'description' => 'nullable|string',
            'sector_code' => 'nullable|string',
        ]);

        $industry = Industry::create($validated);

        return new IndustryResource($industry);
    }

    public function update(Request $request, $id)
    {
        $industry = Industry::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'sector_code' => 'nullable|string',
            'status' => 'nullable|integer',
        ]);

        $industry->update($validated);

        return new IndustryResource($industry);
    }

    public function destroy($id)
    {
        $industry = Industry::findOrFail($id);
        $industry->delete();

        return response()->json(['message' => 'Industry deleted successfully']);
    }
}
