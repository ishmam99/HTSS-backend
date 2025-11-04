<?php

namespace App\Http\Controllers;

use App\Http\Resources\TrainingResource;
use App\Models\Customer;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
       $customer = Customer::where('user_id', auth()->id())->first();
       $query = Training::with('customer.user', 'software', 'solution', 'industry')->where('customer_id',$customer->id)->when('software_id', function ($query, $softwareId) {
                return $query->where('software_id', $softwareId);
            })->when('solution_id', function ($query, $solutionId) {
                return $query->where('solution_id', $solutionId);
            })->when('industry_id', function ($query, $industryId) {
                return $query->where('industry_id', $industryId);
            })->when('status', function ($query, $status) {
                return $query->where('status', $status);
            });
        if($request->has('per_page')) {
            $trainings = $query->paginate($request->per_page);
        } else {
            $trainings = $query->get();
        }
        return TrainingResource::collection($trainings);
    }

    public function store(Request $request)
    {
        $customer = Customer::where('user_id', auth()->id())->first();
        $training = Training::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'software_id' => $request->software_id,
            'solution_id' => $request->solution_id,
            'industry_id' => $request->industry_id,
            'customer_id' => $customer->id,
            'status' => $request->status ?? 0,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Training created successfully',
            'data' => $training
        ], 201);
    }

    public function show(Training $training)
    {
        return new TrainingResource($training->load('customer.user', 'software', 'solution', 'industry'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::where('user_id', auth()->id())->first();

        $training = Training::findOrFail($id);
        if ($training->customer_id !== $customer->id) {
            return response()->json(['message' => 'You cannot update.'], 403);
        }

        $training->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'software_id' => $request->software_id,
            'solution_id' => $request->solution_id,
            'industry_id' => $request->industry_id,
            'customer_id' => $customer->id,
            'status' => $request->status ?? 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Training updated successfully',
            'data' => $training
        ], 200);
    }


    public function destroy(Training $training)
    {
        $customer = Customer::where('user_id', auth()->id())->first();
        if ($training->customer_id !== $customer->id) {
            return response()->json(['message' => 'You cannot update.'], 403);
        }
        $training->delete();
        return response()->json(['message' => 'Training deleted successfully']);
    }
}
