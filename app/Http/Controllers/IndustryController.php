<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use Illuminate\Http\Request;
use App\Http\Resources\IndustryResource;

class IndustryController extends Controller
{
    public function index(Request $request)
    {
        $industries = Industry::paginate(10);
        return IndustryResource::collection($industries);
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
