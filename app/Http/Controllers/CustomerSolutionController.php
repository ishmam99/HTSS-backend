<?php

namespace App\Http\Controllers;

use App\Models\CustomerSolution;
use Illuminate\Http\Request;

class CustomerSolutionController extends Controller
{
    //
    public function store(Request $request)
    {
        if(auth()->user()->role == 'customer')
        {
            $request['customer_id'] = auth()->user()->customer->id;
        }
        $request->validate([
            'software_id' => 'required|exists:softwares,id',
            'customer_id' =>  'required|exists:customers,id',
        ]);
        CustomerSolution::create([
            'customer_id' => $request->customer_id,
            'solution_id' => $request->solution_id
        ]);
        return response()->json('Customer solution added to list');
    }
}
