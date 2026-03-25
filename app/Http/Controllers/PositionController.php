<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRequest;
use App\Http\Resources\PositionResource;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::with('department')
        ->orderBy('id', 'desc');

        if ($request->filled('per_page')) {
            $positions = $query->paginate((int) $request->per_page);
        } else {
            $positions = $query->get();
        }

    return PositionResource::collection($positions);
    }

    public function store(PositionRequest $request)
    {
        $position = Position::create($request->validated());

        return response()->json([
            'message' => 'Position created successfully',
            'data' => new PositionResource($position),
        ], 201);
    }

    public function show(Position $position)
    {
        return response()->json([
            'data' => new PositionResource($position->load('department')),
        ]);
    }

    public function update(PositionRequest $request, Position $position)
    {
        $position->update($request->validated());

        return response()->json([
            'message' => 'Position updated successfully',
            'data' => new PositionResource($position->fresh()),
        ]);
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return response()->json([
            'message' => 'Position deleted successfully',
        ]);
    }
}
