<?php

namespace App\Http\Controllers;

use App\Models\TrainingEvent;
use App\Http\Requests\TrainingEventRequest;
use App\Http\Resources\TrainingEventResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class TrainingEventController extends Controller
{
    public function index(Request $request)
{
    $query = TrainingEvent::query()
        ->when($request->status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->orderBy('id', 'desc');

    if ($request->has('per_page')) {
        $lists = $query->paginate($request->per_page);
    } else {
        $lists = $query->get();
    }
    $lists->load('trainingCourse');
    return TrainingEventResource::collection($lists);
}



    public function store(TrainingEventRequest $request)
    {
        $data = $request->validated();

        $trainingEvent = TrainingEvent::create($data);
        return response()->json([
            'status' => true,
            'message' => 'TrainingEvent created successfully',
        ], 201);
    }

    public function show(TrainingEvent $trainingEvent)
    {
        return new TrainingEventResource($trainingEvent);
    }

    public function update(TrainingEventRequest $request, TrainingEvent $trainingEvent)
    {
        $data = $request->validated();
        $trainingEvent->update($data);

        return response()->json([
            'status' => true,
            'message' => 'TrainingEvent updated successfully',
        ], 200);
    }

    public function destroy(TrainingEvent $trainingEvent)
    {
        $trainingEvent->delete();
        return response()->json(['status' => true,'message' => 'TrainingEvent deleted successfully'],200);
    }
}
