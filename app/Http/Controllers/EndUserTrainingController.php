<?php

namespace App\Http\Controllers;

use App\Http\Resources\EndUserTrainingResource;
use App\Models\EndUserTraining;
use Illuminate\Http\Request;

class EndUserTrainingController extends Controller
{
    //
    public function store(Request $request)
    {
        if(auth()->user()->role == 'end-user')
        {
            $request['end_user_id'] = auth()->user()->endUser->id;
        }

        $data = $request->validate([
            'end_user_id' => 'required|exists:end_users,id',
            'training_offer_id' => 'required|exists:end_users,id',
        ]);
        $req =  EndUserTraining::create($data);
        return response()->json(['message'=>'Enrolled Successfull','data'=>$req]);
    }

    public function index(Request $request)
    {
       $query = EndUserTraining::with(['user','offer'])
            ->when($request->end_user_id, fn($q) => $q->where('end_user_id', $request->end_user_id))
            ->when($request->training_offer_id, fn($q) => $q->where('training_offer_id', $request->training_offer_id))
            ->orderBy('id', 'desc');


           $lists = $request->has('per_page')
        ? $query->paginate($request->per_page)
        : $query->get();

        return EndUserTrainingResource::collection($lists);
    }
    public function show(EndUserTraining $endUserTraining)
    {

    }
    public function destroy(EndUserTraining $endUserTraining)
    {

    }
    public function update(EndUserTraining $endUserTraining,Request $req)
    {

    }

}
