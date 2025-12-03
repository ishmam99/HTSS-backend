<?php

namespace App\Http\Controllers;

use App\Models\TrainerSchedule;
use App\Http\Requests\TrainerScheduleRequest;
use App\Http\Resources\TrainerScheduleResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class TrainerScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainerSchedule::when($request->status, function ($query) use ($request) {
            return $query->where('status', $request->status);
        })->when($request->trainer_id, function ($query) use ($request) {
            return $query->where('trainer_id', $request->trainer_id);
        })->when($request->training_course_id, function ($query) use ($request) {
            return $query->where('training_course_id', $request->training_course_id);
        })->orderBy('id', 'desc');

        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }

        return TrainerScheduleResource::collection($lists);
    }


    public function store(TrainerScheduleRequest $request)
    {
        $data = $request->validated();
        $data['trainer_id'] = auth()->user()->id;
        $data['days'] = json_encode($request->days);
        $data['start_date'] = Carbon::parse($request->start_date);
        $data['end_date'] = Carbon::parse($request->end_date);

        $trainerSchedule = TrainerSchedule::create($data);

        return response()->json([
            'status' => true,
            'message' => 'TrainerSchedule created successfully',
        ], 201);
    }

    public function show(TrainerSchedule $trainerSchedule)
    {
        return new TrainerScheduleResource($trainerSchedule);
    }

    public function update(TrainerScheduleRequest $request, TrainerSchedule $trainerSchedule)
    {
        $data = $request->validated();
        $trainerSchedule->update($data);

        return response()->json([
            'status' => true,
            'message' => 'TrainerSchedule updated successfully',
        ], 200);
    }

    public function destroy(TrainerSchedule $trainerSchedule)
    {
        $trainerSchedule->delete();
        return response()->json(['status' => true, 'message' => 'TrainerSchedule deleted successfully'], 200);
    }

    public function statusUpdate(Request $request,$id)
    {
       $trainerSchedule = TrainerSchedule::find($id);
       $trainerSchedule->update([
            'status' => $request->status
       ]);

        return response()->json([
            'status' => true,
            'message' => 'Trainer Schedule status updated successfully',
        ], 200);
    }
}
