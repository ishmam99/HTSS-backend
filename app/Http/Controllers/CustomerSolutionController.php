<?php

namespace App\Http\Controllers;

use App\Models\CustomerSolution;
use Illuminate\Http\Request;

class CustomerSolutionController extends Controller
{
    //
    public function index(Request $request)
    {
       $query =  CustomerSolution::query();
        if(auth()->user()->role == 'customer')
        {
           $data = $query->with('customer','solution')->where('customer_id',auth()->user()->customer->id)->get();
        }
        if($request->has('customer_id'))
        {
            $data = $query->with('customer','solution')->where('customer_id',$request->customer_id)->get();
        }
        if($request->has('solution_id'))
        {
            $data = $query->with('customer','solution')->where('solution_id',$request->customer_id)->get();
        }

        return response()->json($data);

    }
    public function store(Request $request)
    {
        if(auth()->user()->role == 'customer')
        {
            $request['customer_id'] = auth()->user()->customer->id;
        }
        $request->validate([
            'solution_id' => 'required|exists:solutions,id',
            'customer_id' =>  'required|exists:customers,id',
        ]);
        CustomerSolution::create([
            'customer_id' => $request->customer_id,
            'solution_id' => $request->solution_id
        ]);
        return response()->json('Customer solution added to list');
    }
}
