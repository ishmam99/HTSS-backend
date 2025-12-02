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
        $query = SoftwareLevel::when($request->status, function ($query) use ($request) {
            return $query->where('status', $request->status);
        })->when($request->trainer_id, function ($query) use ($request) {
            return $query->where('trainer_id', $request->trainer_id);
        })->when($request->software_id, function ($query) use ($request) {
            return $query->where('software_id', $request->software_id);
        })->when($request->solution_id, function ($query) use ($request) {
            return $query->where('solution_id', $request->solution_id);
        })->when($request->industry_id, function ($query) use ($request) {
            return $query->where('industry_id', $request->industry_id);
        })->orderBy('id', 'desc');

        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }
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

    public function update(SoftwareLevelRequest $request, SoftwareLevel $softwareLevel)
    {
        $items = $request->validated()['items'];
        $trainerId = auth()->id();

        foreach ($items as $item) {
            if (isset($item['id'])) {
                SoftwareLevel::where('id', $item['id'])
                    ->where('trainer_id', $trainerId)
                    ->update([
                        'industry_id' => $item['industry_id'] ?? null,
                        'solution_id' => $item['solution_id'] ?? null,
                        'software_id' => $item['software_id'] ?? null,
                        'levels' => $item['levels'],
                        'status' => $item['status'] ?? 1,
                        'updated_at' => now(),
                    ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Software levels updated successfully',
        ], 200);
    }

    public function destroy(SoftwareLevel $softwareLevel)
    {
        $softwareLevel->delete();
        return response()->json(['status' => true, 'message' => 'SoftwareLevel deleted successfully'], 200);
    }
}
