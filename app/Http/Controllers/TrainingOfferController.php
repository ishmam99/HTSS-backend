<?php

namespace App\Http\Controllers;

use App\Models\TrainingOffer;
use App\Http\Requests\TrainingOfferRequest;
use App\Http\Resources\TrainingOfferResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class TrainingOfferController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingOffer::advancedQuery($request);

        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }

          return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => TrainingOffer::count()
        ]);
    }


    public function store(TrainingOfferRequest $request)
    {
        $data = $request->validated();
        $data['start_date'] = $data['start_date']  ? Carbon::parse($data['start_date']) : null;
        $data['end_date'] = $data['end_date'] ? Carbon::parse($data['end_date']) : null;
        $trainingOffer = TrainingOffer::create($data);
        return response()->json([
            'status' => true,
            'message' => 'TrainingOffer created successfully',
        ], 201);
    }

    public function show(TrainingOffer $trainingOffer)
    {
        return new TrainingOfferResource($trainingOffer);
    }

    public function update(TrainingOfferRequest $request, TrainingOffer $trainingOffer)
    {
        $data = $request->validated();
        $trainingOffer->update($data);
        return response()->json([
            'status' => true,
            'message' => 'TrainingOffer updated successfully',
        ], 200);
    }

    public function destroy(TrainingOffer $trainingOffer)
    {
        $trainingOffer->delete();
        return response()->json(['status' => true,'message' => 'TrainingOffer deleted successfully'],200);
    }
}
