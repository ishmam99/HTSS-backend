<?php

namespace App\Http\Controllers;

use App\Models\CustomerSoftware;
use Illuminate\Http\Request;

class CustomerSoftwareController extends Controller
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
        CustomerSoftware::create([
            'customer_id' => $request->customer_id,
            'software_id' => $request->software_id
        ]);
        return response()->json('Customer Software added to list');
    }
}
