<?php

namespace App\Http\Controllers;

use App\Models\EndUserSoftware;
use App\Models\EndUserSolution;
use Illuminate\Http\Request;

class EndUserSoftwareController extends Controller
{
    public function getSoftwares()
    {
        $softewares = auth()->user()->endUser?->softwares->load('solutions','industries');
        return response()->json(['data'=>$softewares]);
    }
    public function getSolutions()
    {
        $solutions = auth()->user()->endUser?->solutions->load('softwares','industries');
        return response()->json(['data'=>$solutions]);
    }

      public function addSoftware(Request $request)
    {
        if(auth()->user()->role == 'end-user')
        {
            $request['end_user_id'] = auth()->user()->endUser->id;
        }
        $request->validate([
            'software_id' => 'required|exists:softwares,id',
            'end_user_id' =>  'required|exists:end_users,id',
            'level'     => 'required|string'
        ]);
        EndUserSoftware::firstOrcreate([
            'end_user_id' => $request->end_user_id,
            'software_id' => $request->software_id,
            'level' => $request->level
        ]);
        return response()->json('User Software added to list');
    }
      public function addSolution(Request $request)
    {
        if(auth()->user()->role == 'end-user')
        {
            $request['end_user_id'] = auth()->user()->endUser->id;
        }
        $request->validate([
            'solution_id' => 'required|exists:solutions,id',
            'end_user_id' =>  'required|exists:end_users,id',
        ]);
        EndUserSolution::firstOrcreate([
            'end_user_id' => $request->end_user_id,
            'solution_id' => $request->solution_id
        ]);
        return response()->json('User solution added to list');
    }
}
