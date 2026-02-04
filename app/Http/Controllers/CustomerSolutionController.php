<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerSolutionResource;
use App\Http\Resources\CustomerSolutions;
use App\Models\CustomerSolution;
use Illuminate\Http\Request;

class CustomerSolutionController extends Controller
{
    //
  public function index(Request $request)
{
    $query = CustomerSolution::with(['customer.user', 'solution', 'solution.softwares'])
        ->when(auth()->user()->role === 'customer', function ($q) {
            $q->where('customer_id', auth()->user()->customer->id);
        })
        ->when($request->filled('customer_id'), function ($q) use ($request) {
            $q->where('customer_id', $request->customer_id);
        })
      ->when($request->filled('customer_ids'), function ($q) use ($request) {
                $ids = explode(',', $request->customer_ids);
                $q->whereIn('customer_id', $ids);
            })
        ->when($request->filled('solution_id'), function ($q) use ($request) {
            $q->where('solution_id', $request->solution_id);
        })
      ->when($request->filled('solution_ids'), function ($q) use ($request) {
                $ids = explode(',', $request->solution_ids);
                $q->whereIn('solution_id', $ids);
            })
        ->when($request->filled('softwares'), function ($q) {
            $q->with('solution.softwares');
        });

    $data = $query->get();

    return response()->json(CustomerSolutionResource::collection($data));
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
        CustomerSolution::firstOrcreate([
            'customer_id' => $request->customer_id,
            'solution_id' => $request->solution_id
        ]);
        return response()->json('Customer solution added to list');
    }
}
