<?php

namespace App\Http\Controllers;

use App\Models\SoftwareLevel;
use App\Http\Requests\SoftwareLevelRequest;
use App\Http\Resources\SoftwareLevelResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class SoftwareLevelController extends Controller
{
  public function index(Request $request)
{
    $query = SoftwareLevel::when($request->filled('status'), function ($query) use ($request) {
            return $query->where('status', $request->status);
        })->when($request->filled('trainer_id'), function ($query) use ($request) {
            return $query->where('trainer_id', $request->trainer_id);
        })->when($request->filled('software_id'), function ($query) use ($request) {
            return $query->where('software_id', $request->software_id);
        })->when($request->filled('solution_id'), function ($query) use ($request) {
            return $query->where('solution_id', $request->solution_id);
        })->when($request->filled('industry_id'), function ($query) use ($request) {
            return $query->where('industry_id', $request->industry_id);
        })->orderBy('id', 'desc');

        if(auth()->user()->role == 'trainer')
        {
             $query =  $query->where('trainer_id',auth()->id());
        }

        $lists = $request->has('per_page')
        ? $query->paginate($request->per_page)
        : $query->get();

    return SoftwareLevelResource::collection($lists);
}



    public function store(SoftwareLevelRequest $request)
    {
        $items = $request->validated()['items'];
        $trainerId = auth()->id();

        $data = collect($items)->map(function ($item) use ($trainerId) {
            return array_merge($item, [
                'trainer_id' => $trainerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        })->toArray();

        SoftwareLevel::insert($data);

        return response()->json([
            'status' => true,
            'message' => 'Software levels created successfully',
        ], 201);
    }

    public function show(SoftwareLevel $softwareLevel)
    {
        return new SoftwareLevelResource($softwareLevel);
    }

    public function update(Request $request, SoftwareLevel $softwareLevel)
    {
        if(auth()->user()->role == 'trainer')
       { $trainerId = auth()->id();
        $data = $request->all();
        $updateData = [
            'trainer_id' => $trainerId,
            'updated_at' => now(),
        ];

        if (isset($data['industry_id'])) {
            $updateData['industry_id'] = $data['industry_id'];
        }

        if (isset($data['solution_id'])) {
            $updateData['solution_id'] = $data['solution_id'];
        }

        if (isset($data['software_id'])) {
            $updateData['software_id'] = $data['software_id'];
        }

        if (isset($data['levels'])) {
            $updateData['levels'] = $data['levels'];
        }

        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }

        $softwareLevel->update($updateData);
    }
    else
             $softwareLevel->update(['status'=>$request->status]);
        return response()->json([
            'status' => true,
            'message' => 'Software level updated successfully',
        ], 200);
    }


    public function destroy(SoftwareLevel $softwareLevel)
    {
        $softwareLevel->delete();
        return response()->json(['status' => true, 'message' => 'SoftwareLevel deleted successfully'], 200);
    }
}
