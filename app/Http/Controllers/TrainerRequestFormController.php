<?php

namespace App\Http\Controllers;

use App\Models\TrainerRequestForm;
use App\Http\Requests\TrainerRequestFormRequest;
use App\Http\Resources\TrainerRequestFormResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class TrainerRequestFormController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainerRequestForm::with('software', 'industry', 'solution')
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })->when($request->has('software_id'), function ($query) use ($request) {
                return $query->where('software_id', $request->software_id);
            })->when($request->has('industry_id'), function ($query) use ($request) {
                return $query->where('industry_id', $request->industry_id);
            })->when($request->has('solution_id'), function ($query) use ($request) {
                return $query->where('solution_id', $request->solution_id);
            });
        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }

        return TrainerRequestFormResource::collection($lists);
    }


    public function store(TrainerRequestFormRequest $request)
    {
        $data = $request->validated();

        $trainerRequestForm = TrainerRequestForm::create($data);


        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/trainerRequestForm', 'public');
            $trainerRequestForm->update(['image' => $path]);
        }


        return response()->json([
            'status' => true,
            'message' => 'TrainerRequestForm created successfully',
        ], 201);
    }

    public function show(TrainerRequestForm $trainerRequestForm)
    {
        return new TrainerRequestFormResource($trainerRequestForm);
    }

    public function update(TrainerRequestFormRequest $request, TrainerRequestForm $trainerRequestForm)
    {
        $data = $request->validated();


        if ($request->hasFile('image')) {

            if ($trainerRequestForm->image && Storage::disk('public')->exists($trainerRequestForm->image)) {
                Storage::disk('public')->delete($trainerRequestForm->image);
            }


            $path = $request->file('image')->store('uploads/trainerRequestForm', 'public');
            $data['image'] = $path;
        }


        $trainerRequestForm->update($data);

        return response()->json([
            'status' => true,
            'message' => 'TrainerRequestForm updated successfully',
        ], 200);
    }

    public function destroy(TrainerRequestForm $trainerRequestForm)
    {
        $trainerRequestForm->delete();
        return response()->json(['status' => true, 'message' => 'TrainerRequestForm deleted successfully'], 200);
    }

    public function statusUpdate(Request $request, $id)
    {
        $trainerRequestForm = TrainerRequestForm::findOrFail($id);
        $trainerRequestForm->update(['status' => $request->status]);
        return response()->json(['status' => true, 'message' => 'TrainerRequestForm status updated successfully'], 200);
    }
}
