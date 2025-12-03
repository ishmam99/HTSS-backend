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
        $query = TrainerRequestForm::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('current_company')) {
            $query->where('current_company', 'LIKE', '%' . $request->current_company . '%');
        }

        if ($request->has('current_position')) {
            $query->where('current_position', 'LIKE', '%' . $request->current_position . '%');
        }

        if ($request->has('experience_year')) {
            $query->where('experience_year', $request->experience_year);
        }
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

    public function update(Request $request, TrainerRequestForm $trainerRequestForm)
    {
        $data = $request->validate([
             'industry_id' => 'nullable|exists:industries,id',
            'solution_id' => 'nullable|exists:solutions,id',
            'software_id' => 'nullable|exists:softwares,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:trainer_request_forms,email',
            'phone' => 'nullable|string|unique:trainer_request_forms,phone',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'experience_year' => 'nullable|string'
        ]);


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
